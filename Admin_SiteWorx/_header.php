<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SiteWorx Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style_admin.css" rel="stylesheet">
</head>
<body>
<header class="swx-header bg-primary text-white py-2">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
      <a class="navbar-brand text-white me-3" href="index.php">SiteWorx</a>
      <nav class="nav d-none d-md-flex">
        <a class="nav-link text-white" href="index.php">Dashboard</a>
        <a class="nav-link text-white" href="manage_plans.php">Plans</a>
        <a class="nav-link text-white" href="view.php">Products</a>
        <a class="nav-link text-white" href="create_user.php">Users</a>
        <a class="nav-link text-white" href="orders.php">Orders</a>
      </nav>
    </div>
    <div>
      <?php if (!empty($_SESSION['name'])): ?>
        <span class="me-3">Signed in as <?php echo htmlspecialchars($_SESSION['name']); ?></span>
        <a class="btn btn-sm btn-light" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-sm btn-light" href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class="container my-4">
