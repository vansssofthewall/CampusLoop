<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$reviewer_id = $_SESSION['user_id'];
$seller_id = (int)$_POST['seller_id'];
$listing_id = (int)$_POST['listing_id'];
$rating = (int)$_POST['rating'];
$comment = trim($_POST['comment']);

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'error' => 'Invalid rating']);
    exit;
}

// Check if user already reviewed this listing
$check = $conn->query("
    SELECT id FROM reviews 
    WHERE reviewer_id = $reviewer_id AND listing_id = $listing_id
");

if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'You have already reviewed this listing']);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO reviews (reviewer_id, seller_id, listing_id, rating, comment) 
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("iiiis", $reviewer_id, $seller_id, $listing_id, $rating, $comment);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>