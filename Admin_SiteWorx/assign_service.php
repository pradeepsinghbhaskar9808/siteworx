<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_admin.php';
require_once 'lib_mailer.php';
require_role($pdo, ['admin', 'manager']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    [$current, $role] = sw_current_role($pdo);
    [$userWhere, $userParams] = sw_manager_user_filter_sql($role, (int)$current['id'], 'u');

    $userStmt = $pdo->prepare("SELECT u.id, u.name, u.username, u.email FROM login u WHERE {$userWhere} ORDER BY u.name, u.username");
    $userStmt->execute($userParams);
    $users = $userStmt->fetchAll();

    $plans = $pdo->query("SELECT id,name,category,price_monthly,currency FROM hosting_plans WHERE status='active' ORDER BY category, price_monthly")->fetchAll();
    $services = $pdo->query("SELECT id,name,type,price FROM service_catalog WHERE status='active' ORDER BY type,name")->fetchAll();
    $servers = $pdo->query("SELECT id,hostname,region,ip_address FROM servers WHERE status='active' ORDER BY region,hostname")->fetchAll();
    ?>
    <?php include '_header.php'; ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">Assign Service</h1>
            <div class="text-muted">Select a user, service, and billing period to create the service and invoice.</div>
        </div>
        <a class="btn btn-outline-secondary" href="index">Back to Dashboard</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post" action="assign_service">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Select user</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo (int)$user['id']; ?>">
                                    <?php echo htmlspecialchars(($user['name'] ?: $user['username']) . ' - ' . $user['email']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Invoice Date</label>
                        <input
                            type="date"
                            name="invoice_date"
                            value="<?php echo date('Y-m-d'); ?>"
                            class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Plan / Service</label>
                        <select name="item_id" class="form-select" required>
                            <option value="">Select item</option>
                            <?php foreach ($plans as $p): ?>
                                <option value="plan:<?php echo (int)$p['id']; ?>">
                                    <?php echo htmlspecialchars($p['name'] . ' (' . $p['category'] . ') - '); ?><?php echo sw_format_money($p['price_monthly'], $p['currency'] ?? 'INR'); ?>
                                </option>
                            <?php endforeach; ?>
                            <?php foreach ($services as $s): ?>
                                <option value="service:<?php echo (int)$s['id']; ?>">
                                    <?php echo htmlspecialchars($s['name'] . ' (' . $s['type'] . ') - '); ?><?php echo sw_format_money($s['price']); ?>
                                </option>
                            <?php endforeach; ?>
                            <?php foreach ($servers as $srv): ?>
                                <option value="server:<?php echo (int)$srv['id']; ?>">
                                    Server: <?php echo htmlspecialchars($srv['hostname'] . ' ' . ($srv['ip_address'] ? '(' . $srv['ip_address'] . ')' : '') . ' ' . $srv['region']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Period (Months)</label>
                        <input type="number" min="1" name="period_months" value="1" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" min="1" name="quantity" value="1" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Manual Amount</label>
                        <input type="number"
                               name="manual_amount"
                               class="form-control"
                               step="0.01"
                               min="0"
                               placeholder="Auto">
                        <small class="text-muted">Leave blank to use default price.</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Domain Details</label>
                        <textarea id="domain_editor" name="domain" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-primary">Assign Service</button>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '_footer.php'; ?>
    <?php
    exit;
}

// ===============================
// POST Processing
// ===============================

$user_id      = (int)($_POST['user_id'] ?? 0);
$item         = trim($_POST['item_id'] ?? '');
$months       = max(1, (int)($_POST['period_months'] ?? 1));
$quantity     = max(1, (int)($_POST['quantity'] ?? 1));
$manualAmount = trim($_POST['manual_amount'] ?? '');
$invoiceDate  = !empty($_POST['invoice_date']) ? $_POST['invoice_date'] : date('Y-m-d');
$domain       = trim($_POST['domain'] ?? '');

if (!$user_id || !$item || !sw_can_manage_user($pdo, $user_id)) {
    die('Missing parameters or unauthorized access.');
}

$type = 'plan';
$itemId = 0;

if (strpos($item, ':') !== false) {
    list($type, $rawId) = explode(':', $item, 2);
    $itemId = (int)$rawId;
} else {
    $itemId = (int)$item;
}

$plan = null;
$service = null;
$server = null;
$currency = 'INR';

switch ($type) {
    case 'service':
        $stmt = $pdo->prepare("SELECT * FROM service_catalog WHERE id=:id LIMIT 1");
        $stmt->execute([':id'=>$itemId]);
        $service = $stmt->fetch();

        if(!$service){
            die("Service not found");
        }
        $unit = (float)$service['price'];
        break;

    case 'server':
        $stmt = $pdo->prepare("SELECT * FROM servers WHERE id=:id LIMIT 1");
        $stmt->execute([':id'=>$itemId]);
        $server = $stmt->fetch();

        if(!$server){
            die("Server not found");
        }
        $unit = 0;
        break;

    default:
        $stmt = $pdo->prepare("SELECT * FROM hosting_plans WHERE id=:id LIMIT 1");
        $stmt->execute([':id'=>$itemId]);
        $plan = $stmt->fetch();

        if(!$plan){
            die("Hosting Plan not found");
        }
        $unit = (float)$plan['price_monthly'];
        $currency = $plan['currency'] ?: 'INR';
        break;
}

if($manualAmount !== '' && is_numeric($manualAmount)){
    $unit = (float)$manualAmount;
}

$total = $unit * $quantity * $months;

// Calculate expiration date safely in PHP to resolve the HY093 syntax error
$expiresDate = date('Y-m-d', strtotime("+" . $months . " months", strtotime($invoiceDate)));

try {
    $pdo->beginTransaction();

    // =============================
    // ORDER
    // =============================
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, currency, status, created_at)
        VALUES (:u, :total, :currency, 'completed', :created)
    ");
    $stmt->execute([
        ':u'        => $user_id,
        ':total'    => $total,
        ':currency' => $currency,
        ':created'  => $invoiceDate
    ]);
    $order_id = $pdo->lastInsertId();

    // =============================
    // META
    // =============================
    $meta = [];
    if($server){
        $meta['server_id'] = $server['id'];
        $meta['hostname'] = $server['hostname'];
    }
    $meta['domain'] = $domain;
    $metaJson = json_encode($meta);

    // =============================
    // ORDER ITEM
    // =============================
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, plan_id, service_id, quantity, unit_price, period_months, meta)
        VALUES (:o, :p, :s, :q, :price, :period, :meta)
    ");
    $stmt->execute([
        ':o'      => $order_id,
        ':p'      => $plan['id'] ?? null,
        ':s'      => $service['id'] ?? null,
        ':q'      => $quantity,
        ':price'  => $unit,
        ':period' => $months,
        ':meta'   => $metaJson
    ]);
    $orderItemId = $pdo->lastInsertId();

    // =============================
    // SUBSCRIPTION
    // =============================
    $stmt = $pdo->prepare("
        INSERT INTO subscriptions (user_id, plan_id, service_id, server_id, order_item_id, started_at, expires_at, status)
        VALUES (:u, :p, :s, :srv, :oi, :start, :expires, 'active')
    ");
    $stmt->execute([
        ':u'       => $user_id,
        ':p'       => $plan['id'] ?? null,
        ':s'       => $service['id'] ?? null,
        ':srv'     => $server['id'] ?? null,
        ':oi'      => $orderItemId,
        ':start'   => $invoiceDate,
        ':expires' => $expiresDate // Clean parameters passed straight through PHP
    ]);

    // =============================
    // INVOICE
    // =============================
    $stmt = $pdo->prepare("
        INSERT INTO invoices (order_id, amount, status, issued_at, invoice_date)
        VALUES (:o, :amount, 'unpaid', :issued, :invoiceDate)
    ");
    $stmt->execute([
        ':o'           => $order_id,
        ':amount'      => $total,
        ':issued'      => $invoiceDate,
        ':invoiceDate' => $invoiceDate
    ]);
    $invoice_id = $pdo->lastInsertId();

    // =============================
    // AUDIT LOG
    // =============================
    $audit = [
        'invoice_id' => $invoice_id,
        'user_id'    => $user_id,
        'amount'     => $total,
        'qty'        => $quantity,
        'months'     => $months,
        'domain'     => $domain
    ];
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs (operation, user_id, data)
        VALUES ('Invoice Created', :uid, :data)
    ");
    $stmt->execute([
        ':uid'  => current_user($pdo)['id'],
        ':data' => json_encode($audit)
    ]);

    $pdo->commit();

} catch(Exception $e) {
    $pdo->rollBack();
    die($e->getMessage());
}

// =============================
// SEND EMAIL
// =============================
$userStmt = $pdo->prepare("SELECT email, name FROM login WHERE id=:id LIMIT 1");
$userStmt->execute([':id' => $user_id]);
$user = $userStmt->fetch();

if($user && !empty($user['email'])) {
    $subject = "Invoice #" . $invoice_id;
    $link = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/generate_invoice?invoice_id=" . $invoice_id;
    $amountText = $currency . " " . number_format($total, 2);
    $clientName = $user['name'] ?: $user['email'];

    $html = sw_invoice_email_html($clientName, $invoice_id, $amountText, 'unpaid', $link);
    $text = "Hello {$clientName}\n\nInvoice Generated\n\nAmount : {$amountText}\n\nView Invoice\n\n{$link}\n\nThanks";

    sw_mail_send($user['email'], $clientName, $subject, $html, $text);
}

header("Location: generate_invoice?invoice_id=" . $invoice_id);
exit;