<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_role($pdo, ['admin','manager']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo 'Method Not Allowed'; exit;
}

$user_id = (int)($_POST['user_id'] ?? 0);
$plan_id = (int)($_POST['plan_id'] ?? 0);
$months = max(1, (int)($_POST['period_months'] ?? 1));

if (!$user_id || !$plan_id) {
    die('Missing params');
}

// fetch plan
$stmt = $pdo->prepare('SELECT * FROM hosting_plans WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $plan_id]);
$plan = $stmt->fetch();
if (!$plan) die('Plan not found');

$unit = (float)($plan['price_monthly'] ?? 0.00);
$total = $unit * $months;

try {
    $pdo->beginTransaction();
    // create order
    $stmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, currency, status, created_at) VALUES (:u,:total,:cur,:st,NOW())');
    $stmt->execute([':u'=>$user_id,':total'=>$total,':cur'=>'USD',':st'=>'completed']);
    $order_id = $pdo->lastInsertId();

    // create order item
    $stmt = $pdo->prepare('INSERT INTO order_items (order_id, plan_id, quantity, unit_price, period_months) VALUES (:o,:p,1,:price,:pm)');
    $stmt->execute([':o'=>$order_id,':p'=>$plan_id,':price'=>$unit,':pm'=>$months]);
    $item_id = $pdo->lastInsertId();

    // create subscription
    $stmt = $pdo->prepare('INSERT INTO subscriptions (user_id, plan_id, order_item_id, started_at, expires_at, status) VALUES (:u,:p,:oi,NOW(), DATE_ADD(NOW(), INTERVAL :m MONTH), :st)');
    $stmt->execute([':u'=>$user_id,':p'=>$plan_id,':oi'=>$item_id,':m'=>$months,':st'=>'active']);

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
