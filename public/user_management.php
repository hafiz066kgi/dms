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

$mysqli = db_connect();

// Add new user (optional, not handling validation for brevity)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $email = trim($_POST['email']);
    $department = trim($_POST['department']);
    $stmt = $mysqli->prepare("INSERT INTO users (username, password, role, email, department) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $username, $password, $role, $email, $department);
    $stmt->execute();
    $_SESSION['message'] = "User created.";
    header("Location: user_management.php");
    exit;
}

$users = $mysqli->query("SELECT * FROM users")->fetch_all(MYSQLI_ASSOC);

include_once __DIR__ . '/../templates/header.php';
?>

<div class="container">
    <h4>User Management</h4>
    <?php include_once __DIR__ . '/../templates/messages.php'; ?>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No.</th>
                <th>Username</th>
                <th>Role</th>
                <th>Email</th>
                <th>Department</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php $n=1; foreach($users as $u): ?>
            <tr>
                <td><?=$n++?></td>
                <td><?=htmlspecialchars($u['username'])?></td>
                <td><?=htmlspecialchars($u['role'])?></td>
                <td><?=htmlspecialchars($u['email'])?></td>
                <td><?=htmlspecialchars($u['department'])?></td>
                <td><?=$u['status'] ? "Active" : "Inactive"?></td>
                <td><?=htmlspecialchars($u['created_at'])?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h5>Add New User</h5>
    <form method="post" class="row g-3 mb-4">
        <div class="col-md-2"><input name="username" class="form-control" placeholder="Username" required></div>
        <div class="col-md-2"><input name="password" class="form-control" type="password" placeholder="Password" required></div>
        <div class="col-md-2">
            <select name="role" class="form-control" required>
                <option value="viewer">Viewer</option>
                <option value="admin">Admin</option>
                <option value="superadmin">Superadmin</option>
            </select>
        </div>
        <div class="col-md-3"><input name="email" class="form-control" placeholder="Email"></div>
        <div class="col-md-2"><input name="department" class="form-control" placeholder="Department"></div>
        <div class="col-md-1"><button class="btn btn-success">Add</button></div>
    </form>
</div>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>
