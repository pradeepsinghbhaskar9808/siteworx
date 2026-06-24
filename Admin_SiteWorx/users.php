<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_admin.php';
require_role($pdo, ['admin','manager']);

[$current, $role] = sw_current_role($pdo);
[$where, $params] = sw_manager_user_filter_sql($role, (int)$current['id'], 'u');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'admin') {
    $id = (int)($_POST['id'] ?? 0);
    $roleId = (int)($_POST['role_id'] ?? 3);
    $managerId = (int)($_POST['manager_id'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    $stmt = $pdo->prepare('UPDATE login SET role_id=:role_id, manager_id=:manager_id, status=:status WHERE id=:id');
    $stmt->execute([':role_id'=>$roleId, ':manager_id'=>($managerId ?: null), ':status'=>$status, ':id'=>$id]);
    header('Location: users.php');
    exit;
}

$roles = $pdo->query('SELECT id,name FROM roles ORDER BY id')->fetchAll();
$managers = $pdo->query("SELECT id,name,username FROM login WHERE role_id IN (1,2) AND status='active' ORDER BY name,username")->fetchAll();
$stmt = $pdo->prepare("SELECT u.*, r.name AS role_name, m.name AS manager_name, m.username AS manager_username FROM login u LEFT JOIN roles r ON r.id=u.role_id LEFT JOIN login m ON m.id=u.manager_id WHERE {$where} ORDER BY u.id DESC");
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<?php include '_header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>Users</h1>
  <a class="btn btn-primary" href="create_user.php">Create User</a>
</div>
<table class="table table-striped align-middle">
  <thead><tr><th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Manager</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($users as $u): ?>
    <tr>
      <td><?php echo $u['id']; ?></td>
      <td><?php echo htmlspecialchars($u['name']); ?></td>
      <td><?php echo htmlspecialchars($u['username']); ?></td>
      <td><?php echo htmlspecialchars($u['email']); ?></td>
      <?php if ($role === 'admin'): ?>
        <form method="post">
          <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
          <td><select name="role_id" class="form-select form-select-sm"><?php foreach($roles as $r): ?><option value="<?php echo $r['id']; ?>" <?php echo (int)$u['role_id']===(int)$r['id']?'selected':''; ?>><?php echo htmlspecialchars($r['name']); ?></option><?php endforeach; ?></select></td>
          <td><select name="manager_id" class="form-select form-select-sm"><option value="">None</option><?php foreach($managers as $m): ?><option value="<?php echo $m['id']; ?>" <?php echo (int)$u['manager_id']===(int)$m['id']?'selected':''; ?>><?php echo htmlspecialchars($m['name'] ?: $m['username']); ?></option><?php endforeach; ?></select></td>
          <td><select name="status" class="form-select form-select-sm"><?php foreach(['active','suspended','deleted'] as $s): ?><option <?php echo $u['status']===$s?'selected':''; ?>><?php echo $s; ?></option><?php endforeach; ?></select></td>
          <td><button class="btn btn-sm btn-primary">Save</button></td>
        </form>
      <?php else: ?>
        <td><?php echo htmlspecialchars($u['role_name']); ?></td>
        <td><?php echo htmlspecialchars($u['manager_name'] ?: $u['manager_username']); ?></td>
        <td><?php echo htmlspecialchars($u['status']); ?></td>
        <td></td>
      <?php endif; ?>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php include '_footer.php'; ?>
