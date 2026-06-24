<?php
session_start();
require_once 'connection.php';
require_once 'lib_auth.php';

$message = '';
$messageType = '';
$step = isset($_GET['step']) ? $_GET['step'] : 1;
$resetToken = isset($_GET['token']) ? $_GET['token'] : '';

// Redirect if already logged in
if (!empty($_SESSION['valid'])) {
	header('Location: index.php');
	exit;
}

// Step 1: Request password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 1) {
	$email = trim($_POST['email'] ?? '');
	
	if ($email === '') {
		$message = 'Please enter your email address.';
		$messageType = 'error';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$message = 'Please enter a valid email address.';
		$messageType = 'error';
	} else {
		try {
			// Check if email exists
			$stmt = $pdo->prepare('SELECT * FROM login WHERE email = :email LIMIT 1');
			$stmt->execute([':email' => $email]);
			$user = $stmt->fetch();
			
			if ($user) {
				// Generate reset token
				$resetToken = bin2hex(random_bytes(32));
				$expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
				
				// Store token in database (you need to add reset_token and reset_expiry columns to login table)
				try {
					$stmt = $pdo->prepare('UPDATE login SET reset_token = :token, reset_expiry = :expiry WHERE email = :email');
					$stmt->execute([
						':token' => $resetToken,
						':expiry' => $expiry,
						':email' => $email
					]);
					
					$message = 'If an account exists with this email, you will receive a password reset link shortly.';
					$messageType = 'success';
					
					// In production, send email here with reset link
					// For demo, show the reset link
					echo '<!-- Demo Reset Link: reset_password.php?token=' . $resetToken . ' -->';
				} catch (PDOException $e) {
					// If columns don't exist, show message
					$message = 'Password reset feature is being set up. Please contact support.';
					$messageType = 'info';
				}
			} else {
				// Don't reveal if email exists for security
				$message = 'If an account exists with this email, you will receive a password reset link shortly.';
				$messageType = 'success';
			}
		} catch (Exception $e) {
			$message = 'Error: ' . $e->getMessage();
			$messageType = 'error';
		}
	}
}

// Step 2: Reset password with token
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 2) {
	$token = trim($_POST['token'] ?? '');
	$password = $_POST['password'] ?? '';
	$confirm_pass = $_POST['confirm_password'] ?? '';
	
	if ($password === '' || $confirm_pass === '') {
		$message = 'Please fill in all password fields.';
		$messageType = 'error';
	} elseif (strlen($password) < 6) {
		$message = 'Password must be at least 6 characters long.';
		$messageType = 'error';
	} elseif ($password !== $confirm_pass) {
		$message = 'Passwords do not match.';
		$messageType = 'error';
	} else {
		try {
			// Validate token
			$stmt = $pdo->prepare('SELECT * FROM login WHERE reset_token = :token AND reset_expiry > NOW()');
			$stmt->execute([':token' => $token]);
			$user = $stmt->fetch();
			
			if ($user) {
				// Update password
				$newHash = password_hash($password, PASSWORD_DEFAULT);
				$stmt = $pdo->prepare('UPDATE login SET password = :password, reset_token = NULL, reset_expiry = NULL WHERE id = :id');
				$stmt->execute([':password' => $newHash, ':id' => $user['id']]);
				
				$message = 'Password reset successful! Redirecting to login...';
				$messageType = 'success';
				echo '<meta http-equiv="refresh" content="2;url=login.php">';
			} else {
				$message = 'Invalid or expired reset token.';
				$messageType = 'error';
			}
		} catch (Exception $e) {
			$message = 'Error: ' . $e->getMessage();
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
	<title>SiteWorx Admin - Reset Password</title>
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

		.reset-container {
			width: 100%;
			max-width: 450px;
			padding: 0 15px;
		}

		.reset-card {
			background: white;
			border-radius: 10px;
			box-shadow: 0 10px 50px rgba(0, 0, 0, 0.2);
			overflow: hidden;
		}

		.reset-header {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			padding: 40px 20px;
			text-align: center;
		}

		.reset-header h1 {
			font-size: 28px;
			font-weight: 700;
			margin: 0;
			margin-bottom: 5px;
		}

		.reset-header p {
			margin: 0;
			font-size: 14px;
			opacity: 0.9;
		}

		.reset-body {
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

		.btn-reset {
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

		.btn-reset:hover {
			transform: translateY(-2px);
			box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
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

		.alert-info {
			background-color: #eef;
			color: #33c;
		}

		.reset-footer {
			padding: 20px 40px;
			background-color: #f8f9fa;
			text-align: center;
			border-top: 1px solid #e0e0e0;
		}

		.reset-footer a {
			color: #667eea;
			text-decoration: none;
			font-weight: 600;
		}

		.reset-footer a:hover {
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

		.info-box {
			background-color: #e7f3ff;
			border-left: 4px solid #2196F3;
			padding: 12px 15px;
			border-radius: 4px;
			font-size: 13px;
			color: #0c5aa0;
			margin-bottom: 20px;
		}
	</style>
</head>
<body>
	<div class="reset-container">
		<div class="reset-card">
			<div class="reset-header">
				<h1><i class="fas fa-cube"></i> SiteWorx</h1>
				<p>Reset Your Password</p>
			</div>

			<div class="reset-body">
				<?php if (!empty($message)): ?>
					<div class="alert alert-<?php echo $messageType; ?>" role="alert">
						<i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : ($messageType === 'error' ? 'exclamation-circle' : 'info-circle'); ?>"></i> 
						<?php echo $message; ?>
					</div>
				<?php endif; ?>

				<?php if ($step == 1): ?>
					<div class="info-box">
						<i class="fas fa-info-circle"></i> Enter your email address and we'll send you a password reset link.
					</div>

					<form method="post" action="">
						<div class="form-group">
							<label for="email" class="form-label">Email Address</label>
							<div class="input-icon">
								<i class="fas fa-envelope"></i>
								<input 
									type="email" 
									class="form-control" 
									id="email" 
									name="email" 
									placeholder="Enter your registered email" 
									required 
									autofocus
								>
							</div>
						</div>

						<button type="submit" class="btn btn-reset">
							<i class="fas fa-paper-plane"></i> Send Reset Link
						</button>
					</form>

				<?php elseif ($step == 2 && !empty($resetToken)): ?>
					<div class="info-box">
						<i class="fas fa-info-circle"></i> Enter your new password below.
					</div>

					<form method="post" action="">
						<input type="hidden" name="token" value="<?php echo htmlspecialchars($resetToken); ?>">

						<div class="form-group">
							<label for="password" class="form-label">New Password</label>
							<div class="input-icon">
								<i class="fas fa-lock"></i>
								<input 
									type="password" 
									class="form-control" 
									id="password" 
									name="password" 
									placeholder="Enter new password (min. 6 characters)" 
									required 
									minlength="6"
									autofocus
								>
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
									minlength="6"
								>
							</div>
						</div>

						<button type="submit" class="btn btn-reset">
							<i class="fas fa-key"></i> Reset Password
						</button>
					</form>

				<?php else: ?>
					<div class="alert alert-danger" role="alert">
						<i class="fas fa-exclamation-circle"></i> Invalid or expired reset link.
					</div>
				<?php endif; ?>
			</div>

			<div class="reset-footer">
				<p>
					<a href="login.php">
						<i class="fas fa-arrow-left"></i> Back to Login
					</a>
				</p>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
