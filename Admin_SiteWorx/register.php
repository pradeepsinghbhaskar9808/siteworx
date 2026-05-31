<?php
require_once 'connection.php';
require_once 'lib_auth.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = trim($_POST['name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$user = trim($_POST['username'] ?? '');
	$pass = $_POST['password'] ?? '';

	if ($user === '' || $pass === '' || $name === '' || $email === '') {
		$message = 'All fields should be filled. Either one or many fields are empty.';
	} else {
		try {
			$id = register_user($pdo, $name, $email, $user, $pass);
			$message = 'Registration successful. <a href="login">Login</a>';
		} catch (PDOException $e) {
			$message = 'Registration error: ' . $e->getMessage();
		}
	}
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Register</title>
</head>
<body>
<a href="index">Home</a> <br />
<?php if ($message): ?>
	<div><?php echo $message; ?></div>
<?php endif; ?>

<h2>Register</h2>
<form method="post" action="">
	<label>Full Name<br><input type="text" name="name" required></label><br>
	<label>Email<br><input type="email" name="email" required></label><br>
	<label>Username<br><input type="text" name="username" required></label><br>
	<label>Password<br><input type="password" name="password" required></label><br>
	<button type="submit">Submit</button>
</form>
</body>
</html>
