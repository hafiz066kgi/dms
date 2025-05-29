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

$id = intval($_GET['id'] ?? 0);
$doc = get_document($id);

if (!$doc) {
    $_SESSION['error'] = "Document not found.";
    header("Location: index.php");
    exit;
}

$category = $doc['category'];
$department = $doc['department'];
$filename = $doc['filename'];

// Sanitize department for file path
$safeDepartment = preg_replace('/[^A-Za-z0-9_\- ]/', '', $department);
$safeDepartment = str_replace(' ', '_', $safeDepartment);
$file_path = __DIR__ . "/../uploads/{$category}/{$safeDepartment}/{$filename}";

$mysqli = db_connect();
$stmt = $mysqli->prepare("DELETE FROM documents WHERE id=?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    log_action($_SESSION['user']['id'], "Deleted document: {$doc['title']}", $id);
    $_SESSION['message'] = "Document deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete document.";
}
header("Location: index.php");
exit;
