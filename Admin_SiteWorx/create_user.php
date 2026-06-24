<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_admin.php';
require_role($pdo, ['admin','manager']);

$roles = $pdo->query('SELECT id,name FROM roles ORDER BY id')->fetchAll();
$managers = $pdo->query("SELECT id,name,username FROM login WHERE role_id IN (1,2) AND status='active' ORDER BY name,username")->fetchAll();
$current = current_user($pdo);
$currentRole = get_user_role($pdo, $current);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role_id = $currentRole === 'admin' ? (int)($_POST['role_id'] ?? 3) : 3;
    $manager_id = $currentRole === 'admin' ? (int)($_POST['manager_id'] ?? 0) : (int)$current['id'];
    if ($username && $password) {
        $id = register_user($pdo, $name, $email, $username, $password);
        $stmt = $pdo->prepare('UPDATE login SET role_id = :rid, manager_id = :mid WHERE id = :id');
        $stmt->execute([':rid'=>$role_id, ':mid'=>($manager_id ?: null), ':id'=>$id]);
        header('Location: users.php'); exit;
    } else {
        $error = 'Username and password required';
    }
}
?>
<?php include '_header.php'; ?>
<div class="card">
  <div class="card-body">
    <h4>Create User</h4>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <div class="mb-2"><label>Name<input class="form-control" name="name"></label></div>
      <div class="mb-2"><label>Username<input class="form-control" name="username" required></label></div>
      <div class="mb-2"><label>Email<input class="form-control" name="email" type="email"></label></div>
      <div class="mb-2"><label>Password<input class="form-control" name="password" type="password" required></label></div>
      <?php if ($currentRole === 'admin'): ?>
      <div class="mb-2"><label>Role
        <select name="role_id" class="form-select">
          <?php foreach($roles as $r): ?><option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option><?php endforeach; ?>
        </select>
      </label></div>
      <div class="mb-2"><label>Manager
        <select name="manager_id" class="form-select">
          <option value="">No manager</option>
          <?php foreach($managers as $m): ?><option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['name'] ?: $m['username']); ?></option><?php endforeach; ?>
        </select>
      </label></div>
      <?php endif; ?>
      <button class="btn btn-primary">Create</button>
    </form>
  </div>
</div>
<?php include '_footer.php'; ?>
