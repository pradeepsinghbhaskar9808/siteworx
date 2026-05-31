<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_login();

if (isset($_POST['Submit'])) {
	$name = trim($_POST['name'] ?? '');
	$qty = trim($_POST['qty'] ?? '');
	$price = trim($_POST['price'] ?? '');
	$loginId = $_SESSION['id'];

	$errors = [];
	if ($name === '') $errors[] = 'Name field is empty.';
	if ($qty === '') $errors[] = 'Quantity field is empty.';
	if ($price === '') $errors[] = 'Price field is empty.';

	if ($errors) {
		foreach ($errors as $err) {
			echo "<div style=\"color:red\">" . htmlspecialchars($err) . "</div>";
		}
		echo "<br/><a href='javascript:self.history.back();'>Go Back</a>";
	} else {
		$stmt = $pdo->prepare('INSERT INTO products (name, qty, price, login_id) VALUES (:name, :qty, :price, :login_id)');
		$stmt->execute([
			':name' => $name,
			':qty' => $qty,
			':price' => $price,
			':login_id' => $loginId
		]);

		echo "<div style=\"color:green\">Data added successfully.</div>";
		echo "<br/><a href='view'>View Result</a>";
	}
}
?>
