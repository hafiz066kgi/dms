<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

if (!is_admin() && !is_superadmin()) {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: index.php");
    exit;
}

$mysqli = db_connect();
$result = $mysqli->query("SELECT * FROM users ORDER BY id ASC");

include_once __DIR__ . '/../templates/header.php';
?>

<div class="container mt-4 user-mgmt">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">User Management</h4>
            <?php include_once __DIR__ . '/../templates/messages.php'; ?>
            <?php if (is_superadmin()): ?>
                <a href="add_admin.php" class="btn btn-success btn-sm mb-3">Add Admin</a>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Photo</th>
                            <?php if (is_superadmin()): ?>
                            <th>Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?=htmlspecialchars($user['id'])?></td>
                            <td><?=htmlspecialchars($user['username'])?></td>
                            <td><?=htmlspecialchars($user['name'])?></td>
                            <td><?=htmlspecialchars(ucfirst($user['role']))?></td>
                            <td><?=htmlspecialchars($user['email'])?></td>
                            <td><?=htmlspecialchars($user['department'] ?? '-')?></td>
                            <td>
                                <?=
                                    ($user['status'] == 1)
                                        ? '<span class="badge bg-success">Active</span>'
                                        : '<span class="badge bg-secondary">Inactive</span>';
                                ?>
                            </td>
                            <td><?=htmlspecialchars($user['created_at'] ?? '-')?></td>
                            <td>
                                <?php if (!empty($user['photo'])): ?>
                                    <img src="/dms/uploads/user_photos/<?=htmlspecialchars($user['photo'])?>" alt="Photo" style="height:36px;width:36px;object-fit:cover;border-radius:50%;border:1px solid #ccc;">
                                <?php else: ?>
                                    <span class="text-muted">No Photo</span>
                                <?php endif; ?>
                            </td>
                            <?php if (is_superadmin()): ?>
                            <td>
                                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm mb-1">Edit</a>
                                <a href="delete_user.php?id=<?=$user['id']?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Delete this user?');">Delete</a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <a href="index.php" class="btn btn-secondary btn-sm mt-2 ms-2">Cancel</a>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>
