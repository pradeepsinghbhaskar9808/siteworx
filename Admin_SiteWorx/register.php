<?php
session_start();
require_once 'connection.php';
require_once 'lib_auth.php';

$message = '';
$messageType = '';

// Redirect if already logged in
if (!empty($_SESSION['valid'])) {
	header('Location: index.php');
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = trim($_POST['name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$user = trim($_POST['username'] ?? '');
	$pass = $_POST['password'] ?? '';
	$confirm_pass = $_POST['confirm_password'] ?? '';

	if ($user === '' || $pass === '' || $name === '' || $email === '') {
		$message = 'All fields are required.';
		$messageType = 'error';
	} elseif (strlen($pass) < 6) {
		$message = 'Password must be at least 6 characters long.';
		$messageType = 'error';
	} elseif ($pass !== $confirm_pass) {
		$message = 'Passwords do not match.';
		$messageType = 'error';
	} elseif (strlen($user) < 3) {
		$message = 'Username must be at least 3 characters long.';
		$messageType = 'error';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$message = 'Please enter a valid email address.';
		$messageType = 'error';
	} else {
		try {
			// Check if username already exists
			$existing = find_user_by_username($pdo, $user);
			if ($existing) {
				$message = 'Username already exists. Please choose another.';
				$messageType = 'error';
			} else {
				$id = register_user($pdo, $name, $email, $user, $pass);
				$message = 'Registration successful! Redirecting to login...';
				$messageType = 'success';
				// Redirect after 2 seconds
				echo '<meta http-equiv="refresh" content="2;url=login.php">';
			}
		} catch (PDOException $e) {
			if (strpos($e->getMessage(), 'Duplicate') !== false) {
				$message = 'Email or username already registered.';
			} else {
				$message = 'Registration error: ' . $e->getMessage();
			}
			$messageType = 'error';
		}
	}
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SiteWorx Admin - Register</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<style>
		body {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
			padding: 20px 0;
		}

		.register-container {
			width: 100%;
			max-width: 500px;
			padding: 0 15px;
		}

		.register-card {
			background: white;
			border-radius: 10px;
			box-shadow: 0 10px 50px rgba(0, 0, 0, 0.2);
			overflow: hidden;
		}

		.register-header {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			padding: 40px 20px;
			text-align: center;
		}

		.register-header h1 {
			font-size: 28px;
			font-weight: 700;
			margin: 0;
			margin-bottom: 5px;
		}

		.register-header p {
			margin: 0;
			font-size: 14px;
			opacity: 0.9;
		}

		.register-body {
			padding: 40px;
		}

		.form-group {
			margin-bottom: 20px;
		}

		.form-label {
			font-weight: 600;
			color: #333;
			margin-bottom: 8px;
			font-size: 14px;
		}

		.form-control {
			border: 2px solid #e0e0e0;
			border-radius: 6px;
			padding: 12px 15px;
			font-size: 15px;
			transition: all 0.3s ease;
		}

		.form-control:focus {
			border-color: #667eea;
			box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
			outline: none;
		}

		.form-control::placeholder {
			color: #999;
		}

		.btn-register {
			width: 100%;
			padding: 12px;
			font-size: 16px;
			font-weight: 600;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border: none;
			border-radius: 6px;
			color: white;
			cursor: pointer;
			transition: all 0.3s ease;
			margin-top: 10px;
		}

		.btn-register:hover {
			transform: translateY(-2px);
			box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
		}

		.btn-register:active {
			transform: translateY(0);
		}

		.alert {
			border-radius: 6px;
			margin-bottom: 20px;
			border: none;
			padding: 12px 15px;
			font-size: 14px;
		}

		.alert-danger {
			background-color: #fee;
			color: #c33;
		}

		.alert-success {
			background-color: #efe;
			color: #3c3;
		}

		.register-footer {
			padding: 20px 40px;
			background-color: #f8f9fa;
			text-align: center;
			border-top: 1px solid #e0e0e0;
		}

		.register-footer p {
			margin: 0;
			font-size: 14px;
			color: #666;
		}

		.register-footer a {
			color: #667eea;
			text-decoration: none;
			font-weight: 600;
		}

		.register-footer a:hover {
			color: #764ba2;
			text-decoration: underline;
		}

		.input-icon {
			position: relative;
			padding-left: 35px;
		}

		.input-icon i {
			position: absolute;
			left: 12px;
			top: 50%;
			transform: translateY(-50%);
			color: #667eea;
			font-size: 16px;
		}

		.input-icon .form-control {
			padding-left: 40px;
		}

		.password-requirements {
			background-color: #f5f5f5;
			padding: 12px 15px;
			border-radius: 6px;
			font-size: 13px;
			color: #666;
			margin-top: 10px;
		}

		.password-requirements ul {
			margin: 10px 0 0 20px;
			padding: 0;
		}

		.password-requirements li {
			margin: 5px 0;
		}

		.requirement-check {
			display: inline-block;
			width: 16px;
			height: 16px;
			border-radius: 50%;
			background-color: #ddd;
			color: white;
			text-align: center;
			line-height: 16px;
			font-size: 12px;
			margin-right: 5px;
		}

		.requirement-check.valid {
			background-color: #28a745;
		}
	</style>
</head>
<body>
	<div class="register-container">
		<div class="register-card">
			<div class="register-header">
				<h1><i class="fas fa-cube"></i> SiteWorx</h1>
				<p>Create Your Account</p>
			</div>

			<div class="register-body">
				<?php if (!empty($message)): ?>
					<div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>" role="alert">
						<i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i> 
						<?php echo $message; ?>
					</div>
				<?php endif; ?>

				<form method="post" action="" autocomplete="off">
					<div class="form-group">
						<label for="name" class="form-label">Full Name</label>
						<div class="input-icon">
							<i class="fas fa-user"></i>
							<input 
								type="text" 
								class="form-control" 
								id="name" 
								name="name" 
								placeholder="Enter your full name" 
								required 
								value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
							>
						</div>
					</div>

					<div class="form-group">
						<label for="email" class="form-label">Email Address</label>
						<div class="input-icon">
							<i class="fas fa-envelope"></i>
							<input 
								type="email" 
								class="form-control" 
								id="email" 
								name="email" 
								placeholder="Enter your email" 
								required 
								value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
							>
						</div>
					</div>

					<div class="form-group">
						<label for="username" class="form-label">Username</label>
						<div class="input-icon">
							<i class="fas fa-at"></i>
							<input 
								type="text" 
								class="form-control" 
								id="username" 
								name="username" 
								placeholder="Choose a username (min. 3 characters)" 
								required 
								autocomplete="off"
								value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
								minlength="3"
							>
						</div>
					</div>

					<div class="form-group">
						<label for="password" class="form-label">Password</label>
						<div class="input-icon">
							<i class="fas fa-lock"></i>
							<input 
								type="password" 
								class="form-control" 
								id="password" 
								name="password" 
								placeholder="Choose a strong password" 
								required 
								autocomplete="new-password"
								minlength="6"
								onkeyup="checkPasswordStrength()"
							>
						</div>
						<div class="password-requirements">
							<strong>Password Requirements:</strong>
							<ul style="margin-bottom: 0;">
								<li><span class="requirement-check" id="len-check">✓</span>At least 6 characters</li>
							</ul>
						</div>
					</div>

					<div class="form-group">
						<label for="confirm_password" class="form-label">Confirm Password</label>
						<div class="input-icon">
							<i class="fas fa-lock"></i>
							<input 
								type="password" 
								class="form-control" 
								id="confirm_password" 
								name="confirm_password" 
								placeholder="Re-enter your password" 
								required 
								autocomplete="new-password"
								minlength="6"
							>
						</div>
					</div>

					<button type="submit" class="btn btn-register">
						<i class="fas fa-user-plus"></i> Create Account
					</button>
				</form>
			</div>

			<div class="register-footer">
				<p>
					Already have an account? 
					<a href="login.php">
						<i class="fas fa-sign-in-alt"></i> Login here
					</a>
				</p>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		function checkPasswordStrength() {
			const password = document.getElementById('password').value;
			const lenCheck = document.getElementById('len-check');
			
			if (password.length >= 6) {
				lenCheck.classList.add('valid');
			} else {
				lenCheck.classList.remove('valid');
			}
		}

		// Initialize password check on page load
		document.addEventListener('DOMContentLoaded', function() {
			checkPasswordStrength();
		});
	</script>
</body>
</html>
