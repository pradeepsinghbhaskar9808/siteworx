<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$base_url = $base_url ?? '';

// If role not in session, try to get it from database (fallback for existing pages)
if (empty($_SESSION['role']) && !empty($_SESSION['id'])) {
    require_once __DIR__ . '/connection.php';
    require_once __DIR__ . '/lib_auth.php';
    try {
        $user = current_user($pdo);
        if ($user) {
            $role = get_user_role($pdo, $user);
            $_SESSION['role'] = $role ?? 'client';
        }
    } catch (Exception $e) {
        $_SESSION['role'] = 'client';
    }
}

// Get current user role for menu filtering
$current_role = $_SESSION['role'] ?? 'client';

// Define menu items with role restrictions
// Format: 'label' => ['url' => 'path', 'roles' => ['admin', 'manager', 'client']]
$menu_items = [
    'Dashboard' => ['url' => 'index.php', 'roles' => ['admin', 'manager']],
    'Plans' => ['url' => 'manage_plans.php', 'roles' => ['admin', 'manager']],
    'Services' => ['url' => 'services.php', 'roles' => ['admin', 'manager']],
    'Servers' => ['url' => 'servers.php', 'roles' => ['admin']],
    'Users' => ['url' => 'users.php', 'roles' => ['admin', 'manager']],
    'My Services' => ['url' => 'my_services.php', 'roles' => ['admin', 'manager', 'client']],
    'Orders' => ['url' => 'orders.php', 'roles' => ['admin', 'manager', 'client']],
];
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
        <?php foreach ($menu_items as $label => $item): ?>
            <?php if (in_array($current_role, $item['roles'])): ?>
                <a class="nav-link text-white" href="<?php echo htmlspecialchars($item['url']); ?>">
                    <?php echo htmlspecialchars($label); ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
      </nav>
    </div>
    <div>
      <?php if (!empty($_SESSION['name'])): ?>
        <span class="me-3">
          <?php echo htmlspecialchars($_SESSION['name']); ?> 
          <small class="text-muted">(<?php echo htmlspecialchars($current_role); ?>)</small>
        </span>
        <a class="btn btn-sm btn-outline-light me-2" href="change_password.php">Password</a>
        <a class="btn btn-sm btn-light" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-sm btn-light" href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class="container my-4">
