<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: view'); exit; }

$user = current_user($pdo);
$isAdmin = (get_user_role($pdo, $user) === 'admin');

// If not admin, ensure ownership
$stmt = $pdo->prepare('SELECT login_id FROM products WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$row = $stmt->fetch();
if (!$row) { header('Location: view'); exit; }
if (!$isAdmin && $row['login_id'] != $_SESSION['id']) { http_response_code(403); echo 'Forbidden'; exit; }

$del = $pdo->prepare('DELETE FROM products WHERE id = :id');
$del->execute([':id' => $id]);
header('Location: view');
exit;
<?php session_start(); ?>

<?php
if(!isset($_SESSION['valid'])) {
	header('Location: login');
}
?>

<?php
//including the database connection file
include("connection");

//getting id of the data from url
$id = $_GET['id'];

//deleting the row from table
$result=mysqli_query($mysqli, "DELETE FROM products WHERE id=$id");

//redirecting to the display page (view in our case)
header("Location:view");
?>

