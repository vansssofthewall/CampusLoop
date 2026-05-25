<?php 
session_start();
include("config.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$order_number = isset($_GET['order']) ? $_GET['order'] : '';
$order = null;

if ($ordeer_number) {
    $user_id = $_SESSION['user_id'];
    $order = $conn->query("SELECT * FROM orders WHERE order_number = '$order_number' AND user_id = $user_id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - CampusLoop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="index.php" class="logo">
                <div class="logo-icon">CL</div>
                <span class="logo=text">CampusLoop</span>
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
                <a href="cart.php" class="btn btn-outline">🛒 Cart</a
                <span>👋 <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container page-content" style="max-width: 600px;">
        <div class="card text-center" style="padding: 3rem;">
            <span style="font-size: 4rem;">✅</span>
            <h1 style="margin: 1rem 0;">Order Confirmed!</h1>
            <p style="color: var(--text-secondary);">Thank you for shopping at CampusLoop!</p>

            <?php if ($order): ?>
                <div style="background: var(--bg-primary); border-radius: var(--radius); padding: 1rem; margin: 1.5rem 0;">
                    <p><strong>Order Number:</strong> <?php echo $order['order_number']; ?></p>
                    <p><strong>Total Amount:</strong> R <?php echo number_format($order['total_amount'], 2); ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                </div>
            <?php endif; ?>

                <p style="color: var(--text-secondary); font-size: 0.9rem;">A confirmation email has been sent to your student email address.</p>
                <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
                    <a href="index.php" class="btn btn-primary">Continue Shopping →</a>
                    <a href="order_history.php" class="btn btn-outline">View My Orders</a>
                </div>
        </div>
    </div>

    <footer class="footer">
    <p>© 2026 CampusLoop — Made for students, by students</p>
    <p style="margin-top: 0.5rem;">
        <a href="terms.php" style="color: var(--text-muted); text-decoration: none; margin: 0 0.5rem;">Terms</a>
        <a href="support.php" style="color: var(--text-muted); text-decoration: none; margin: 0 0.5rem;">Support</a>
    </p>
</footer>

    <script src="theme.js"><script>
</body>
</html>


        


