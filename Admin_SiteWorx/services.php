<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_admin.php';
require_role($pdo, ['admin','manager']);

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? 'service');
    $price = (float)($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    $meta = sw_json_or_error($_POST['meta'] ?? '{}', $msg);
    if ($code === '' || $name === '') {
        $msg = 'Code and name are required.';
    } elseif ($meta !== false) {
        if ($id) {
            $stmt = $pdo->prepare('UPDATE service_catalog SET code=:code,name=:name,type=:type,meta=:meta,price=:price,status=:status WHERE id=:id');
            $stmt->execute([':code'=>$code,':name'=>$name,':type'=>$type,':meta'=>$meta,':price'=>$price,':status'=>$status,':id'=>$id]);
            $msg = 'Service updated.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO service_catalog (code,name,type,meta,price,status) VALUES (:code,:name,:type,:meta,:price,:status)');
            $stmt->execute([':code'=>$code,':name'=>$name,':type'=>$type,':meta'=>$meta,':price'=>$price,':status'=>$status]);
            $msg = 'Service created.';
        }
    }
}

$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM service_catalog WHERE id=:id');
    $stmt->execute([':id'=>(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}
$services = $pdo->query('SELECT * FROM service_catalog ORDER BY type,name')->fetchAll();
?>
<?php include '_header.php'; ?>
<h1>Services</h1>
<?php if ($msg): ?><div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<form method="post" class="card card-body mb-4">
  <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit['id'] ?? ''); ?>">
  <div class="row g-2">
    <div class="col-md-2"><label>Code<input class="form-control" name="code" value="<?php echo htmlspecialchars($edit['code'] ?? ''); ?>"></label></div>
    <div class="col-md-3"><label>Name<input class="form-control" name="name" value="<?php echo htmlspecialchars($edit['name'] ?? ''); ?>"></label></div>
    <div class="col-md-2"><label>Type<input class="form-control" name="type" value="<?php echo htmlspecialchars($edit['type'] ?? 'service'); ?>"></label></div>
    <div class="col-md-2"><label>Price<input class="form-control" name="price" value="<?php echo htmlspecialchars($edit['price'] ?? '0'); ?>"></label></div>
    <div class="col-md-2"><label>Status<select class="form-select" name="status"><option value="active">active</option><option value="inactive">inactive</option></select></label></div>
  </div>
  <label class="mt-2 w-100">Meta JSON<textarea class="form-control" name="meta" rows="3"><?php echo htmlspecialchars($edit['meta'] ?? '{}'); ?></textarea></label>
  <div class="mt-2"><button class="btn btn-primary">Save Service</button></div>
</form>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>Code</th><th>Name</th><th>Type</th><th>Price</th><th>Status</th><th></th></tr></thead>
  <tbody><?php foreach($services as $s): ?><tr><td><?php echo $s['id']; ?></td><td><?php echo htmlspecialchars($s['code']); ?></td><td><?php echo htmlspecialchars($s['name']); ?></td><td><?php echo htmlspecialchars($s['type']); ?></td><td><?php echo sw_format_money($s['price']); ?></td><td><?php echo htmlspecialchars($s['status']); ?></td><td><a class="btn btn-sm btn-secondary" href="services.php?edit=<?php echo $s['id']; ?>">Edit</a></td></tr><?php endforeach; ?></tbody>
</table>
<?php include '_footer.php'; ?>
