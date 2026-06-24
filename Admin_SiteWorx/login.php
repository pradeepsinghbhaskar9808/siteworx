<?php
session_start();
require_once 'connection.php';
require_once 'lib_auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$user = trim($_POST['username'] ?? '');
	$pass = $_POST['password'] ?? '';

	if ($user === '' || $pass === '') {
		$error = 'Both username and password are required.';
	} else {
		try {
			$u = login_user($pdo, $user, $pass);
			if ($u) {
				$_SESSION['valid'] = $u['username'];
				$_SESSION['name'] = $u['name'];
				$_SESSION['id'] = $u['id'];
				
				// Get and store user role
				$roleStmt = $pdo->prepare('SELECT r.name FROM roles r WHERE r.id = :role_id');
				$roleStmt->execute([':role_id' => $u['role_id'] ?? null]);
				$roleRow = $roleStmt->fetch();
				$_SESSION['role'] = $roleRow ? $roleRow['name'] : 'client';

				if (!empty($_POST['rememberme'])) {
					setcookie('siteworx_user', $u['username'], time() + (30 * 24 * 60 * 60), '/');
				}

				header('Location: index.php');
				exit;
			}
			$error = 'Invalid username or password. Please try again.';
		} catch (Exception $e) {
			$error = 'Login error: ' . $e->getMessage();
		}
	}
}

