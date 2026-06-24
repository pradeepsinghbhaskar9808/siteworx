<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_admin.php';
require_role($pdo, ['admin','manager']);

// simple create/edit handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $category = trim($_POST['category'] ?? 'shared');
    $price = floatval($_POST['price'] ?? 0);
    $currency = trim($_POST['currency'] ?? 'INR');
    $specs = $_POST['specs'] ?? '{}';
    $status = trim($_POST['status'] ?? 'active');
    $id = (int)($_POST['id'] ?? 0);
    $specsJson = sw_json_or_error($specs, $msg);

    if (empty($name) || empty($slug)) {
        $msg = 'Name and slug are required.';
    } elseif ($specsJson === false) {
        // message already set
    } else {
        if ($id > 0) {
            $up = $pdo->prepare('UPDATE hosting_plans SET name=:name, slug=:slug, category=:cat, specs=:specs, price_monthly=:price, currency=:currency, status=:status WHERE id=:id');
            $up->execute([':name'=>$name,':slug'=>$slug,':cat'=>$category,':specs'=>$specsJson,':price'=>$price,':currency'=>$currency,':status'=>$status,':id'=>$id]);
            $msg = 'Updated plan.';
        } else {
            $ins = $pdo->prepare('INSERT INTO hosting_plans (name,slug,category,specs,price_monthly,currency,status) VALUES (:name,:slug,:cat,:specs,:price,:currency,:status)');
            $ins->execute([':name'=>$name,':slug'=>$slug,':cat'=>$category,':specs'=>$specsJson,':price'=>$price,':currency'=>$currency,':status'=>$status]);
            $msg = 'Created plan.';
        }
    }
}

$editPlan = null;
if (!empty($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM hosting_plans WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => (int)$_GET['edit']]);
    $editPlan = $stmt->fetch();
}
$planCategories = [
    'Hosting' => [
        'shared-linux' => 'Shared Hosting - Linux',
        'shared-windows' => 'Shared Hosting - Windows',
        'reseller-linux' => 'Reseller Hosting - Linux',
        'reseller-windows' => 'Reseller Hosting - Windows',
    ],
    'Servers' => [
        'vps-linux' => 'VPS Server - Linux',
        'vps-windows' => 'VPS Server - Windows',
        'cloud-linux' => 'Cloud Server - Linux',
        'cloud-windows' => 'Cloud Server - Windows',
        'dedicated-linux' => 'Dedicated Server - Linux',
        'dedicated-windows' => 'Dedicated Server - Windows',
    ],
    'Other' => [
        'email-marketing' => 'Email Marketing',
        'gsuite' => 'Google Workspace',
    ],
];
$currentCategory = $editPlan['category'] ?? 'shared-linux';
$knownCategory = false;
foreach ($planCategories as $categoryGroup) {
    if (array_key_exists($currentCategory, $categoryGroup)) {
        $knownCategory = true;
        break;
    }
}

// Get filter category from URL or session
$filterCategory = $_GET['filter_category'] ?? null;

// Build query with optional category filter
if (!empty($filterCategory)) {
    $stmt = $pdo->prepare('SELECT * FROM hosting_plans WHERE category = :cat ORDER BY price_monthly');
    $stmt->execute([':cat' => $filterCategory]);
    $plans = $stmt->fetchAll();
} else {
    $plans = $pdo->query('SELECT * FROM hosting_plans ORDER BY category, price_monthly')->fetchAll();
}
?>
<?php include '_header.php'; ?>
    <h1>Manage Plans</h1>
    <?php if (!empty($msg)): ?><div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <form method="post" class="mb-4">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($editPlan['id'] ?? ''); ?>">
        <div class="row">
            <div class="col-md-3"><label>Name<br><input class="form-control" name="name" value="<?php echo htmlspecialchars($editPlan['name'] ?? ''); ?>"></label></div>
            <div class="col-md-2"><label>Slug<br><input class="form-control" name="slug" value="<?php echo htmlspecialchars($editPlan['slug'] ?? ''); ?>"></label></div>
            <div class="col-md-2"><label>Category<br>
                <select class="form-select" name="category">
                    <?php foreach ($planCategories as $groupName => $categoryGroup): ?>
                        <optgroup label="<?php echo htmlspecialchars($groupName); ?>">
                            <?php foreach ($categoryGroup as $value => $label): ?>
                                <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $currentCategory === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                    <?php if (!$knownCategory): ?>
                        <option value="<?php echo htmlspecialchars($currentCategory); ?>" selected><?php echo htmlspecialchars($currentCategory); ?></option>
                    <?php endif; ?>
                </select>
            </label></div>
            <div class="col-md-2"><label>Price<br><input class="form-control" name="price" value="<?php echo htmlspecialchars($editPlan['price_monthly'] ?? '0'); ?>"></label></div>
            <div class="col-md-1"><label>Currency<br><input class="form-control" name="currency" value="<?php echo htmlspecialchars($editPlan['currency'] ?? 'INR'); ?>"></label></div>
            <div class="col-md-2"><label>Status<br><select class="form-select" name="status"><?php $currentStatus = $editPlan['status'] ?? 'active'; ?><option value="active" <?php echo $currentStatus === 'active' ? 'selected' : ''; ?>>active</option><option value="draft" <?php echo $currentStatus === 'draft' ? 'selected' : ''; ?>>draft</option><option value="archived" <?php echo $currentStatus === 'archived' ? 'selected' : ''; ?>>archived</option></select></label></div>
        </div>
        <div class="mt-2"><label class="w-100">Specs (JSON)<br><textarea class="form-control" name="specs" rows="4"><?php echo htmlspecialchars($editPlan['specs'] ?? '{"websites":1,"disk":"2GB","bandwidth":"5GB","emails":5,"subdomains":2,"databases":1}'); ?></textarea></label></div>
        <div class="mt-2"><button class="btn btn-primary">Save</button></div>
    </form>

    <h2>Existing Plans</h2>
    
    <!-- Category Filter -->
    <div class="mb-3">
        <label for="categoryFilter"><strong>Filter by Category:</strong></label>
        <select id="categoryFilter" class="form-select" style="max-width: 300px;">
            <option value="">All Categories</option>
            <?php foreach ($planCategories as $groupName => $categoryGroup): ?>
                <optgroup label="<?php echo htmlspecialchars($groupName); ?>">
                    <?php foreach ($categoryGroup as $value => $label): ?>
                        <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $filterCategory === $value ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
    </div>
    
    <script>
        document.getElementById('categoryFilter').addEventListener('change', function() {
            const category = this.value;
            if (category === '') {
                window.location.href = 'manage_plans.php';
            } else {
                window.location.href = 'manage_plans.php?filter_category=' + encodeURIComponent(category);
            }
        });
    </script>
    
    <table class="table">
        <thead><tr><th>Id</th><th>Name</th><th>Slug</th><th>Category</th><th>Price</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($plans as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['slug']); ?></td>
                <td><?php echo htmlspecialchars($p['category']); ?></td>
                <td><?php echo sw_format_money($p['price_monthly'], $p['currency'] ?? 'INR'); ?></td>
                <td><a class="btn btn-sm btn-secondary" href="manage_plans?edit=<?php echo $p['id']; ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php include '_footer.php'; ?>
