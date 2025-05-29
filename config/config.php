<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change this
define('DB_NAME', 'iso9001_dms');

function db_connect() {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        die('Database connection error: ' . $mysqli->connect_error);
    }
    return $mysqli;
}
?>
