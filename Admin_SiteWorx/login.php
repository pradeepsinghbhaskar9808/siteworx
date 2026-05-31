<?php
session_start();
require_once 'connection.php';
require_once 'lib_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$user = trim($_POST['username'] ?? '');
	$pass = $_POST['password'] ?? '';

	if ($user === '' || $pass === '') {
		$error = 'Either username or password field is empty.';
	} else {
		try {
			$u = login_user($pdo, $user, $pass);
			if ($u) {
				$_SESSION['valid'] = $u['username'];
				$_SESSION['name'] = $u['name'];
				$_SESSION['id'] = $u['id'];
				header('Location: index');
				exit;
			} else {
				$error = 'Invalid username or password.';
			}
		} catch (Exception $e) {
			$error = 'Login error: ' . $e->getMessage();
		}
	}
}

?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Login</title>
</head>
<body>
<a href="index">Home</a> <br />
<?php if (!empty($error)): ?>
	<div style="color:red"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<h2>Login</h2>
<form method="post" action="">
	<label>Username<br><input type="text" name="username" required></label><br>
	<label>Password<br><input type="password" name="password" required></label><br>
	<button type="submit">Submit</button>
</form>
</body>
</html>
