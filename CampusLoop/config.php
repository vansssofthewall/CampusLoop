<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "campusloop_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session if not started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>