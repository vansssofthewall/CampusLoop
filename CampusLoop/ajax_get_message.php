<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$other_id = (int)$_GET['user_id'];

// Mark messages as read
$conn->query("UPDATE messages SET is_read = 1 WHERE sender_id = $other_id AND receiver_id = $user_id");

// Get messages between two users
$stmt = $conn->prepare("
    SELECT * FROM messages
    WHERE (sender_id = ? AND receiver_id = ?)
        OR (sender_id = ? AND reciever_id = ?) 
        ORDER BY created_at ASC
");
$tmt->bind_param("iiii", $user_id, $other_id, $other_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'id' => $row['id'],
        'sender_id' => $row['sender_id'],
        'receiver_id' => $row['receiver_id'],
        'message' => htmlspecialchars($row['message']),
        'created_at' => $row['created_at']
    ];
}

echo json_encode($messages);
?>