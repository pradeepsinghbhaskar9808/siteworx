<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_admin.php';
require_login();

[$current, $role] = sw_current_role($pdo);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($role, ['admin','manager'], true)) {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $status = trim($_POST['status'] ?? 'pending');
    $invoiceStatus = trim($_POST['invoice_status'] ?? 'unpaid');
    $owner = $pdo->prepare('SELECT user_id FROM orders WHERE id=:id');
    $owner->execute([':id'=>$orderId]);
    $row = $owner->fetch();
    if ($row && sw_can_manage_user($pdo, $row['user_id'])) {
        $pdo->prepare('UPDATE orders SET status=:status WHERE id=:id')->execute([':status'=>$status, ':id'=>$orderId]);
        $pdo->prepare('UPDATE invoices SET status=:status WHERE order_id=:id')->execute([':status'=>$invoiceStatus, ':id'=>$orderId]);
    }
    header('Location: orders.php');
    exit;
}
[$where, $params] = sw_manager_user_filter_sql($role, (int)$current['id'], 'u');
$stmt = $pdo->prepare("SELECT o.*, u.name, u.username, i.id AS invoice_id, i.status AS invoice_status FROM orders o JOIN login u ON u.id=o.user_id LEFT JOIN invoices i ON i.order_id=o.id WHERE {$where} ORDER BY o.created_at DESC LIMIT 200");
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>
<?php include '_header.php'; ?>
<h1>Orders</h1>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Created</th><th>Invoice</th><th></th></tr></thead>
  <tbody>
  <?php foreach($orders as $o): ?>
    <tr>
      <td><?php echo $o['id']; ?></td>
      <td><?php echo htmlspecialchars($o['name'] ?: $o['username']); ?></td>
      <td><?php echo htmlspecialchars($o['currency']); ?> <?php echo number_format($o['total_amount'],2); ?></td>
      <td><?php echo htmlspecialchars($o['status']); ?></td>
      <td><?php echo htmlspecialchars($o['created_at']); ?></td>
      <td><?php if ($o['invoice_id']): ?><a href="generate_invoice?invoice_id=<?php echo $o['invoice_id']; ?>">#<?php echo $o['invoice_id']; ?> <?php echo htmlspecialchars($o['invoice_status']); ?></a><?php endif; ?></td>
      <td>
        <?php if (in_array($role, ['admin','manager'], true)): ?>
          <form method="post" class="d-flex gap-2">
            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
            <select name="status" class="form-select form-select-sm"><option>pending</option><option <?php echo $o['status']==='completed'?'selected':''; ?>>completed</option><option <?php echo $o['status']==='cancelled'?'selected':''; ?>>cancelled</option></select>
            <select name="invoice_status" class="form-select form-select-sm"><option>unpaid</option><option <?php echo $o['invoice_status']==='paid'?'selected':''; ?>>paid</option><option <?php echo $o['invoice_status']==='void'?'selected':''; ?>>void</option></select>
            <button class="btn btn-sm btn-primary">Save</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php include '_footer.php'; ?>
