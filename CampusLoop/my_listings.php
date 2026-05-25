<?php
session_start();
include("config.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$listings = $conn->query("SELECT * FROM listings WHERE user_id = $user_id ORDER BY id DESC");
$total = $listings->num_rows;
$active = $conn->query("SELECT COUNT(*) as c FROM listings WHERE user_id = $user_id AND (status IS NULL OR status = 'available')")->fetch_assoc()['c'];
$sold = $conn->query("SELECT COUNT(*) as c FROM listings WHERE user_id = $user_id AND status = 'sold'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Listings - CampusLoop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="index.php" class="logo">
                <div class="logo-icon">CL</div>
                <span class="logo-text">CampusLoop</span>
            </a>
            <div class="nav-links">
                <a href="index.php">Marketplace</a>
                <a href="add_listing.php">Sell</a>
                <a href="my_listings.php" style="color: var(--primary);">My Items</a>
                <a href="messages.php">Messages</a>
                <a href="order_history.php">Orders</a>
            </div>
            <div class="nav-actions">
                <div class="theme-toggle-container"></div>
                <a href="cart.php" class="btn btn-outline" style="padding: 0.4rem 1rem;">🛒 Cart <span id="cartCount">0</span></a>
                <span style="color: var(--text-secondary);">👋 <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin: 1.5rem 0;">
            <h1>My Listings</h1>
            <a href="add_listing.php" class="btn btn-primary">+ New Listing</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-number"><?php echo $total; ?></div><div>Total Listings</div></div>
            <div class="stat-card"><div class="stat-number"><?php echo $active; ?></div><div>Active</div></div>
            <div class="stat-card"><div class="stat-number"><?php echo $sold; ?></div><div>Sold</div></div>
        </div>
        
        <?php if ($listings->num_rows > 0): ?>
            <div class="product-grid">
                <?php while($row = $listings->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if(!empty($row['image']) && file_exists("uploads/".$row['image'])): ?>
                                <img src="uploads/<?php echo $row['image']; ?>">
                            <?php else: ?>
                                <div style="height: 100%; display: flex; align-items: center; justify-content: center;">📷</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            <div class="product-price">R <?php echo number_format($row['price'], 2); ?></div>
                            <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                <a href="product.php?id=<?php echo $row['id']; ?>" class="btn btn-outline" style="padding: 0.3rem 0.8rem;">View</a>
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-outline" style="padding: 0.3rem 0.8rem;">Edit</a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" style="padding: 0.3rem 0.8rem;" onclick="return confirm('Delete?')">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card text-center" style="padding: 3rem;">
                <span style="font-size: 3rem;">📦</span>
                <h3 style="margin: 1rem 0;">No listings yet</h3>
                <a href="add_listing.php" class="btn btn-primary">Create your first listing →</a>
            </div>
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
    <script>
        function updateCartCount() {
            let cart = JSON.parse(localStorage.getItem('cart') || '[]');
            let count = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
            let span = document.getElementById('cartCount');
            if (span) span.textContent = count;
        }
        updateCartCount();
    </script>
</body>
</html>