<?php
// Make sure session is destroyed
session_start();
session_destroy();
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SiteWorx Admin - Logged Out</title>
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
		}

		.logout-container {
			width: 100%;
			max-width: 500px;
			padding: 0 15px;
		}

		.logout-card {
			background: white;
			border-radius: 10px;
			box-shadow: 0 10px 50px rgba(0, 0, 0, 0.2);
			overflow: hidden;
			text-align: center;
		}

		.logout-body {
			padding: 60px 40px;
		}

		.logout-icon {
			font-size: 60px;
			color: #28a745;
			margin-bottom: 20px;
			animation: slideDown 0.6s ease;
		}

		@keyframes slideDown {
			from {
				transform: translateY(-30px);
				opacity: 0;
			}
			to {
				transform: translateY(0);
				opacity: 1;
			}
		}

		.logout-title {
			font-size: 28px;
			font-weight: 700;
			color: #333;
			margin-bottom: 10px;
		}

		.logout-subtitle {
			font-size: 16px;
			color: #666;
			margin-bottom: 30px;
			line-height: 1.6;
		}

		.btn-group-custom {
			display: flex;
			gap: 10px;
			flex-wrap: wrap;
			justify-content: center;
		}

		.btn-custom {
			flex: 1;
			min-width: 120px;
			padding: 12px 20px;
			font-size: 15px;
			font-weight: 600;
			border-radius: 6px;
			text-decoration: none;
			transition: all 0.3s ease;
			border: none;
			cursor: pointer;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			gap: 8px;
		}

		.btn-primary-custom {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
		}

		.btn-primary-custom:hover {
			transform: translateY(-2px);
			box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
			color: white;
			text-decoration: none;
		}

		.btn-secondary-custom {
			background: #f0f0f0;
			color: #333;
			border: 2px solid #e0e0e0;
		}

		.btn-secondary-custom:hover {
			background: #e0e0e0;
			color: #333;
			text-decoration: none;
		}

		.logout-footer {
			background-color: #f8f9fa;
			padding: 20px 40px;
			border-top: 1px solid #e0e0e0;
			font-size: 14px;
			color: #666;
		}

		.info-text {
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
	<div class="logout-container">
		<div class="logout-card">
			<div class="logout-body">
				<div class="logout-icon">
					<i class="fas fa-check-circle"></i>
				</div>
				<h1 class="logout-title">Successfully Logged Out</h1>
				<p class="logout-subtitle">
					You have been successfully logged out of your SiteWorx Admin account.
					Your session has been securely terminated.
				</p>

				<div class="info-text">
					<i class="fas fa-info-circle"></i> Your session data has been cleared for security.
				</div>

				<div class="btn-group-custom">
					<a href="login.php" class="btn-custom btn-primary-custom">
						<i class="fas fa-sign-in-alt"></i> Login Again
					</a>
					<a href="/" class="btn-custom btn-secondary-custom">
						<i class="fas fa-home"></i> Home
					</a>
				</div>
			</div>

			<div class="logout-footer">
				<p style="margin: 0;">
					<i class="fas fa-shield-alt"></i> For security, please close your browser if you're using a shared computer.
				</p>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		// Prevent browser back button from accessing logged-out session
		window.history.forward();
		function noBack() {
			window.history.forward();
		}
	</script>
</body>
</html>
