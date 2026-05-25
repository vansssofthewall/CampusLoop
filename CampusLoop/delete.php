<?php
session_start();
include("config.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("No ID provided");
}

$id = (int)$_GET['id'];

$check = $conn->query("SELECT user_id FROM listings WHERE id = $id");
$listing = $check->fetch_assoc();

if (!$listing || ($listing['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'admin')) {
    die("You don't have permission to delete this listing.");
}

$stmt = $conn->prepare("DELETE FROM listings WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
exit;
?>