<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

if (!is_superadmin()) {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $role     = 'admin';

    // Handle file upload (photo)
    $photo_filename = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_types)) {
            $photo_filename = 'admin_' . uniqid() . '.' . $ext;
            $targetDir = __DIR__ . '/../uploads/user_photos/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            $targetFile = $targetDir . $photo_filename;
            move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile);
        } else {
            $_SESSION['error'] = "Invalid photo type. Only JPG and PNG are allowed.";
            $photo_filename = null;
        }
    }

    // Validate fields
    if (empty($username) || empty($password) || empty($name)) {
        $_SESSION['error'] = "All fields marked * are required.";
    } else {
        $mysqli = db_connect();
        // Check username uniqueness
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = "Username already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare(
                "INSERT INTO users (username, password, name, email, role, photo) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssssss", $username, $hashed, $name, $email, $role, $photo_filename);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Admin account created successfully.";
                log_action($_SESSION['user']['id'], "Created admin user: {$username}", $mysqli->insert_id);
                header("Location: manage_users.php");
                exit;
            } else {
                $_SESSION['error'] = "Failed to create admin account.";
            }
        }
    }
}

include_once __DIR__ . '/../templates/header.php';
?>

<style>
.form-small, .form-small label, .form-small input, .form-small select, .form-small textarea, .form-small button {
    font-size: 0.93rem !important;
}
</style>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <h4 class="form mb-3">Add New Admin</h4>
            <?php include_once __DIR__ . '/../templates/messages.php'; ?>
            <form method="post" enctype="multipart/form-data" class="mt-3 form-small">
                <div class="mb-2">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-control form-control-sm" required>
                </div>
                <div class="mb-2">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control form-control-sm" required>
                </div>
                <div class="mb-2">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm" required>
                </div>
                <div class="mb-2">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label>Photo (JPG, PNG)</label>
                    <input type="file" name="photo" class="form-control form-control-sm" accept=".jpg,.jpeg,.png">
                </div>
                <input type="hidden" name="role" value="admin">
                <button type="submit" class="btn btn-success btn-sm">Add Admin</button>
                <a href="manage_users.php" class="btn btn-secondary btn-sm ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
