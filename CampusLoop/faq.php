<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FAQ - CampusLoop</title>
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
            <a href="faq.php">FAQ</a>
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

    <div class="container" style="max-width: 800px;">
        <h1 style="margin-bottom: 2rem;">❓ Frequently Asked Questions</h1>
        
        <div style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border); overflow: hidden;">
            <div style="padding: 1.2rem; border-bottom: 1px solid var(--border);"><strong>How do I create a listing?</strong><p style="color: var(--text-secondary); margin-top: 0.3rem;">Click "Sell" in the navigation bar, fill in your item details, upload a photo, and publish!</p></div>
            <div style="padding: 1.2rem; border-bottom: 1px solid var(--border);"><strong>Is CampusLoop free?</strong><p style="color: var(--text-secondary); margin-top: 0.3rem;">Yes! CampusLoop is completely free for all students.</p></div>
            <div style="padding: 1.2rem; border-bottom: 1px solid var(--border);"><strong>How do I contact a seller?</strong><p style="color: var(--text-secondary); margin-top: 0.3rem;">Click "Message Seller" on any product page to start a conversation.</p></div>
            <div style="padding: 1.2rem; border-bottom: 1px solid var(--border);"><strong>How do payments work?</strong><p style="color: var(--text-secondary); margin-top: 0.3rem;">Payments are arranged directly between buyer and seller. Meet on campus in a public place for safety.</p></div>
            <div style="padding: 1.2rem;"><strong>Can I edit or delete my listing?</strong><p style="color: var(--text-secondary); margin-top: 0.3rem;">Yes! Go to "My Items" to manage all your listings.</p></div>
        </div>
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