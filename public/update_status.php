<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

if (!is_superadmin()) {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: manage_users.php");
    exit;
}

$id = intval($_POST['id'] ?? 0);
$status = isset($_POST['status']) ? intval($_POST['status']) : 1;

if ($id > 0) {
    $mysqli = db_connect();
    $stmt = $mysqli->prepare("UPDATE users SET status=? WHERE id=?");
    $stmt->bind_param("ii", $status, $id);
    $stmt->execute();
    $_SESSION['message'] = "Status updated successfully.";
}
header("Location: manage_users.php");
exit;
