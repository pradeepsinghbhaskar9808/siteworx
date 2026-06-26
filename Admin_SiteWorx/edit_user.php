<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_admin.php';

require_role($pdo, ['admin','manager']);

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM login WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die('User not found');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name         = trim($_POST['name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone        = trim($_POST['phone'] ?? ''); // Added phone variable
    $company_name = trim($_POST['company_name'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $city         = trim($_POST['city'] ?? '');
    $state        = trim($_POST['state'] ?? '');
    $pin_code     = trim($_POST['pin_code'] ?? '');
    $gst_number   = trim($_POST['gst_number'] ?? '');

    $sql = "
        UPDATE login
        SET
            name = :name,
            email = :email,
            phone = :phone, 
            company_name = :company_name,
            address = :address,
            city = :city,
            state = :state,
            pin_code = :pin_code,
            gst_number = :gst_number
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);

    // Added :phone to the execution array
    $stmt->execute([
        ':name'         => $name,
        ':email'        => $email,
        ':phone'        => $phone, 
        ':company_name' => $company_name,
        ':address'      => $address,
        ':city'         => $city,
        ':state'        => $state,
        ':pin_code'     => $pin_code,
        ':gst_number'   => $gst_number,
        ':id'           => $id
    ]);

    header("Location: users.php?updated=1");
    exit;
}

include '_header.php';
?>

<div class="container">
    <div class="card shadow">
        <div class="card-header bg-warning">
            <h4>Edit User</h4>
        </div>
    <div class="card-body">

        <form method="post">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Name</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Phone</label>
                    <input type="text"
                           name="phone"
                           class="form-control"
                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Company Name</label>
                    <input type="text"
                           name="company_name"
                           class="form-control"
                           value="<?php echo htmlspecialchars($user['company_name'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>GST Number</label>
                    <input type="text"
                           name="gst_number"
                           class="form-control"
                           value="<?php echo htmlspecialchars($user['gst_number'] ?? ''); ?>">
                </div>

                <div class="col-12 mb-3">
                    <label>Address</label>
                    <textarea name="address"
                              class="form-control"
                              rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>

                <div class="col-md-4 mb-3">
                    <label>City</label>
                    <input type="text"
                           name="city"
                           class="form-control"
                           value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label>State</label>
                    <input type="text"
                           name="state"
                           class="form-control"
                           value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label>PIN Code</label>
                    <input type="text"
                           name="pin_code"
                           class="form-control"
                           value="<?php echo htmlspecialchars($user['pin_code'] ?? ''); ?>">
                </div>

            </div>

            <button class="btn btn-success">
                Update User
            </button>

            <a href="users.php"
               class="btn btn-secondary">
                Back
            </a>

        </form>

    </div>
</div>

</div>

<?php include '_footer.php'; ?>