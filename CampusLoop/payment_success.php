<?php
session_start();
include("config.php");

// Get PayFast response (may be empty if direct access)
$pfData = $_GET;
$order_id = isset($pfData['m_payment_id']) ? intval($pfData['m_payment_id']) : 0;

// If no order ID in URL, get the most recent pending order for this user
if (!$order_id && isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $recent = $conn->query("SELECT id FROM orders WHERE user_id = $user_id AND status = 'pending' ORDER BY id DESC LIMIT 1");
    if ($recent && $recent->num_rows > 0) {
        $order_id = $recent->fetch_assoc()['id'];
        // Mark as paid
        $conn->query("UPDATE orders SET status = 'paid' WHERE id = $order_id");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - CampusLoop</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Clear cart from localStorage
        localStorage.removeItem('cart');
    </script>
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
                <a href="my_listings.php">My Items</a>
                <a href="messages.php">Messages</a>
                <a href="order_history.php">Orders</a>
            </div>
            <div class="nav-actions">
                <div class="theme-toggle-container"></div>
                <a href="cart.php" class="btn btn-outline">🛒 Cart <span id="cartCount">0</span></a>
                <?php if (isset($_SESSION["username"])): ?>
                    <span>👋 <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <a href="logout.php" class="btn btn-outline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline">Login</a>
                    <a href="register.php" class="btn btn-primary">Sign up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container page-content" style="max-width: 600px;">
        <div class="card text-center" style="padding: 3rem;">
            <span style="font-size: 4rem;">✅</span>
            <h1 style="margin: 1rem 0;">Payment Successful!</h1>
            <p style="color: var(--text-secondary);">Your payment has been processed successfully.</p>
            
            <div style="background: var(--bg-primary); border-radius: var(--radius); padding: 1rem; margin: 1.5rem 0;">
                <p><strong>Order ID:</strong> #<?php echo $order_id ? $order_id : 'N/A'; ?></p>
                <p><strong>Status:</strong> <span style="color: #10b981;">Paid</span></p>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
                <a href="order_history.php" class="btn btn-primary">View My Orders →</a>
                <a href="index.php" class="btn btn-outline">Continue Shopping</a>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>© 2026 CampusLoop — Made for students, by students</p>
        <div class="footer-links">
            <a href="terms.php">Terms</a>
            <a href="support.php">Support</a>
        </div>
    </footer>

    <script src="theme.js"></script>
    <script>
        function updateCartCount() {
            let cartSpan = document.getElementById('cartCount');
            if (cartSpan) cartSpan.textContent = '0';
        }
        updateCartCount();
    </script>
</body>
</html>