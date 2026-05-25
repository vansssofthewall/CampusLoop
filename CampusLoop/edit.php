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
    die("You don't have permission to edit this listing.");
}

$sql = "SELECT * FROM listings WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $price = $_POST["price"];
    $description = trim($_POST["description"]);
    $category = $_POST["category"];
    
    $updateSql = "UPDATE listings SET title = ?, price = ?, description = ?, category = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sdssi", $title, $price, $description, $category, $id);
    
    if ($updateStmt->execute()) {
        $message = "Listing updated!";
        $item['title'] = $title;
        $item['price'] = $price;
        $item['description'] = $description;
        $item['category'] = $category;
    } else {
        $message = "Error updating.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Listing - CampusLoop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo-area">
            <img src="images/logo.png" class="logo-img" alt="CampusLoop">
            <span class="logo-text">CampusLoop</span>
        </div>
        <div class="nav-center">
            <a href="index.php">Marketplace</a>
            <a href="add_listing.php">Sell</a>
            <a href="my_listings.php">My Items</a>
            <a href="messages.php">Messages</a>
            <a href="order_history.php">Orders</a>
        </div>
        <div class="nav-right">
            <div class="theme-toggle-container"></div>
            <span style="color: var(--text-secondary); font-size: 0.8rem;">👋 <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
            <a href="logout.php" class="btn btn-outline">Logout</a>
        </div>
    </nav>

    <div class="form-container">
        <h2>Edit Listing</h2>
        
        <?php if ($message): ?>
            <div style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 0.8rem; border-radius: 0.75rem; margin-bottom: 1rem;">✅ <?php echo $message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
            </div>
            <div class="form-group">
                <label>Price (R)</label>
                <input type="number" step="0.01" name="price" value="<?php echo $item['price']; ?>" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category">
                    <option <?php echo $item['category'] == 'Textbooks' ? 'selected' : ''; ?>>Textbooks</option>
                    <option <?php echo $item['category'] == 'Notes' ? 'selected' : ''; ?>>Notes</option>
                    <option <?php echo $item['category'] == 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                    <option <?php echo $item['category'] == 'Furniture' ? 'selected' : ''; ?>>Furniture</option>
                    <option <?php echo $item['category'] == 'Clothing' ? 'selected' : ''; ?>>Clothing</option>
                    <option <?php echo $item['category'] == 'Tutoring' ? 'selected' : ''; ?>>Tutoring</option>
                    <option <?php echo $item['category'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($item['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="my_listings.php" style="margin-left: 1rem; color: var(--text-secondary);">Cancel</a>
        </form>
    </div>

    <footer class="footer">
    <p>© 2026 CampusLoop — Made for students, by students</p>
    <p style="margin-top: 0.5rem;">
        <a href="terms.php" style="color: var(--text-muted); text-decoration: none; margin: 0 0.5rem;">Terms</a>
        <a href="support.php" style="color: var(--text-muted); text-decoration: none; margin: 0 0.5rem;">Support</a>
    </p>
</footer>

    <script src="theme.js"></script>
</body>
</html>