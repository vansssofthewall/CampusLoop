<?php
session_start();
include("config.php");

if (isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusLoop - Student Marketplace</title>
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
                <a href="#">How it works</a>
                <a href="faq.php">Help</a>
                <a href="order_history.php">Orders</a>
            </div>
            <div class="nav-actions">
                <div class="theme-toggle-container"></div>
                <a href="login.php" class="btn btn-outline">Log in</a>
                <a href="register.php" class="btn btn-primary">Sign up →</a>
            </div>
        </div>
    </nav>

    <div class="container page-content">
        <div class="hero">
            <div>
                <h1>Buy and sell<br>with your campus</h1>
                <p>The student marketplace for textbooks, electronics, notes, and more — from students, for students. Zero fees, zero middlemen.</p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="register.php" class="btn btn-primary">Start selling →</a>
                    <a href="index.php" class="btn btn-outline">Browse items</a>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-circle">
                    <span>📚</span>
                </div>
            </div>
        </div>

        <div class="category-pills">
            <a href="index.php?category=Textbooks" class="category-pill">📚 Textbooks</a>
            <a href="index.php?category=Notes" class="category-pill">📝 Study Notes</a>
            <a href="index.php?category=Electronics" class="category-pill">💻 Electronics</a>
            <a href="index.php?category=Furniture" class="category-pill">🪑 Furniture</a>
            <a href="index.php?category=Tutoring" class="category-pill">🎓 Tutoring</a>
            <a href="index.php?category=Clothing" class="category-pill">👕 Clothing</a>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: baseline; margin: 2rem 0 1rem;">
            <h2 style="font-size: 1.3rem; font-weight: 600;">Trending now</h2>
            <a href="index.php" style="color: var(--text-muted); font-size: 0.85rem;">View all →</a>
        </div>

        <?php
        $trending = $conn->query("SELECT l.*, u.username FROM listings l JOIN users u ON l.user_id = u.id ORDER BY l.id DESC LIMIT 4");
        ?>
        
        <?php if ($trending && $trending->num_rows > 0): ?>
            <div class="product-grid">
                <?php while($item = $trending->fetch_assoc()): ?>
                    <a href="product.php?id=<?php echo $item['id']; ?>" class="product-card">
                        <div class="product-image">
                            <?php if(!empty($item['image']) && file_exists("uploads/" . $item['image'])): ?>
                                <img src="uploads/<?php echo $item['image']; ?>">
                            <?php else: ?>
                                <div style="height: 100%; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary);">
                                    <span style="font-size: 2rem;">📷</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-title"><?php echo htmlspecialchars($item['title']); ?></div>
                            <div class="product-price">R <?php echo number_format($item['price'], 2); ?></div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card text-center" style="padding: 3rem;">
                <p>No listings yet. Be the first to sell!</p>
                <a href="add_listing.php" class="btn btn-primary" style="margin-top: 1rem;">Sell something →</a>
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
    <script src="animations.js"></script>
</body>
</html>