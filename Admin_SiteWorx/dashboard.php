<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_login();

$user = current_user($pdo);
$role = get_user_role($pdo, $user);

// totals
$totalUsers = $pdo->query('SELECT COUNT(*) FROM login')->fetchColumn();
$totalProducts = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$myProducts = 0;
if (!empty($_SESSION['id'])) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE login_id = :id');
    $stmt->execute([':id' => $_SESSION['id']]);
    $myProducts = $stmt->fetchColumn();
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h1>Dashboard</h1>
    <p>Welcome <?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?> — Role: <?php echo htmlspecialchars($role); ?></p>
    <div class="row">
        <div class="col-md-3"><div class="card p-3">Total users: <strong><?php echo $totalUsers; ?></strong></div></div>
        <div class="col-md-3"><div class="card p-3">Total products: <strong><?php echo $totalProducts; ?></strong></div></div>
        <div class="col-md-3"><div class="card p-3">My products: <strong><?php echo $myProducts; ?></strong></div></div>
    </div>
    <div class="mt-3">
        <a class="btn btn-primary" href="view">Manage Products</a>
        <a class="btn btn-secondary" href="logout">Logout</a>
    </div>
</body>
</html>
