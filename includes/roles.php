<?php
function is_superadmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'superadmin';
}
function is_admin() {
    return isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['admin','superadmin']);
}
function is_viewer() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'viewer';
}
?>
