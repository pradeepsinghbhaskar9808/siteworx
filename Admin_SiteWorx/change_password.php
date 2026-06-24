<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_login();

$message = '';
$error = '';
$current = current_user($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if ($new === '' || strlen($new) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $error = 'New passwords do not match.';
    } elseif (!login_user($pdo, $current['username'], $old)) {
        $error = 'Current password is incorrect.';
    } else {
        update_user_password($pdo, $current['id'], $new);
        $message = 'Password changed successfully.';
    }
}
?>
<?php include '_header.php'; ?>
<div class="card" style="max-width:560px">
  <div class="card-body">
    <h1 class="h4">Change Password</h1>
    <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <label class="w-100 mb-2">Current Password<input class="form-control" type="password" name="old_password" required></label>
      <label class="w-100 mb-2">New Password<input class="form-control" type="password" name="new_password" minlength="6" required></label>
      <label class="w-100 mb-3">Confirm Password<input class="form-control" type="password" name="confirm_password" minlength="6" required></label>
      <button class="btn btn-primary">Update Password</button>
    </form>
  </div>
</div>
<?php include '_footer.php'; ?>
