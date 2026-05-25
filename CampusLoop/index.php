<?php
session_start();
include("config.php");

if (!isset($_SESSION["username"])) {
    header("Location: landing.php");
    exit;
}

$sql = "SELECT l.*, u.username FROM listings l JOIN users u ON l.user_id = u.id WHERE (l.status IS NULL OR l.status = 'available')";
$params = [];
$types = "";

// Search
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = "%" . trim($_GET['search']) . "%";
    $sql .= " AND (l.title LIKE ? OR l.description LIKE ?)";
    $params[] = $search;
    $params[] = $search;
    $types .= "ss";
}

// Category filter
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $sql .= " AND l.category = ?";
    $params[] = $_GET['category'];
    $types .= "s";
}

// Listing type filter (product or service)
if (isset($_GET['type']) && $_GET['type'] == 'product') {
    $sql .= " AND (l.listing_type IS NULL OR l.listing_type = 'product')";
} elseif (isset($_GET['type']) && $_GET['type'] == 'service') {
    $sql .= " AND l.listing_type = 'service'";
}

// Sort
$sort = $_GET['sort'] ?? 'newest';
switch ($sort) {
    case 'price_low': $sql .= " ORDER BY l.price ASC"; break;
    case 'price_high': $sql .= " ORDER BY l.price DESC"; break;
    default: $sql .= " ORDER BY l.id DESC";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusLoop - Marketplace</title>
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
                <a href="index.php" style="color: var(--accent-gold);">Marketplace</a>
                <a href="add_listing.php">Sell</a>
                <a href="my_listings.php">My Items</a>
                <a href="messages.php">Messages</a>
                <a href="order_history.php">Orders</a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
            </div>
            <div class="nav-actions">
                <div class="theme-toggle-container"></div>
                <a href="cart.php" class="btn btn-outline">🛒 Cart <span id="cartCount">0</span></a>
                <span>👋 <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container page-content">
        <!-- Category & Type Pills -->
        <div class="category-pills">
            <a href="index.php" class="category-pill <?php echo (!isset($_GET['type']) && !isset($_GET['category'])) ? 'active' : ''; ?>">All</a>
            <a href="index.php?type=product" class="category-pill <?php echo (isset($_GET['type']) && $_GET['type'] == 'product') ? 'active' : ''; ?>">📦 Products</a>
            <a href="index.php?type=service" class="category-pill <?php echo (isset($_GET['type']) && $_GET['type'] == 'service') ? 'active' : ''; ?>">🎓 Services</a>
            <a href="index.php?category=Textbooks" class="category-pill <?php echo (isset($_GET['category']) && $_GET['category'] == 'Textbooks') ? 'active' : ''; ?>">📚 Textbooks</a>
            <a href="index.php?category=Notes" class="category-pill <?php echo (isset($_GET['category']) && $_GET['category'] == 'Notes') ? 'active' : ''; ?>">📝 Notes</a>
            <a href="index.php?category=Electronics" class="category-pill <?php echo (isset($_GET['category']) && $_GET['category'] == 'Electronics') ? 'active' : ''; ?>">💻 Electronics</a>
            <a href="index.php?category=Furniture" class="category-pill <?php echo (isset($_GET['category']) && $_GET['category'] == 'Furniture') ? 'active' : ''; ?>">🪑 Furniture</a>
            <a href="index.php?category=Tutoring" class="category-pill <?php echo (isset($_GET['category']) && $_GET['category'] == 'Tutoring') ? 'active' : ''; ?>">🎓 Tutoring</a>
            <a href="index.php?category=Clothing" class="category-pill <?php echo (isset($_GET['category']) && $_GET['category'] == 'Clothing') ? 'active' : ''; ?>">👕 Clothing</a>
        </div>

        <!-- Search & Filter Bar -->
        <div class="filter-bar">
            <form method="GET" id="filterForm" style="display: flex; gap: 0.5rem; width: 100%; flex-wrap: wrap;">
                <input type="text" name="search" class="filter-input" placeholder="Search listings..." value="<?php echo $_GET['search'] ?? ''; ?>">
                <select name="sort" class="filter-select">
                    <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                    <option value="price_low" <?php echo ($sort == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo ($sort == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                </select>
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="index.php" class="btn btn-outline">Clear</a>
            </form>
        </div>

        <!-- Results Count -->
        <div style="margin-bottom: 1rem; font-size: 0.85rem; color: var(--text-secondary);">
            <?php echo $result->num_rows; ?> item<?php echo $result->num_rows != 1 ? 's' : ''; ?> found
        </div>

        <!-- Product Grid -->
        <?php if ($result->num_rows > 0): ?>
            <div class="product-grid">
                <?php while($row = $result->fetch_assoc()): ?>
                    <a href="product.php?id=<?php echo $row['id']; ?>" class="product-card">
                        <div class="product-image">
                            <?php if (!empty($row['image']) && file_exists("uploads/" . $row['image'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>">
                            <?php else: ?>
                                <div style="height: 100%; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary);">
                                    <span style="font-size: 2rem;">📷</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            <div class="product-price">R <?php echo number_format($row['price'], 2); ?></div>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.3rem;">
                                <?php if ($row['listing_type'] == 'service'): ?>
                                    <span style="display: inline-block; background: var(--accent-gold); color: #111; font-size: 0.6rem; padding: 0.2rem 0.5rem; border-radius: 20px;">
                                        🎓 Service
                                    </span>
                                <?php else: ?>
                                    <span style="display: inline-block; background: var(--bg-secondary); color: var(--text-muted); font-size: 0.6rem; padding: 0.2rem 0.5rem; border-radius: 20px;">
                                        📦 Product
                                    </span>
                                <?php endif; ?>
                                <span style="display: inline-block; background: var(--bg-secondary); color: var(--text-muted); font-size: 0.6rem; padding: 0.2rem 0.5rem; border-radius: 20px;">
                                    <?php echo htmlspecialchars($row['category'] ?? 'Other'); ?>
                                </span>
                            </div>
                            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.5rem;">
                                by <?php echo htmlspecialchars($row['username']); ?>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card text-center" style="padding: 3rem;">
                <span style="font-size: 3rem;">🔍</span>
                <h3 style="margin: 1rem 0;">No listings found</h3>
                <p style="color: var(--text-secondary);">Try adjusting your search or filters</p>
                <a href="add_listing.php" class="btn btn-primary" style="margin-top: 1rem;">Create a listing →</a>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <p>© 2026 CampusLoop — Made for students, by students</p>
        <div class="footer-links">
            <a href="terms.php">Terms</a>
            <a href="support.php">Support</a>
        </div>
    </footer>

    <script src="theme.js"></script>
    <script src="animations.js"></script>
    <script>
        function updateCartCount() {
            let cart = JSON.parse(localStorage.getItem('cart') || '[]');
            let count = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
            let spans = document.querySelectorAll('#cartCount');
            spans.forEach(span => { if (span) span.textContent = count; });
        }
        updateCartCount();
        
        document.querySelector('select[name="sort"]')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    </script>
</body>
</html>