<?php
ob_start();
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// no output before this PHP tag — ensures headers/session can start
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/lib_auth.php';
require_once __DIR__ . '/lib_admin.php';

if (empty($_SESSION['valid'])) {
	header('Location: login.php');
	exit;
}

$error = null;
try {
	$user = current_user($pdo);
	$role = get_user_role($pdo, $user);
	
	// Store role in session for use in _header.php
	$_SESSION['role'] = $role;
	
	if ($role === 'client') {
		header('Location: my_services.php');
		exit;
	}

	// gather counts
	[$userWhere, $userParams] = sw_manager_user_filter_sql($role, (int)$user['id'], 'u');
	$countUsers = $pdo->prepare("SELECT COUNT(*) FROM login u WHERE {$userWhere}");
	$countUsers->execute($userParams);
	$totalUsers = $countUsers->fetchColumn();
	$totalPlans = $pdo->query('SELECT COUNT(*) FROM hosting_plans')->fetchColumn();
	$totalServices = $pdo->query('SELECT COUNT(*) FROM service_catalog')->fetchColumn();
	$subs = $pdo->prepare("SELECT COUNT(*) FROM subscriptions s JOIN login u ON u.id = s.user_id WHERE {$userWhere}");
	$subs->execute($userParams);
	$totalSubscriptions = $subs->fetchColumn();

	// fetch users
	$userStmt = $pdo->prepare("SELECT u.id,u.name,u.username,u.email,r.name AS role_name FROM login u LEFT JOIN roles r ON r.id = u.role_id WHERE {$userWhere} ORDER BY u.id DESC LIMIT 200");
	$userStmt->execute($userParams);
	$users = $userStmt->fetchAll();
	// fetch plans
	$plans = $pdo->query("SELECT id,name,slug,category,price_monthly,currency FROM hosting_plans WHERE status='active' ORDER BY category, price_monthly")->fetchAll();
	$services = $pdo->query("SELECT id,name,type,price FROM service_catalog WHERE status='active' ORDER BY type,name")->fetchAll();
	$servers = $pdo->query("SELECT id,hostname,region,ip_address FROM servers WHERE status='active' ORDER BY region,hostname")->fetchAll();

} catch (Exception $e) {
	$error = $e->getMessage();
	$users = $services = $servers = [];
	$plans = [];
	$totalUsers = $totalPlans = $totalServices = $totalSubscriptions = 0;
}

?>
<?php include '_header.php'; ?>
<div class="row g-3">

<?php if (!empty($error)): ?>
	<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="row g-3">
	<div class="col-md-3">
		<div class="card p-3 card-stat">
			<div class="text-muted">Users</div>
			<h3><?php echo $totalUsers; ?></h3>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card p-3 card-stat">
			<div class="text-muted">Plans</div>
			<h3><?php echo $totalPlans; ?></h3>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card p-3 card-stat">
			<div class="text-muted">Services</div>
			<h3><?php echo $totalServices; ?></h3>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card p-3 card-stat">
			<div class="text-muted">Subscriptions</div>
			<h3><?php echo $totalSubscriptions; ?></h3>
		</div>
	</div>
</div>

<div class="row mt-4">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<strong><?php echo $role === 'manager' ? 'My Users' : 'User List'; ?></strong>
				<div>
					<a class="btn btn-sm btn-primary" href="create_user.php">Create User</a>
					<a class="btn btn-sm btn-secondary" href="manage_plans.php">Manage Plans</a>
				</div>
			</div>
			<div class="card-body p-0">
				<table class="table table-striped user-table mb-0">
					<thead><tr><th>#</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Assign</th></tr></thead>
					<tbody>
					<?php foreach ($users as $u): ?>
						<tr>
							<td><?php echo $u['id']; ?></td>
							<td><?php echo htmlspecialchars($u['name']); ?></td>
							<td><?php echo htmlspecialchars($u['username']); ?></td>
							<td><?php echo htmlspecialchars($u['email']); ?></td>
							<td><?php echo htmlspecialchars($u['role_name'] ?? 'client'); ?></td>
							<td><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal" data-userid="<?php echo $u['id']; ?>" data-username="<?php echo htmlspecialchars($u['username']); ?>">Assign Service</button></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card mb-3">
			<div class="card-header"><strong>Available Plans</strong></div>
			<div class="card-body">
				<ul class="list-group">
					<?php foreach ($plans as $p): ?>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<div>
								<strong><?php echo htmlspecialchars($p['name']); ?></strong><br>
								<small class="text-muted"><?php echo htmlspecialchars($p['category']); ?></small>
							</div>
							<span class="badge bg-primary"><?php echo sw_format_money($p['price_monthly'], $p['currency'] ?? 'INR'); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="card">
			<div class="card-header"><strong>Quick Actions</strong></div>
			<div class="card-body">
				<a href="<?php echo $base_url; ?>manage_plans.php" class="btn btn-sm btn-outline-primary mb-2">Create Plan</a>
				<a href="<?php echo $base_url; ?>services.php" class="btn btn-sm btn-outline-secondary mb-2">Manage Services</a>
				<a href="<?php echo $base_url; ?>servers.php" class="btn btn-sm btn-outline-secondary mb-2">Manage Servers</a>
			</div>
		</div>
	</div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" action="assign_service">
			<div class="modal-header"><h5 class="modal-title">Assign Service to <span id="modal-username"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
			<div class="modal-body">
				<input type="hidden" name="user_id" id="modal-userid">
				<div class="mb-2">
					<label class="w-100">Plan
						<select name="item_id" class="form-select">
							<?php foreach ($plans as $p): ?>
								<option value="plan:<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?> - <?php echo sw_format_money($p['price_monthly'], $p['currency'] ?? 'INR'); ?></option>
							<?php endforeach; ?>
							<?php foreach ($services as $s): ?>
								<option value="service:<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?> - <?php echo sw_format_money($s['price']); ?></option>
							<?php endforeach; ?>
							<?php foreach ($servers as $srv): ?>
								<option value="server:<?php echo $srv['id']; ?>">Server: <?php echo htmlspecialchars($srv['hostname']); ?> <?php echo htmlspecialchars($srv['ip_address'] ? '(' . $srv['ip_address'] . ')' : ''); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>
				<div class="mb-2"><label>Period (months)<br><input name="period_months" value="1" class="form-control"></label></div>
			<div class="col-md-2">
    <label class="form-label">Quantity</label>
    <input type="number" min="1" name="quantity" value="1" class="form-control" required>
</div>
			</div>
			<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button class="btn btn-primary">Assign</button></div>
			</form>
		</div>
	</div>
</div>

<script>
var assignModal = document.getElementById('assignModal');
assignModal.addEventListener('show.bs.modal', function (event) {
	var button = event.relatedTarget;
	var userid = button.getAttribute('data-userid');
	var username = button.getAttribute('data-username');
	document.getElementById('modal-userid').value = userid;
	document.getElementById('modal-username').textContent = username;
});
</script>

<?php include '_footer.php';

ob_end_flush();
?>

