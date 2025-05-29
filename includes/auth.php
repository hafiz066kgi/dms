<?php
require_once __DIR__ . '/../config/config.php';
session_start();

function login($username, $password) {
    $mysqli = db_connect();
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? AND status=1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        return true;
    }
    return false;
}

function require_login() {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }
}

function logout() {
    session_destroy();
    header("Location: login.php");
    exit;
}

function current_user() {
    return $_SESSION['user'] ?? null;
}
?>
