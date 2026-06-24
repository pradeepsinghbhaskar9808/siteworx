<?php
session_start();

// Store user info before destroying session
$username = $_SESSION['name'] ?? 'User';

// Clear all session variables
$_SESSION = [];

// Delete session cookie if it exists
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(
		session_name(), 
		'', 
		time() - 42000,
		$params['path'], 
		$params['domain'], 
		$params['secure'], 
		$params['httponly']
	);
}

// Clear remember-me cookie if it exists
setcookie('siteworx_user', '', time() - 42000, '/');

// Destroy the session
session_destroy();

// Redirect to logout success page
header('Location: logout_success.php');
exit;
?>
