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

$id = intval($_GET['id'] ?? 0);
$mysqli = db_connect();
$stmt = $mysqli->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$edit_user = $stmt->get_result()->fetch_assoc(); // <-- Use $edit_user here

if (!$edit_user) {
    $_SESSION['error'] = "User not found.";
    header("Location: manage_users.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... (same code as before, but update all references to $edit_user)
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1;

    $photo_filename = $edit_user['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_types)) {
            $photo_filename = 'user_' . uniqid() . '.' . $ext;
            $targetDir = __DIR__ . '/../uploads/user_photos/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            $targetFile = $targetDir . $photo_filename;
            move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile);
        }
    }

    $stmt = $mysqli->prepare("UPDATE users SET name=?, email=?, department=?, status=?, photo=? WHERE id=?");
    $stmt->bind_param("sssssi", $name, $email, $department, $status, $photo_filename, $id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "User updated successfully.";
        header("Location: manage_users.php");
        exit;
    } else {
        $_SESSION['error'] = "Failed to update user.";
    }
}

include_once __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <h4>Edit User:</h4>
            <p class="text-muted">You are editing user ID: <?= $edit_user['id'] ?>. Username: <b><?= htmlspecialchars($edit_user['username']) ?></b></p>
            <?php include_once __DIR__ . '/../templates/messages.php'; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" class="form-control" value="<?=htmlspecialchars($edit_user['username'])?>" readonly>
                </div>
                <div class="mb-3">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" required
                           value="<?= htmlspecialchars($edit_user['name'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?=htmlspecialchars($edit_user['email'])?>">
                </div>
                <div class="mb-3">
                    <label>Department</label>
                    <input type="text" name="department" class="form-control" value="<?=htmlspecialchars($edit_user['department'])?>">
                </div>
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" <?=($edit_user['status'] == 1 ? 'selected' : '')?>>Active</option>
                        <option value="0" <?=($edit_user['status'] == 0 ? 'selected' : '')?>>Inactive</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Photo (JPG, PNG)</label>
                    <br>
                    <?php if (!empty($edit_user['photo']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/dms/uploads/user_photos/' . $edit_user['photo'])): ?>
                        <img src="/dms/uploads/user_photos/<?=htmlspecialchars($edit_user['photo'])?>" alt="Photo" style="height:40px;width:40px;object-fit:cover;border-radius:50%;border:1px solid #ccc;">
                    <?php else: ?>
                        <span class="text-muted">No Photo</span>
                    <?php endif; ?>
                    <input type="file" name="photo" class="form-control mt-2" accept=".jpg,.jpeg,.png">
                </div>
                <button type="submit" class="btn btn-success">Update</button>
                <a href="manage_users.php" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>
