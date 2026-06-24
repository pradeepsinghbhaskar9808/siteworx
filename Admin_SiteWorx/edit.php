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

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();
if (!$product) {
    header('Location: view.php');
    exit;
}
if (!$isAdmin && (int)$product['login_id'] !== (int)$_SESSION['id']) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $qty = trim($_POST['qty'] ?? '');
    $price = trim($_POST['price'] ?? '');
    if ($name === '' || $qty === '' || $price === '') {
        $error = 'All fields are required.';
    } else {
        $up = $pdo->prepare('UPDATE products SET name = :name, qty = :qty, price = :price WHERE id = :id');
        $up->execute([':name'=>$name, ':qty'=>$qty, ':price'=>$price, ':id'=>$id]);
        header('Location: view.php');
        exit;
    }
}
?>
<?php include '_header.php'; ?>
<a href="view.php">Back</a>
<h2>Edit Product</h2>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<form method="post">
    <div class="mb-2"><label>Name<br><input class="form-control" name="name" value="<?php echo htmlspecialchars($product['name']); ?>"></label></div>
    <div class="mb-2"><label>Quantity<br><input class="form-control" name="qty" value="<?php echo htmlspecialchars($product['qty']); ?>"></label></div>
    <div class="mb-2"><label>Price<br><input class="form-control" name="price" value="<?php echo htmlspecialchars($product['price']); ?>"></label></div>
    <button class="btn btn-primary" type="submit">Save</button>
</form>
<?php include '_footer.php'; ?>
