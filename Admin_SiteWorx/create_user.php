<?php
require_once 'connection.php';
require_once 'lib_auth.php';
require_once 'lib_admin.php';

require_role($pdo, ['admin','manager']);

$roles = $pdo->query("SELECT id,name FROM roles ORDER BY name")->fetchAll();
$managers = $pdo->query("
    SELECT id,name,username
    FROM login
    WHERE role_id IN (1,2)
    AND status='active'
    ORDER BY name,username
")->fetchAll();

$current = current_user($pdo);
$currentRole = get_user_role($pdo, $current);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name         = trim($_POST['name'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone        = trim($_POST['phone'] ?? ''); // Added phone variable
    $password     = $_POST['password'] ?? '';

    $company_name = trim($_POST['company_name'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $city         = trim($_POST['city'] ?? '');
    $state        = trim($_POST['state'] ?? '');
    $pin_code     = trim($_POST['pin_code'] ?? '');
    $gst_number   = trim($_POST['gst_number'] ?? '');

    $role_id = $currentRole === 'admin'
        ? (int)($_POST['role_id'] ?? 3)
        : 3;

    $manager_id = $currentRole === 'admin'
        ? (int)($_POST['manager_id'] ?? 0)
        : (int)$current['id'];

    if (empty($username) || empty($password)) {
        $error = "Username and Password are required.";
    } else {

        try {

            $id = register_user(
                $pdo,
                $name,
                $email,
                $username,
                $password
            );

            $stmt = $pdo->prepare("
                UPDATE login
                SET
                    role_id = :role_id,
                    manager_id = :manager_id,
                    phone = :phone, 
                    company_name = :company_name,
                    address = :address,
                    city = :city,
                    state = :state,
                    pin_code = :pin_code,
                    gst_number = :gst_number
                WHERE id = :id
            ");

            // Added :phone to the execution array
            $stmt->execute([
                ':role_id'      => $role_id,
                ':manager_id'   => $manager_id ?: null,
                ':phone'        => $phone, 
                ':company_name' => $company_name,
                ':address'      => $address,
                ':city'         => $city,
                ':state'        => $state,
                ':pin_code'     => $pin_code,
                ':gst_number'   => $gst_number,
                ':id'           => $id
            ]);

            header("Location: users.php?success=1");
            exit;

        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<?php include '_header.php'; ?>

<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Create New User</h4>
                </div>

                <div class="card-body">

                    <?php if($error): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Full Name
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Username *
                                </label>
                                <input
                                    type="text"
                                    name="username"
                                    class="form-control"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Phone
                                </label>
                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Password *
                                </label>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    required>
                            </div>

                        </div>

                        <hr>

                        <h5 class="mb-3">
                            Company Information
                        </h5>

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Company Name
                                </label>
                                <input
                                    type="text"
                                    name="company_name"
                                    class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    GST Number
                                </label>
                                <input
                                    type="text"
                                    name="gst_number"
                                    class="form-control">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">
                                    Address
                                </label>
                                <textarea
                                    name="address"
                                    rows="3"
                                    class="form-control"></textarea>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    City
                                </label>
                                <input
                                    type="text"
                                    name="city"
                                    class="form-control">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    State
                                </label>
                                <input
                                    type="text"
                                    name="state"
                                    class="form-control">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    PIN Code
                                </label>
                                <input
                                    type="text"
                                    name="pin_code"
                                    class="form-control">
                            </div>

                        </div>

                        <?php if($currentRole === 'admin'): ?>

                            <hr>

                            <h5 class="mb-3">
                                User Access
                            </h5>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Role
                                    </label>

                                    <select
                                        name="role_id"
                                        class="form-select">

                                        <?php foreach($roles as $role): ?>
                                            <option value="<?php echo $role['id']; ?>">
                                                <?php echo htmlspecialchars($role['name']); ?>
                                            </option>
                                        <?php endforeach; ?>

                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Manager
                                    </label>

                                    <select
                                        name="manager_id"
                                        class="form-select">

                                        <option value="">
                                            No Manager
                                        </option>

                                        <?php foreach($managers as $manager): ?>
                                            <option value="<?php echo $manager['id']; ?>">
                                                <?php echo htmlspecialchars(
                                                    $manager['name'] ?: $manager['username']
                                                ); ?>
                                            </option>
                                        <?php endforeach; ?>

                                    </select>
                                </div>

                            </div>

                        <?php endif; ?>

                        <div class="text-end">

                            <a href="users.php"
                               class="btn btn-secondary">
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="btn btn-success">
                                Create User
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<?php include '_footer.php'; ?>