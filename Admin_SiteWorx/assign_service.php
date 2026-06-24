<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_admin.php';
require_role($pdo, ['admin','manager']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo 'Method Not Allowed'; exit;
}

$user_id = (int)($_POST['user_id'] ?? 0);
$item = (string)($_POST['item_id'] ?? '');
$months = max(1, (int)($_POST['period_months'] ?? 1));

if (!$user_id || !$item || !sw_can_manage_user($pdo, $user_id)) {
    die('Missing params');
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
}
$total = $unit * $months;

try {
    $pdo->beginTransaction();
    // create order
    $stmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, currency, status, created_at) VALUES (:u,:total,:cur,:st,NOW())');
    $stmt->execute([':u'=>$user_id,':total'=>$total,':cur'=>'USD',':st'=>'completed']);
    $order_id = $pdo->lastInsertId();

    // create order item
    $stmt = $pdo->prepare('INSERT INTO order_items (order_id, plan_id, service_id, quantity, unit_price, period_months, meta) VALUES (:o,:p,:s,1,:price,:pm,:meta)');
    $stmt->execute([
        ':o'=>$order_id,
        ':p'=>$plan['id'] ?? null,
        ':s'=>$service['id'] ?? null,
        ':price'=>$unit,
        ':pm'=>$months,
        ':meta'=>$server ? json_encode(['server_id' => $server['id'], 'hostname' => $server['hostname']]) : null
    ]);
    $item_id = $pdo->lastInsertId();

    // create subscription
    $stmt = $pdo->prepare('INSERT INTO subscriptions (user_id, plan_id, service_id, server_id, order_item_id, started_at, expires_at, status) VALUES (:u,:p,:s,:srv,:oi,NOW(), DATE_ADD(NOW(), INTERVAL ' . $months . ' MONTH), :st)');
    $stmt->execute([
        ':u'=>$user_id,
        ':p'=>$plan['id'] ?? null,
        ':s'=>$service['id'] ?? null,
        ':srv'=>$server['id'] ?? null,
        ':oi'=>$item_id,
        ':st'=>'active'
    ]);

    // create invoice
    $stmt = $pdo->prepare('INSERT INTO invoices (order_id, amount, status, issued_at) VALUES (:o,:amt,:st,NOW())');
    $stmt->execute([':o'=>$order_id,':amt'=>$total,':st'=>'unpaid']);
    $invoice_id = $pdo->lastInsertId();

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die('Error assigning service: '.$e->getMessage());
}

// send basic invoice email
$u = $pdo->prepare('SELECT email,name FROM login WHERE id = :id LIMIT 1');
$u->execute([':id'=>$user_id]); $user = $u->fetch();
if ($user && !empty($user['email'])) {
    $to = $user['email'];
    $subject = 'Your SiteWorx invoice #' . $invoice_id;
    $link = (isset($_SERVER['HTTP_HOST'])? 'https://'.$_SERVER['HTTP_HOST'] : '') . dirname($_SERVER['REQUEST_URI']) . '/generate_invoice.php?invoice_id=' . $invoice_id;
    $message = "Hello " . ($user['name'] ?? $user['email']) . ",\n\nAn invoice has been generated for your service. View: " . $link;
    $headers = 'From: noreply@' . ($_SERVER['HTTP_HOST'] ?? 'example.com') . "\r\n";
    @mail($to, $subject, $message, $headers);
}

header('Location: generate_invoice.php?invoice_id=' . $invoice_id);
exit;
