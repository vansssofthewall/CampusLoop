<?php
session_start();
include("config.php");

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET["id"];
$product = $conn->query("SELECT l.*, u.username as seller_name, u.id as seller_id FROM listings l JOIN users u ON l.user_id = u.id WHERE l.id = $id")->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['title']); ?> - CampusLoop</title>
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
                <a href="my_listings.php">My Items</a>
                <a href="messages.php">Messages</a>
                <a href="order_history.php">Orders</a>
            </div>
            <div class="nav-actions">
                <div class="theme-toggle-container"></div>
                <a href="cart.php" class="btn btn-outline">🛒 Cart</a>
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

    <div class="container" style="max-width: 1100px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; background: var(--bg-card); border-radius: var(--radius-xl); padding: 2rem; border: 1px solid var(--border); margin: 2rem 0;">
            <div>
                <?php if (!empty($product['image']) && file_exists("uploads/" . $product['image'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" style="width: 100%; border-radius: var(--radius-lg);">
                <?php else: ?>
                    <div style="background: var(--bg-secondary); height: 400px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-lg);">
                        <span style="font-size: 4rem;">📷</span>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <!-- Listing Type Badge -->
                <?php if ($product['listing_type'] == 'service'): ?>
                    <span style="display: inline-block; background: var(--accent-gold); color: #111; font-size: 0.7rem; padding: 0.2rem 0.8rem; border-radius: 20px; margin-bottom: 0.5rem;">
                        🎓 Service
                    </span>
                <?php else: ?>
                    <span style="display: inline-block; background: var(--bg-secondary); color: var(--text-muted); font-size: 0.7rem; padding: 0.2rem 0.8rem; border-radius: 20px; margin-bottom: 0.5rem;">
                        📦 Product
                    </span>
                <?php endif; ?>
                
                <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($product['title']); ?></h1>
                <p style="font-size: 2rem; font-weight: 700; color: var(--accent-gold); margin: 0.5rem 0;">R <?php echo number_format($product['price'], 2); ?></p>
                
                <!-- Service Details (if applicable) -->
                <?php if ($product['listing_type'] == 'service'): ?>
                    <div style="background: var(--bg-primary); border-radius: var(--radius); padding: 1rem; margin: 1rem 0;">
                        <h4 style="margin-bottom: 0.5rem;">🎓 Service Details</h4>
                        <p><strong>Duration:</strong> <?php echo htmlspecialchars($product['service_duration'] ?? 'Flexible'); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($product['service_location'] ?? 'To be arranged'); ?></p>
                        <p><strong>Availability:</strong> <?php echo htmlspecialchars($product['availability'] ?? 'Contact seller for availability'); ?></p>
                    </div>
                <?php endif; ?>
                
                <div style="background: var(--bg-secondary); padding: 0.8rem; border-radius: var(--radius); margin: 1rem 0;">
                    <strong>Seller:</strong> 
                    <a href="user_profile.php?id=<?php echo $product['seller_id']; ?>" style="color: var(--accent-gold); text-decoration: none;"><?php echo htmlspecialchars($product['seller_name']); ?></a>
                </div>
                
                <div><span class="btn btn-outline" style="padding: 0.2rem 0.8rem;"><?php echo htmlspecialchars($product['category'] ?? 'Other'); ?></span></div>
                
                <div style="margin: 1rem 0;">
                    <strong>Description:</strong>
                    <p style="color: var(--text-secondary); margin-top: 0.5rem;"><?php echo nl2br(htmlspecialchars($product['description'] ?: 'No description provided.')); ?></p>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap;">
                    <button class="btn btn-primary" onclick="addToCart()">🛒 Add to Cart</button>
                    <?php if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] != $product["seller_id"]): ?>
                        <button class="btn btn-outline" onclick="startConversation()">💬 Message Seller</button>
                    <?php elseif (!isset($_SESSION["user_id"])): ?>
                        <a href="login.php" class="btn btn-outline">Login to Buy</a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION["user_id"]) && ($_SESSION["user_id"] == $product["seller_id"] || ($_SESSION["role"] ?? '') == 'admin')): ?>
                        <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-outline">Edit</a>
                        <a href="delete.php?id=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete?')">Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Reviews Section -->
        <div style="background: var(--bg-card); border-radius: var(--radius-xl); padding: 1.5rem; border: 1px solid var(--border); margin-bottom: 2rem;">
            <h3>⭐ Reviews & Ratings</h3>
            <?php
            $rating_data = $conn->query("SELECT COALESCE(AVG(rating), 0) as avg, COUNT(*) as cnt FROM reviews WHERE seller_id = {$product['seller_id']}")->fetch_assoc();
            $avg = round($rating_data['avg'], 1);
            $cnt = $rating_data['cnt'];
            ?>
            <div style="margin: 1rem 0;">
                <span style="font-size: 1.2rem;"><?php echo str_repeat('⭐', floor($avg)) . ($avg - floor($avg) >= 0.5 ? '½' : ''); ?></span>
                <span style="color: var(--text-muted);"><?php echo $avg; ?> out of 5 (<?php echo $cnt; ?> reviews)</span>
            </div>
            <?php
            $reviews = $conn->query("SELECT r.*, u.username FROM reviews r JOIN users u ON r.reviewer_id = u.id WHERE r.seller_id = {$product['seller_id']} ORDER BY r.created_at DESC LIMIT 10");
            if ($reviews->num_rows > 0):
                while($review = $reviews->fetch_assoc()):
            ?>
                <div style="border-bottom: 1px solid var(--border); padding: 1rem 0;">
                    <div><strong><?php echo htmlspecialchars($review['username']); ?></strong> • <?php echo date('M d, Y', strtotime($review['created_at'])); ?></div>
                    <div><?php echo str_repeat('⭐', $review['rating']); ?></div>
                    <p style="color: var(--text-secondary); margin-top: 0.3rem;"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                </div>
            <?php endwhile; else: ?>
                <p style="color: var(--text-muted);">No reviews yet.</p>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center;">
            <a href="index.php" style="color: var(--text-muted);">← Back to Marketplace</a>
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
        const productId = <?php echo $product['id']; ?>;
        const productTitle = '<?php echo addslashes($product['title']); ?>';
        const productPrice = <?php echo $product['price']; ?>;
        const productImage = '<?php echo !empty($product['image']) ? "uploads/" . $product['image'] : ""; ?>';
        
        function addToCart() {
            let cart = JSON.parse(localStorage.getItem('cart') || '[]');
            let existing = cart.find(item => item.id === productId);
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({id: productId, title: productTitle, price: productPrice, quantity: 1, image_url: productImage});
            }
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            alert(productTitle + ' added to cart!');
        }
        
        function startConversation() {
            sessionStorage.setItem('startChatWith', JSON.stringify({id: <?php echo $product['seller_id']; ?>, name: '<?php echo addslashes($product['seller_name']); ?>'}));
            window.location.href = 'messages.php';
        }
        
        function updateCartCount() {
            let cart = JSON.parse(localStorage.getItem('cart') || '[]');
            let count = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
            let spans = document.querySelectorAll('#cartCount');
            spans.forEach(span => { if (span) span.textContent = count; });
        }
        updateCartCount();
    </script>
</body>
</html>