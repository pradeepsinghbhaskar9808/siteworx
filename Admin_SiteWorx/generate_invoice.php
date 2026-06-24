<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_login();

$invoice_id = (int)($_GET['invoice_id'] ?? 0);
if (!$invoice_id) { echo 'Invoice id required'; exit; }

$stmt = $pdo->prepare('SELECT i.*, o.user_id FROM invoices i JOIN orders o ON o.id = i.order_id WHERE i.id = :id LIMIT 1');
$stmt->execute([':id'=>$invoice_id]);
$inv = $stmt->fetch();
if (!$inv) { echo 'Invoice not found'; exit; }

// ensure client or admin
$current = current_user($pdo);
if ($current['id'] !== (int)$inv['user_id']) {
    // allow admins
    $role = get_user_role($pdo, $current);
    if ($role !== 'admin' && $role !== 'manager') {
        http_response_code(403); echo 'Forbidden'; exit;
    }
}

$items = $pdo->prepare('SELECT oi.*, hp.name AS plan_name, sc.name AS service_name FROM order_items oi LEFT JOIN hosting_plans hp ON hp.id = oi.plan_id LEFT JOIN service_catalog sc ON sc.id = oi.service_id WHERE oi.order_id = :o');
$items->execute([':o'=>$inv['order_id']]);
$items = $items->fetchAll();

?>
<?php include '_header.php'; ?>
<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between">
      <h3>Invoice #<?php echo $inv['id']; ?></h3>
      <div>
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">Print</button>
        <form method="post" style="display:inline" action="generate_invoice.php?invoice_id=<?php echo $inv['id']; ?>&send=1">
          <button class="btn btn-sm btn-primary">Send Email</button>
        </form>
      </div>
    </div>
    <hr>
    <div><strong>Billed To:</strong> <?php $u=$pdo->prepare('SELECT name,email FROM login WHERE id=:id'); $u->execute([':id'=>$inv['user_id']]); $usr=$u->fetch(); echo htmlspecialchars($usr['name']).' &lt;'.htmlspecialchars($usr['email']).'&gt;'; ?></div>
    <table class="table mt-3">
      <thead><tr><th>Item</th><th>Period</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
      <tbody>
        <?php foreach($items as $it): ?>
          <?php
            $meta = !empty($it['meta']) ? json_decode($it['meta'], true) : [];
            $itemName = $it['plan_name'] ?: ($it['service_name'] ?: ($meta['hostname'] ?? 'Service'));
          ?>
          <tr>
            <td><?php echo htmlspecialchars($itemName); ?></td>
            <td><?php echo intval($it['period_months']); ?> months</td>
            <td><?php echo intval($it['quantity']); ?></td>
            <td><?php echo number_format($it['unit_price'],2); ?></td>
            <td><?php echo number_format(($it['unit_price'] * $it['quantity'] * max(1,$it['period_months'])),2); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="text-end"><strong>Amount Due: <?php echo number_format($inv['amount'],2); ?></strong></div>
  </div>
  </div>
<?php include '_footer.php'; ?>

<?php
// handle send
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['send'])) {
    // send same email as assign
    $u = $pdo->prepare('SELECT email,name FROM login WHERE id = :id LIMIT 1');
    $u->execute([':id'=>$inv['user_id']]); $user = $u->fetch();
    if ($user && !empty($user['email'])) {
        $to = $user['email'];
        $subject = 'Invoice #' . $inv['id'];
        $link = (isset($_SERVER['HTTP_HOST'])? 'https://'.$_SERVER['HTTP_HOST'] : '') . dirname($_SERVER['REQUEST_URI']) . '/generate_invoice.php?invoice_id=' . $inv['id'];
        $message = "Hello " . ($user['name'] ?? $user['email']) . ",\n\nYour invoice: " . $link;
        $headers = 'From: noreply@' . ($_SERVER['HTTP_HOST'] ?? 'example.com') . "\r\n";
        @mail($to, $subject, $message, $headers);
        echo '<div class="container mt-2"><div class="alert alert-success">Email sent to '.htmlspecialchars($user['email']).'</div></div>';
    }
}