if (!empty($_SESSION['valid'])) {
	header('Location: index.php');
	exit;
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SiteWorx Admin Login</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<style>
		:root {
			--swx-ink: #17202a;
			--swx-muted: #667085;
			--swx-line: #d9e2ec;
			--swx-blue: #1261a6;
			--swx-teal: #0f8b8d;
			--swx-green: #20a36b;
			--swx-bg: #eef3f7;
		}

		* {
			box-sizing: border-box;
		}

		body {
			min-height: 100vh;
			margin: 0;
			font-family: "Segoe UI", Tahoma, Arial, sans-serif;
			color: var(--swx-ink);
			background:
				linear-gradient(120deg, rgba(18, 97, 166, .08), rgba(32, 163, 107, .08)),
				var(--swx-bg);
			display: grid;
			place-items: center;
			padding: 28px;
		}

		.login-shell {
			width: min(980px, 100%);
			min-height: 560px;
			background: #fff;
			border: 1px solid rgba(23, 32, 42, .08);
			border-radius: 8px;
			box-shadow: 0 24px 70px rgba(15, 35, 52, .14);
			display: grid;
			grid-template-columns: minmax(300px, 0.95fr) minmax(360px, 1.05fr);
			overflow: hidden;
		}

		.brand-panel {
			position: relative;
			padding: 44px;
			color: #fff;
			background:
				linear-gradient(rgba(12, 54, 85, .84), rgba(12, 54, 85, .88)),
				url('../images/background/server-cabinets-banner.jpg') center/cover;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
		}

		.brand-mark {
			display: inline-flex;
			align-items: center;
			gap: 12px;
			font-size: 24px;
			font-weight: 800;
		}

		.brand-mark i {
			width: 40px;
			height: 40px;
			display: inline-grid;
			place-items: center;
			border-radius: 8px;
			background: rgba(255, 255, 255, .16);
			border: 1px solid rgba(255, 255, 255, .22);
		}

		.brand-copy h1 {
			font-size: 34px;
			line-height: 1.15;
			font-weight: 800;
			margin: 0 0 14px;
		}

		.brand-copy p {
			max-width: 340px;
			margin: 0;
			color: rgba(255, 255, 255, .82);
			line-height: 1.7;
		}

		.brand-stats {
			display: grid;
			grid-template-columns: repeat(3, 1fr);
			gap: 10px;
		}

		.brand-stat {
			padding: 12px;
			border-radius: 8px;
			background: rgba(255, 255, 255, .12);
			border: 1px solid rgba(255, 255, 255, .16);
		}

		.brand-stat strong {
			display: block;
			font-size: 18px;
		}

		.brand-stat span {
			display: block;
			font-size: 12px;
			color: rgba(255, 255, 255, .76);
		}

		.login-panel {
			padding: 54px;
			display: flex;
			flex-direction: column;
			justify-content: center;
		}

		.login-heading {
			margin-bottom: 28px;
		}

		.login-heading .eyebrow {
			color: var(--swx-teal);
			font-size: 13px;
			text-transform: uppercase;
			font-weight: 800;
		}

		.login-heading h2 {
			margin: 6px 0 8px;
			font-size: 28px;
			font-weight: 800;
		}

		.login-heading p {
			margin: 0;
			color: var(--swx-muted);
		}

		.form-group {
			margin-bottom: 18px;
		}

		.form-label {
			font-weight: 700;
			font-size: 14px;
			margin-bottom: 8px;
		}

		.input-wrap {
			position: relative;
		}

		.input-wrap i {
			position: absolute;
			left: 14px;
			top: 50%;
			transform: translateY(-50%);
			color: var(--swx-blue);
		}

		.form-control {
			height: 48px;
			border-radius: 8px;
			border: 1px solid var(--swx-line);
			padding: 12px 46px 12px 42px;
			font-size: 15px;
		}

		.form-control:focus {
			border-color: var(--swx-blue);
			box-shadow: 0 0 0 .18rem rgba(18, 97, 166, .14);
		}

		.password-toggle-btn {
			position: absolute;
			right: 10px;
			top: 50%;
			transform: translateY(-50%);
			width: 34px;
			height: 34px;
			border: 0;
			border-radius: 8px;
			background: transparent;
			color: var(--swx-muted);
		}

		.password-toggle-btn:hover {
			background: #eef5fb;
			color: var(--swx-blue);
		}

		.login-options {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin: 8px 0 22px;
			gap: 16px;
		}

		.form-check-label,
		.login-options a {
			font-size: 14px;
		}

		.login-options a {
			color: var(--swx-blue);
			text-decoration: none;
			font-weight: 700;
		}

		.btn-login {
			width: 100%;
			height: 48px;
			border: 0;
			border-radius: 8px;
			background: linear-gradient(90deg, var(--swx-blue), var(--swx-teal));
			color: #fff;
			font-weight: 800;
			letter-spacing: 0;
			box-shadow: 0 12px 22px rgba(18, 97, 166, .22);
		}

		.btn-login:hover {
			filter: brightness(.98);
		}

		.alert {
			border: 0;
			border-radius: 8px;
			padding: 12px 14px;
			font-size: 14px;
		}

		.login-foot {
			margin-top: 22px;
			padding-top: 18px;
			border-top: 1px solid var(--swx-line);
			color: var(--swx-muted);
			font-size: 14px;
		}

		.login-foot a {
			color: var(--swx-blue);
			font-weight: 700;
			text-decoration: none;
		}

		@media (max-width: 820px) {
			body {
				padding: 16px;
			}

			.login-shell {
				grid-template-columns: 1fr;
				min-height: auto;
			}

			.brand-panel {
				min-height: 280px;
				padding: 28px;
			}

			.brand-copy h1 {
				font-size: 28px;
			}

			.login-panel {
				padding: 30px 24px;
			}
		}
	</style>
</head>
<body>
	<main class="login-shell">
		<section class="brand-panel">
			<div class="brand-mark">
				<i class="fas fa-server"></i>
				<span>SiteWorx</span>
			</div>

			<div class="brand-copy">
				<h1>Service control for hosting, domains, email and servers.</h1>
				<p>Manage plans, assign client services, track invoices, and keep every hosting account in one place.</p>
			</div>

			<div class="brand-stats" aria-label="SiteWorx service areas">
				<div class="brand-stat"><strong>DNS</strong><span>Domains</span></div>
				<div class="brand-stat"><strong>VPS</strong><span>Servers</span></div>
				<div class="brand-stat"><strong>Mail</strong><span>Email</span></div>
			</div>
		</section>

		<section class="login-panel">
			<div class="login-heading">
				<div class="eyebrow">Admin Panel</div>
				<h2>Sign in</h2>
				<p>Use your SiteWorx admin, manager, or client account.</p>
			</div>

			<?php if (!empty($error)): ?>
				<div class="alert alert-danger" role="alert">
					<i class="fas fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
				</div>
			<?php endif; ?>

			<?php if (!empty($success)): ?>
				<div class="alert alert-success" role="alert">
					<i class="fas fa-circle-check"></i> <?php echo htmlspecialchars($success); ?>
				</div>
			<?php endif; ?>

			<form method="post" action="" autocomplete="off">
				<div class="form-group">
					<label for="username" class="form-label">Username</label>
					<div class="input-wrap">
						<i class="fas fa-user"></i>
						<input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required autofocus autocomplete="off" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
					</div>
				</div>

				<div class="form-group">
					<label for="password" class="form-label">Password</label>
					<div class="input-wrap">
						<i class="fas fa-lock"></i>
						<input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required autocomplete="off">
						<button type="button" class="password-toggle-btn" onclick="togglePassword()" aria-label="Show or hide password">
							<i class="fas fa-eye"></i>
						</button>
					</div>
				</div>

				<div class="login-options">
					<div class="form-check">
						<input type="checkbox" class="form-check-input" id="rememberme" name="rememberme">
						<label class="form-check-label" for="rememberme">Remember me</label>
					</div>
					<a href="reset_password.php">Forgot password?</a>
				</div>

				<button type="submit" class="btn-login">
					<i class="fas fa-arrow-right-to-bracket"></i> Login
				</button>
			</form>

			<div class="login-foot">
				New client? <a href="register.php">Create an account</a>
			</div>
		</section>
	</main>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		function togglePassword() {
			const passwordInput = document.getElementById('password');
			const toggleIcon = document.querySelector('.password-toggle-btn i');

			if (passwordInput.type === 'password') {
				passwordInput.type = 'text';
				toggleIcon.classList.remove('fa-eye');
				toggleIcon.classList.add('fa-eye-slash');
			} else {
				passwordInput.type = 'password';
				toggleIcon.classList.remove('fa-eye-slash');
				toggleIcon.classList.add('fa-eye');
			}
		}

		if (window.history.replaceState) {
			window.history.replaceState(null, null, window.location.href);
		}
	</script>
</body>
</html>
