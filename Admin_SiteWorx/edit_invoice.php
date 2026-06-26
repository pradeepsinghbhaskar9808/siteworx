<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_admin.php';
require_role($pdo, ['admin', 'manager']);

// Force PDO to throw exceptions if there's a hidden SQL problem
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch from GET or POST cleanly
$invoice_id = (int)($_GET['invoice_id'] ?? $_POST['invoice_id'] ?? 0);

if (!$invoice_id) {
    die("Invalid Invoice ID.");
}

$stmt = $pdo->prepare("
    SELECT 
        i.id AS invoice_id, i.amount AS invoice_amount, i.invoice_date, i.status AS invoice_status, i.transaction_id,
        o.id AS order_id, o.user_id, o.currency,
        oi.id AS order_item_id, oi.plan_id, oi.service_id, oi.quantity, oi.unit_price, oi.period_months, oi.meta,
        u.name AS user_name, u.email AS user_email, u.username AS user_username
    FROM invoices i
    JOIN orders o ON i.order_id = o.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN login u ON o.user_id = u.id
    WHERE i.id = :invoice_id
    LIMIT 1
");
$stmt->execute([':invoice_id' => $invoice_id]);
$invoiceData = $stmt->fetch();

if (!$invoiceData) {
    die("Invoice not found.");
}

if (!sw_can_manage_user($pdo, $invoiceData['user_id'])) {
    die("Unauthorized access to this user's data.");
}

$meta = [];
if (!empty($invoiceData['meta'])) {
    $meta = json_decode($invoiceData['meta'], true);
}
$currentDomain = $meta['domain'] ?? '';

// ==========================================
// 1. POST Request handling (Saving Changes)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity      = max(1, (int)($_POST['quantity'] ?? 1));
    $months        = max(1, (int)($_POST['period_months'] ?? 1));
    $unitPrice     = max(0.0, (float)($_POST['unit_price'] ?? 0));
    $invoiceDate   = !empty($_POST['invoice_date']) ? $_POST['invoice_date'] : date('Y-m-d');
    $domain        = trim($_POST['domain'] ?? '');
    $status        = trim($_POST['status'] ?? 'unpaid');
    $transactionId = trim($_POST['transaction_id'] ?? '');

    $totalAmount  = $unitPrice * $quantity * $months;
    $expiresDate  = date('Y-m-d', strtotime("+" . $months . " months", strtotime($invoiceDate)));

    try {
        $pdo->beginTransaction();

        // FIX: Bound invoice_date and issued_at to distinct parameters to prevent PDO exception
        $stmt = $pdo->prepare("
            UPDATE invoices 
            SET amount = :amount, invoice_date = :invoice_date, issued_at = :issued_at, status = :status, transaction_id = :tx_id
            WHERE id = :invoice_id
        ");
        $stmt->execute([
            ':amount'       => $totalAmount,
            ':invoice_date' => $invoiceDate,
            ':issued_at'    => $invoiceDate,
            ':status'       => $status,
            ':tx_id'        => !empty($transactionId) ? $transactionId : null,
            ':invoice_id'   => $invoice_id
        ]);

        // B. Update Orders table
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET total_amount = :amount, created_at = :idate
            WHERE id = :order_id
        ");
        $stmt->execute([
            ':amount'   => $totalAmount,
            ':idate'    => $invoiceDate,
            ':order_id' => $invoiceData['order_id']
        ]);

        // C. Update Order Items table
        $meta['domain'] = $domain;
        $updatedMetaJson = json_encode($meta);

        $stmt = $pdo->prepare("
            UPDATE order_items 
            SET quantity = :qty, unit_price = :price, period_months = :months, meta = :meta
            WHERE id = :order_item_id
        ");
        $stmt->execute([
            ':qty'           => $quantity,
            ':price'         => $unitPrice,
            ':months'        => $months,
            ':meta'          => $updatedMetaJson,
            ':order_item_id' => $invoiceData['order_item_id']
        ]);

        // D. Update Subscriptions table
        $stmt = $pdo->prepare("
            UPDATE subscriptions 
            SET started_at = :start, expires_at = :expires
            WHERE order_item_id = :order_item_id
        ");
        $stmt->execute([
            ':start'         => $invoiceDate,
            ':expires'       => $expiresDate,
            ':order_item_id' => $invoiceData['order_item_id']
        ]);

        // E. Audit Trail Logging
        $audit = [
            'invoice_id'     => $invoice_id,
            'user_id'        => $invoiceData['user_id'],
            'amount'         => $totalAmount,
            'qty'            => $quantity,
            'months'         => $months,
            'domain'         => $domain,
            'status'         => $status,
            'transaction_id' => $transactionId
        ];
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (operation, user_id, data)
            VALUES ('Invoice Updated Manually', :uid, :data)
        ");
        $stmt->execute([
            ':uid'  => current_user($pdo)['id'],
            ':data' => json_encode($audit)
        ]);

        $pdo->commit();
        
        header("Location: generate_invoice.php?invoice_id=" . $invoice_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error updating database records: " . $e->getMessage());
    }
}

// ==========================================
// 2. GET Request handling (Displaying Form)
// ==========================================
include '_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 mb-1">Edit Invoice #<?php echo $invoice_id; ?></h1>
        <div class="text-muted">Modifying data for client: <strong><?php echo htmlspecialchars(($invoiceData['user_name'] ?: $invoiceData['user_username']) . ' (' . $invoiceData['user_email'] . ')'); ?></strong></div>
    </div>
    <a class="btn btn-outline-secondary" href="index.php">Back to Dashboard</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="">
            <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
            
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Invoice Date</label>
                    <input 
                        type="date" 
                        name="invoice_date" 
                        value="<?php echo htmlspecialchars($invoiceData['invoice_date']); ?>" 
                        class="form-control" 
                        required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Payment Status</label>
                    <select name="status" class="form-select" required>
                        <option value="unpaid" <?php echo $invoiceData['invoice_status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                        <option value="paid" <?php echo $invoiceData['invoice_status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="cancelled" <?php echo $invoiceData['invoice_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="refunded" <?php echo $invoiceData['invoice_status'] === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Transaction ID / Reference</label>
                    <input 
                        type="text" 
                        name="transaction_id" 
                        value="<?php echo htmlspecialchars($invoiceData['transaction_id'] ?? ''); ?>" 
                        placeholder="e.g. TXN1002345"
                        class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Unit Price (<?php echo htmlspecialchars($invoiceData['currency']); ?>)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        min="0" 
                        name="unit_price" 
                        value="<?php echo htmlspecialchars($invoiceData['unit_price']); ?>" 
                        class="form-control" 
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Period (Months)</label>
                    <input 
                        type="number" 
                        min="1" 
                        name="period_months" 
                        value="<?php echo htmlspecialchars($invoiceData['period_months']); ?>" 
                        class="form-control" 
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Quantity</label>
                    <input 
                        type="number" 
                        min="1" 
                        name="quantity" 
                        value="<?php echo htmlspecialchars($invoiceData['quantity']); ?>" 
                        class="form-control" 
                        required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Domain Details</label>
                    <textarea 
                        id="domain_editor" 
                        name="domain" 
                        class="form-control" 
                        rows="5"><?php echo htmlspecialchars($currentDomain); ?></textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="generate_invoice.php?invoice_id=<?php echo $invoice_id; ?>" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php 
include '_footer.php'; 
?>