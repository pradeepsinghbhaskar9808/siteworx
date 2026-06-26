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

// POST Processing
$user_id  = (int)($_POST['user_id'] ?? 0);
$item     = (string)($_POST['item_id'] ?? '');
$months   = max(1, (int)($_POST['period_months'] ?? 1));
$quantity = max(1, (int)($_POST['quantity'] ?? 1));

if (!$user_id || !$item || !sw_can_manage_user($pdo, $user_id)) {
    die('Missing params or unauthorized access');
}

$type = 'plan';
$itemId = 0;
if (strpos($item, ':') !== false) {
    [$type, $rawId] = explode(':', $item, 2);
    $itemId = (int)$rawId;
} else {
    $itemId = (int)$item;
}

$plan = $service = $server = null;
$currency = 'INR';

if ($type === 'service') {
    $stmt = $pdo->prepare('SELECT * FROM service_catalog WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $itemId]);
    $service = $stmt->fetch();
    if (!$service) die('Service not found');
    $unit = (float)($service['price'] ?? 0.00);
} elseif ($type === 'server') {
    $stmt = $pdo->prepare('SELECT * FROM servers WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $itemId]);
    $server = $stmt->fetch();
    if (!$server) die('Server not found');
    $unit = 0.00;
} else {
    $stmt = $pdo->prepare('SELECT * FROM hosting_plans WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $itemId]);
    $plan = $stmt->fetch();
    if (!$plan) die('Plan not found');
    $unit = (float)($plan['price_monthly'] ?? 0.00);
    $currency = $plan['currency'] ?? 'INR';
}

// Correct calculations accounting for quantity and billing months
$total = $unit * $months * $quantity;

try {
    $pdo->beginTransaction();

    // 1. Create order
    $stmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, currency, status, created_at) VALUES (:u, :total, :cur, :st, NOW())');
    $stmt->execute([':u' => $user_id, ':total' => $total, ':cur' => $currency, ':st' => 'completed']);
    $order_id = $pdo->lastInsertId();

    // 2. Create order item
    $meta = $server ? json_encode(['server_id' => $server['id'], 'hostname' => $server['hostname']]) : null;
    $stmt = $pdo->prepare('INSERT INTO order_items (order_id, plan_id, service_id, quantity, unit_price, period_months, meta) VALUES (:o, :p, :s, :qty, :price, :pm, :meta)');
    $stmt->execute([
        ':o'     => $order_id,
        ':p'     => $plan['id'] ?? null,
        ':s'     => $service['id'] ?? null,
        ':qty'   => $quantity,
        ':price' => $unit,
        ':pm'    => $months,
        ':meta'  => $meta
    ]);
    $item_id = $pdo->lastInsertId();

    // 3. Create subscription
    $stmt = $pdo->prepare('INSERT INTO subscriptions (user_id, plan_id, service_id, server_id, order_item_id, started_at, expires_at, status) VALUES (:u, :p, :s, :srv, :oi, NOW(), DATE_ADD(NOW(), INTERVAL :months MONTH), :st)');
    $stmt->execute([
        ':u'      => $user_id,
        ':p'      => $plan['id'] ?? null,
        ':s'      => $service['id'] ?? null,
        ':srv'    => $server['id'] ?? null,
        ':oi'     => $item_id,
        ':months' => $months,
        ':st'     => 'active'
    ]);

    // 4. Create invoice
    $stmt = $pdo->prepare('INSERT INTO invoices (order_id, amount, status, issued_at) VALUES (:o, :amt, :st, NOW())');
    $stmt->execute([':o' => $order_id, ':amt' => $total, ':st' => 'unpaid']);
    $invoice_id = $pdo->lastInsertId();

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die('Error assigning service: ' . $e->getMessage());
}

// Send invoice email
$u = $pdo->prepare('SELECT email, name FROM login WHERE id = :id LIMIT 1');
$u->execute([':id' => $user_id]);
$user = $u->fetch();

if ($user && !empty($user['email'])) {
    $subject    = 'Your SiteWorx invoice #' . $invoice_id;
    $link       = (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] : '') . dirname($_SERVER['REQUEST_URI']) . '/generate_invoice?invoice_id=' . $invoice_id;
    $clientName = $user['name'] ?: $user['email'];
    $amountText = $currency . ' ' . number_format($total, 2);
    
    $html = sw_invoice_email_html($clientName, $invoice_id, $amountText, 'unpaid', $link);
    $text = "Hello {$clientName},\n\n"
        . "An invoice has been generated for your service.\n"
        . "Amount Due: {$amountText}\n"
        . "View Invoice: {$link}\n\n"
        . "Thank you,\nSiteWorx";
        
    sw_mail_send($user['email'], $clientName, $subject, $html, $text);
}

header('Location: generate_invoice?invoice_id=' . $invoice_id);
exit;