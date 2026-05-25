<?php
session_start();
include("config.php");
include("includes/security.php");

if (!isset($_SESSION['user_id']) || !isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    echo json_encode(['success' => false]);
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = (int)$_POST['message'];
$message = sanitizeInput($_POST['messsage']);

if (!empty($message)) {
    $stmt = $conn-prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    $stmt->execute();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
} 
?>