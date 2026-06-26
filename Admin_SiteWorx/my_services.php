<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_admin.php';
require_login();

$current = current_user($pdo);
$stmt = $pdo->prepare(
    'SELECT s.*, hp.name AS plan_name, hp.category AS plan_category, sc.name AS service_name, sc.type AS service_type, srv.hostname, srv.ip_address, srv.region
     FROM subscriptions s
     LEFT JOIN hosting_plans hp ON hp.id = s.plan_id
     LEFT JOIN service_catalog sc ON sc.id = s.service_id
     LEFT JOIN servers srv ON srv.id = s.server_id
     WHERE s.user_id = :uid
     ORDER BY s.started_at DESC'
);
$stmt->execute([':uid' => $current['id']]);
$subs = $stmt->fetchAll();

$inv = $pdo->prepare('SELECT i.* FROM invoices i JOIN orders o ON o.id=i.order_id WHERE o.user_id=:uid ORDER BY i.issued_at DESC LIMIT 10');
$inv->execute([':uid' => $current['id']]);
$invoices = $inv->fetchAll();
?>
<?php include '_header.php'; ?>
<h1>My Services</h1>
<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header"><strong>Active Services</strong></div>
      <div class="card-body p-0">
        <table class="table table-striped mb-0">
          <thead><tr><th>Service</th><th>Server/IP</th><th>Status</th><th>Started</th><th>Expires</th></tr></thead>
          <tbody>
          <?php foreach ($subs as $s): ?>
            <?php $name = $s['plan_name'] ?: ($s['service_name'] ?: $s['hostname']); ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($name); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($s['plan_category'] ?: $s['service_type'] ?: $s['region']); ?></small></td>
              <td><?php echo htmlspecialchars($s['hostname'] ?: ''); ?><br><small><?php echo htmlspecialchars($s['ip_address'] ?: ''); ?></small></td>
              <td><?php echo htmlspecialchars($s['status']); ?></td>
              <td><?php echo htmlspecialchars($s['started_at']); ?></td>
              <td><?php echo htmlspecialchars($s['expires_at']); ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$subs): ?><tr><td colspan="5" class="text-muted">No services assigned yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header"><strong>Recent Invoices</strong></div>
      <div class="list-group list-group-flush">
        <?php foreach($invoices as $i): ?><a class="list-group-item list-group-item-action d-flex justify-content-between" href="generate_invoice?invoice_id=<?php echo $i['id']; ?>"><span>#<?php echo $i['id']; ?> <?php echo htmlspecialchars($i['status']); ?></span><strong><?php echo number_format($i['amount'], 2); ?></strong></a><?php endforeach; ?>
        <?php if (!$invoices): ?><div class="list-group-item text-muted">No invoices yet.</div><?php endif; ?>
      </div>
    </div>
    <a class="btn btn-outline-primary" href="change_password.php">Change Password</a>
  </div>
</div>
<?php include '_footer.php'; ?>
