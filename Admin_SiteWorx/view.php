<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_login();

// fetch products for logged-in user
$stmt = $pdo->prepare('SELECT * FROM products WHERE login_id = :id ORDER BY id DESC');
$stmt->execute([':id' => $_SESSION['id']]);
$rows = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<title>Homepage</title>
</head>
<body>
	<a href="index">Home</a> | <a href="add.html">Add New Data</a> | <a href="logout">Logout</a>
	<br/><br/>

	<table class="table" style="width:80%">
		<thead class="table-secondary">
			<tr>
				<th>Name</th>
				<th>Quantity</th>
				<th>Price</th>
				<th>Update</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($rows as $res): ?>
			<tr>
				<td><?php echo htmlspecialchars($res['name']); ?></td>
				<td><?php echo htmlspecialchars($res['qty']); ?></td>
				<td><?php echo htmlspecialchars($res['price']); ?></td>
				<td>
					<a href="edit?id=<?php echo $res['id']; ?>">Edit</a> |
					<a href="delete?id=<?php echo $res['id']; ?>" onclick="return confirm('Are you sure you want to delete?')">Delete</a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</body>
</html>
