<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: view'); exit;
}

$user = current_user($pdo);
$isAdmin = (get_user_role($pdo, $user) === 'admin');

// fetch product and check ownership
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();
if (!$product) { header('Location: view'); exit; }
if (!$isAdmin && $product['login_id'] != $_SESSION['id']) { http_response_code(403); echo 'Forbidden'; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $qty = trim($_POST['qty'] ?? '');
    $price = trim($_POST['price'] ?? '');
    if ($name === '' || $qty === '' || $price === '') {
        $error = 'All fields are required.';
    } else {
        $up = $pdo->prepare('UPDATE products SET name = :name, qty = :qty, price = :price WHERE id = :id');
        $up->execute([':name'=>$name, ':qty'=>$qty, ':price'=>$price, ':id'=>$id]);
        header('Location: view'); exit;
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <a href="view">Back</a>
    <h2>Edit Product</h2>
    <?php if (!empty($error)): ?><div style="color:red"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-2"><label>Name<br><input class="form-control" name="name" value="<?php echo htmlspecialchars($product['name']); ?>"></label></div>
        <div class="mb-2"><label>Quantity<br><input class="form-control" name="qty" value="<?php echo htmlspecialchars($product['qty']); ?>"></label></div>
        <div class="mb-2"><label>Price<br><input class="form-control" name="price" value="<?php echo htmlspecialchars($product['price']); ?>"></label></div>
        <button class="btn btn-primary" type="submit">Save</button>
    </form>
</body>
</html>
<?php session_start(); ?>

<?php
if(!isset($_SESSION['valid'])) {
	header('Location: login');
}
?>

<?php
// including the database connection file
include_once("connection");

if(isset($_POST['update']))
{	
	$id = $_POST['id'];
	
	$name = $_POST['name'];
	$qty = $_POST['qty'];
	$price = $_POST['price'];	
	
	// checking empty fields
	if(empty($name) || empty($qty) || empty($price)) {
				
		if(empty($name)) {
			echo "<font color='red'>Name field is empty.</font><br/>";
		}
		
		if(empty($qty)) {
			echo "<font color='red'>Quantity field is empty.</font><br/>";
		}
		
		if(empty($price)) {
			echo "<font color='red'>Price field is empty.</font><br/>";
		}		
	} else {	
		//updating the table
		$result = mysqli_query($mysqli, "UPDATE products SET name='$name', qty='$qty', price='$price' WHERE id=$id");
		
		//redirectig to the display page. In our case, it is view
		header("Location: view");
	}
}
?>
<?php
//getting id from url
$id = $_GET['id'];

//selecting data associated with this particular id
$result = mysqli_query($mysqli, "SELECT * FROM products WHERE id=$id");

while($res = mysqli_fetch_array($result))
{
	$name = $res['name'];
	$qty = $res['qty'];
	$price = $res['price'];
}
?>
<html>
<head>	
	<title>Edit Data</title>
</head>

<body>
	<a href="index">Home</a> | <a href="view">View Products</a> | <a href="logout">Logout</a>
	<br/><br/>
	
	<form name="form1" method="post" action="edit">
		<table border="0">
			<tr> 
				<td>Name</td>
				<td><input type="text" name="name" value="<?php echo $name;?>"></td>
			</tr>
			<tr> 
				<td>Quantity</td>
				<td><input type="text" name="qty" value="<?php echo $qty;?>"></td>
			</tr>
			<tr> 
				<td>Price</td>
				<td><input type="text" name="price" value="<?php echo $price;?>"></td>
			</tr>
			<tr>
				<td><input type="hidden" name="id" value=<?php echo $_GET['id'];?>></td>
				<td><input type="submit" name="update" value="Update"></td>
			</tr>
		</table>
	</form>
</body>
</html>
