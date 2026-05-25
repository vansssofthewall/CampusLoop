<?php
session_start();
include("config.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$orders = $conn->query("
    SELECT * FROM orders 
    WHERE user_id = $user_id 
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - CampusLoop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .order-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            margin-bottom: 1rem;
            overflow: hidden;
        }
        .order-header {
            padding: 1rem;
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .order-number {
            font-weight: 600;
            font-family: monospace;
        }
        .order-status {
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        .status-paid { background: var(--accent-gold); color: #111; }
        .status-pending { background: #f59e0b; color: white; }
        .status-completed { background: #10b981; color: white; }
        .status-cancelled { background: #ef4444; color: white; }
        .order-items {
            padding: 1rem;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-light);
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-footer {
            padding: 1rem;
            background: var(--bg-primary);
            border-top: 1px solid var(--border);
            text-align: right;
        }
    </style>
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
                <a href="order_history.php" style="color: var(--accent-gold);">Orders</a>
            </div>
            <div class="nav-actions">
                <div class="theme-toggle-container"></div>
                <a href="cart.php" class="btn btn-outline">🛒 Cart</a>
                <span>👋 <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container page-content">
        <h1 style="margin-bottom: 1.5rem;">📦 Order History</h1>
        
        <?php if ($orders->num_rows > 0): ?>
            <?php while($order = $orders->fetch_assoc()): ?>
                <?php
                $items = $conn->query("SELECT * FROM order_items WHERE order_id = {$order['id']}");
                ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="order-number">Order #<?php echo $order['order_number']; ?></span>
                            <span style="color: var(--text-muted); margin-left: 1rem;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div>
                            <span class="order-status status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="order-items">
                        <?php while($item = $items->fetch_assoc()): ?>
                            <div class="order-item">
                                <div>
                                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                    <span style="color: var(--text-muted); font-size: 0.8rem;"> x<?php echo $item['quantity']; ?></span>
                                </div>
                                <div>R <?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="order-footer">
                        <strong>Total: R <?php echo number_format($order['total_amount'], 2); ?></strong>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card text-center" style="padding: 3rem;">
                <span style="font-size: 3rem;">📦</span>
                <h3 style="margin: 1rem 0;">No orders yet</h3>
                <p>Your order history will appear here after you make a purchase.</p>
                <a href="index.php" class="btn btn-primary" style="margin-top: 1rem;">Browse Marketplace →</a>
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