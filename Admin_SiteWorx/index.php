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

if (empty($_SESSION['valid'])) {
	header('Location: login');
	exit;
}

$error = null;
try {
	$user = current_user($pdo);
	$role = get_user_role($pdo, $user);

	// gather counts
	$totalUsers = $pdo->query('SELECT COUNT(*) FROM login')->fetchColumn();
	$totalPlans = $pdo->query('SELECT COUNT(*) FROM hosting_plans')->fetchColumn();
	$totalProducts = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
	$totalSubscriptions = 0;
	try { $totalSubscriptions = $pdo->query('SELECT COUNT(*) FROM subscriptions')->fetchColumn(); } catch (Exception $e) { $totalSubscriptions = 0; }

	// fetch users
	$users = $pdo->query('SELECT id,name,username,email FROM login ORDER BY id DESC LIMIT 200')->fetchAll();
	// fetch plans
	$plans = $pdo->query("SELECT id,name,slug,category,price_monthly FROM hosting_plans WHERE status='active' ORDER BY category, price_monthly")->fetchAll();

} catch (Exception $e) {
	$error = $e->getMessage();
	$users = [];
	$plans = [];
	$totalUsers = $totalPlans = $totalProducts = $totalSubscriptions = 0;
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
			<div class="text-muted">Products</div>
			<h3><?php echo $totalProducts; ?></h3>
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
				<strong>User List</strong>
				<div>
					<a class="btn btn-sm btn-secondary" href="manage_plans.php">Manage Plans</a>
				</div>
			</div>
			<div class="card-body p-0">
				<table class="table table-striped user-table mb-0">
					<thead><tr><th>#</th><th>Name</th><th>Username</th><th>Email</th><th>Assign</th></tr></thead>
					<tbody>
					<?php foreach ($users as $u): ?>
						<tr>
							<td><?php echo $u['id']; ?></td>
							<td><?php echo htmlspecialchars($u['name']); ?></td>
							<td><?php echo htmlspecialchars($u['username']); ?></td>
							<td><?php echo htmlspecialchars($u['email']); ?></td>
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
							<span class="badge bg-primary"><?php echo number_format($p['price_monthly'],2); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="card">
			<div class="card-header"><strong>Quick Actions</strong></div>
			<div class="card-body">
				<a href="manage_plans" class="btn btn-sm btn-outline-primary mb-2">Create Plan</a>
				<a href="view" class="btn btn-sm btn-outline-secondary mb-2">Manage Products</a>
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
					<label>Plan
						<select name="plan_id" class="form-select">
							<?php foreach ($plans as $p): ?>
								<option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?> - <?php echo number_format($p['price_monthly'],2); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>
				<div class="mb-2"><label>Period (months)<br><input name="period_months" value="1" class="form-control"></label></div>
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

