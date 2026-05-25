<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id']) || !isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$search = "%" . $_GET['q'] . "%";

$stmt = $conn->prepare("SELECT id, username FROM users WHERE id != ? AND username LIKE ? LIMIT 20");
$stmt->bind_param("is", $user_id, $search);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = ['id' => $row['id'], 'username' => htmlspecialchars($row['username'])];
}

echo json_encode($users);
?>