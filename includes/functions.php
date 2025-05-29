<?php
require_once __DIR__ . '/../config/config.php';

function get_documents($category = null) {
    $mysqli = db_connect();
    $sql = "SELECT d.*, u.username as uploaded_by FROM documents d LEFT JOIN users u ON d.uploaded_by=u.id";
    if ($category) {
        $sql .= " WHERE d.category = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $mysqli->query($sql);
    }
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function get_document($id) {
    $mysqli = db_connect();
    $stmt = $mysqli->prepare("SELECT d.*, u.username as uploaded_by FROM documents d LEFT JOIN users u ON d.uploaded_by=u.id WHERE d.id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function log_action($user_id, $action, $document_id = null) {
    $mysqli = db_connect();
    $stmt = $mysqli->prepare("INSERT INTO audit_log (user_id, action, document_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $user_id, $action, $document_id);
    $stmt->execute();
}
?>
