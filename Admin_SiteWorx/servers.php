<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_admin.php';
require_role($pdo, ['admin','manager']);

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $hostname = trim($_POST['hostname'] ?? '');
    $ip = trim($_POST['ip_address'] ?? '');
    $provider = trim($_POST['provider'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $specs = sw_json_or_error($_POST['specs'] ?? '{}', $msg);
    if ($hostname === '') $msg = 'Hostname is required.';
    elseif ($specs !== false) {
        if ($id) {
            $stmt = $pdo->prepare('UPDATE servers SET hostname=:hostname,ip_address=:ip,provider=:provider,region=:region,specs=:specs,status=:status WHERE id=:id');
            $stmt->execute([':hostname'=>$hostname,':ip'=>$ip,':provider'=>$provider,':region'=>$region,':specs'=>$specs,':status'=>$status,':id'=>$id]);
            $msg = 'Server updated.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO servers (hostname,ip_address,provider,region,specs,status) VALUES (:hostname,:ip,:provider,:region,:specs,:status)');
            $stmt->execute([':hostname'=>$hostname,':ip'=>$ip,':provider'=>$provider,':region'=>$region,':specs'=>$specs,':status'=>$status]);
            $msg = 'Server created.';
        }
    }
}
$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM servers WHERE id=:id');
    $stmt->execute([':id'=>(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}
$servers = $pdo->query('SELECT * FROM servers ORDER BY region,hostname')->fetchAll();
?>
<?php include '_header.php'; ?>
<h1>Servers</h1>
<?php if ($msg): ?><div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<form method="post" class="card card-body mb-4">
  <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit['id'] ?? ''); ?>">
  <div class="row g-2">
    <div class="col-md-3"><label>Hostname<input class="form-control" name="hostname" value="<?php echo htmlspecialchars($edit['hostname'] ?? ''); ?>"></label></div>
    <div class="col-md-2"><label>IP<input class="form-control" name="ip_address" value="<?php echo htmlspecialchars($edit['ip_address'] ?? ''); ?>"></label></div>
    <div class="col-md-2"><label>Provider<input class="form-control" name="provider" value="<?php echo htmlspecialchars($edit['provider'] ?? ''); ?>"></label></div>
    <div class="col-md-2"><label>Region<input class="form-control" name="region" value="<?php echo htmlspecialchars($edit['region'] ?? ''); ?>"></label></div>
    <div class="col-md-2"><label>Status<select class="form-select" name="status"><option value="active">active</option><option value="maintenance">maintenance</option><option value="retired">retired</option></select></label></div>
  </div>
  <label class="mt-2 w-100">Specs JSON<textarea class="form-control" name="specs" rows="3"><?php echo htmlspecialchars($edit['specs'] ?? '{}'); ?></textarea></label>
  <div class="mt-2"><button class="btn btn-primary">Save Server</button></div>
</form>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>Hostname</th><th>IP</th><th>Provider</th><th>Region</th><th>Status</th><th></th></tr></thead>
  <tbody><?php foreach($servers as $s): ?><tr><td><?php echo $s['id']; ?></td><td><?php echo htmlspecialchars($s['hostname']); ?></td><td><?php echo htmlspecialchars($s['ip_address']); ?></td><td><?php echo htmlspecialchars($s['provider']); ?></td><td><?php echo htmlspecialchars($s['region']); ?></td><td><?php echo htmlspecialchars($s['status']); ?></td><td><a class="btn btn-sm btn-secondary" href="servers.php?edit=<?php echo $s['id']; ?>">Edit</a></td></tr><?php endforeach; ?></tbody>
</table>
<?php include '_footer.php'; ?>
