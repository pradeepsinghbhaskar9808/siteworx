<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_role($pdo, ['admin','manager']);

// simple create/edit handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $category = trim($_POST['category'] ?? 'shared');
    $price = floatval($_POST['price'] ?? 0);
    $currency = trim($_POST['currency'] ?? 'INR');
    $specs = $_POST['specs'] ?? '{}';

    if (empty($name) || empty($slug)) {
        $msg = 'Name and slug are required.';
    } else {
        // upsert by slug
        $stmt = $pdo->prepare('SELECT id FROM hosting_plans WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug'=>$slug]);
        $row = $stmt->fetch();
        if ($row) {
            $up = $pdo->prepare('UPDATE hosting_plans SET name=:name, category=:cat, specs=:specs, price_monthly=:price, currency=:currency WHERE id=:id');
            $up->execute([':name'=>$name,':cat'=>$category,':specs'=>$specs,':price'=>$price,':currency'=>$currency,':id'=>$row['id']]);
            $msg = 'Updated plan.';
        } else {
            $ins = $pdo->prepare('INSERT INTO hosting_plans (name,slug,category,specs,price_monthly,currency,status) VALUES (:name,:slug,:cat,:specs,:price,:currency,:status)');
            $ins->execute([':name'=>$name,':slug'=>$slug,':cat'=>$category,':specs'=>$specs,':price'=>$price,':currency'=>$currency,':status'=>'active']);
            $msg = 'Created plan.';
        }
    }
}

$plans = $pdo->query('SELECT * FROM hosting_plans ORDER BY category, price_monthly')->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Plans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h1>Manage Plans</h1>
    <?php if (!empty($msg)): ?><div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <form method="post" class="mb-4">
        <div class="row">
            <div class="col-md-4"><label>Name<br><input class="form-control" name="name"></label></div>
            <div class="col-md-2"><label>Slug<br><input class="form-control" name="slug"></label></div>
            <div class="col-md-2"><label>Category<br><input class="form-control" name="category" value="shared"></label></div>
            <div class="col-md-2"><label>Price<br><input class="form-control" name="price" value="0"></label></div>
            <div class="col-md-2"><label>Currency<br><input class="form-control" name="currency" value="INR"></label></div>
        </div>
        <div class="mt-2"><label>Specs (JSON)<br><textarea class="form-control" name="specs" rows="4">{"websites":1,"disk":"2GB","bandwidth":"5GB","emails":5,"subdomains":2,"databases":1}</textarea></label></div>
        <div class="mt-2"><button class="btn btn-primary">Save</button></div>
    </form>

    <h2>Existing Plans</h2>
    <table class="table">
        <thead><tr><th>Id</th><th>Name</th><th>Slug</th><th>Category</th><th>Price</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($plans as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['slug']); ?></td>
                <td><?php echo htmlspecialchars($p['category']); ?></td>
                <td><?php echo htmlspecialchars($p['price_monthly']); ?></td>
                <td><a class="btn btn-sm btn-secondary" href="manage_plans?edit=<?php echo $p['id']; ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
