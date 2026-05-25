<?php
session_start();
include("config.php");

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$user_id = (int)$_GET['id'];

$user = $conn->query("SELECT username, created_at FROM users WHERE id = $user_id")->fetch_assoc();
if (!$user) die("User not found.");

$listings = $conn->query("SELECT * FROM listings WHERE user_id = $user_id ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($user['username']); ?> - CampusLoop</title>
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
            <?php if (isset($_SESSION["username"])): ?>
                <span style="color: var(--text-secondary); font-size: 0.8rem;">👋 <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Log in</a>
                <a href="register.php" class="btn btn-primary">Sign up →</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <div style="background: var(--bg-card); border-radius: var(--radius-lg); padding: 2rem; margin-bottom: 2rem; border: 1px solid var(--border); text-align: center;">
            <span style="font-size: 4rem;">👤</span>
            <h1><?php echo htmlspecialchars($user['username']); ?></h1>
            <p style="color: var(--text-muted);">Member since <?php echo date('F Y', strtotime($user['created_at'] ?? 'now')); ?></p>
        </div>
        
        <h2 style="margin-bottom: 1rem;">📦 Listings by <?php echo htmlspecialchars($user['username']); ?></h2>
        
        <?php if ($listings->num_rows > 0): ?>
            <div class="product-grid">
                <?php while($row = $listings->fetch_assoc()): ?>
                    <a href="product.php?id=<?php echo $row['id']; ?>" class="product-card">
                        <div class="product-image">
                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>">
                            <div class="product-badge">For Sale</div>
                        </div>
                        <div class="product-info">
                            <div class="product-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            <div class="product-price">R <?php echo number_format($row['price'], 2); ?></div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><p>This user has no active listings.</p></div>
        <?php endif; ?>
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