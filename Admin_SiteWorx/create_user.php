<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_role($pdo, ['admin']);

$roles = $pdo->query('SELECT id,name FROM roles ORDER BY id')->fetchAll();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role_id = (int)($_POST['role_id'] ?? 3);
    if ($username && $password) {
        $id = register_user($pdo, $name, $email, $username, $password);
        $stmt = $pdo->prepare('UPDATE login SET role_id = :rid WHERE id = :id');
        $stmt->execute([':rid'=>$role_id, ':id'=>$id]);
        header('Location: index.php'); exit;
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
      <div class="mb-2"><label>Role
        <select name="role_id" class="form-select">
          <?php foreach($roles as $r): ?><option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option><?php endforeach; ?>
        </select>
      </label></div>
      <button class="btn btn-primary">Create</button>
    </form>
  </div>
</div>
<?php include '_footer.php'; ?>
