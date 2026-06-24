<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: view.php');
    exit;
}

$user = current_user($pdo);
$isAdmin = (get_user_role($pdo, $user) === 'admin');

$stmt = $pdo->prepare('SELECT login_id FROM products WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$row = $stmt->fetch();
if (!$row) {
    header('Location: view.php');
    exit;
}
if (!$isAdmin && (int)$row['login_id'] !== (int)$_SESSION['id']) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$del = $pdo->prepare('DELETE FROM products WHERE id = :id');
$del->execute([':id' => $id]);
header('Location: view.php');
exit;
