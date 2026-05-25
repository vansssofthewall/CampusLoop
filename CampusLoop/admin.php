<?php
session_start();
include("config.php");

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $id = (int)$_GET['delete_user'];
    if ($id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id = $id");
    }
    header("Location: admin.php?tab=users");
    exit;
}

// Handle listing deletion
if (isset($_GET['delete_listing'])) {
    $id = (int)$_GET['delete_listing'];
    $conn->query("DELETE FROM listings WHERE id = $id");
    header("Location: admin.php?tab=listings");
    exit;
}

// Handle role update
if (isset($_GET['update_role'])) {
    $id = (int)$_GET['update_role'];
    $role = $_GET['role'];
    if ($id != $_SESSION['user_id']) {
        $conn->query("UPDATE users SET role = '$role' WHERE id = $id");
    }
    header("Location: admin.php?tab=users");
    exit;
}

$tab = $_GET['tab'] ?? 'dashboard';
$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
$listings = $conn->query("SELECT l.*, u.username FROM listings l JOIN users u ON l.user_id = u.id ORDER BY l.id DESC");
$total_users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_listings = $conn->query("SELECT COUNT(*) as c FROM listings")->fetch_assoc()['c'];
$admin_count = $conn->query("SELECT COUNT(*) as c FROM users WHERE role = 'admin'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - CampusLoop</title>
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
                <a href="order_history.php">📦 Orders</a>
                <a href="admin.php" style="color: var(--primary);">Admin</a>
            </div>
            <div class="nav-actions">
                <div class="theme-toggle-container"></div>
                <span style="color: var(--text-secondary);">👑 <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 style="margin: 1.5rem 0;">Admin Dashboard</h1>
        
        <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <a href="?tab=dashboard" class="btn <?php echo $tab == 'dashboard' ? 'btn-primary' : 'btn-outline'; ?>">Dashboard</a>
            <a href="?tab=users" class="btn <?php echo $tab == 'users' ? 'btn-primary' : 'btn-outline'; ?>">Users</a>
            <a href="?tab=listings" class="btn <?php echo $tab == 'listings' ? 'btn-primary' : 'btn-outline'; ?>">Listings</a>
        </div>
        
        <?php if ($tab == 'dashboard'): ?>
            <div class="stats-grid">
                <div class="stat-card"><div class="stat-number"><?php echo $total_users; ?></div><div>Total Users</div></div>
                <div class="stat-card"><div class="stat-number"><?php echo $admin_count; ?></div><div>Admins</div></div>
                <div class="stat-card"><div class="stat-number"><?php echo $total_users - $admin_count; ?></div><div>Regular Users</div></div>
                <div class="stat-card"><div class="stat-number"><?php echo $total_listings; ?></div><div>Total Listings</div></div>
            </div>
        <?php endif; ?>
        
        <?php if ($tab == 'users'): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr><th>ID</th><th>Username</th><th>Role</th><th>Member Since</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td>
                                <span class="badge <?php echo $row['role'] == 'admin' ? 'badge-primary' : 'badge-secondary'; ?>">
                                    <?php echo $row['role']; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'] ?? 'now')); ?></td>
                            <td>
                                <?php if($row['id'] != $_SESSION['user_id']): ?>
                                    <a href="?update_role=<?php echo $row['id']; ?>&role=<?php echo $row['role'] == 'admin' ? 'user' : 'admin'; ?>&tab=users" class="btn btn-outline" style="padding: 0.2rem 0.6rem;">Change Role</a>
                                    <a href="?delete_user=<?php echo $row['id']; ?>&tab=users" class="btn btn-danger" style="padding: 0.2rem 0.6rem;" onclick="return confirm('Delete this user?')">Delete</a>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">(You)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <?php if ($tab == 'listings'): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr><th>ID</th><th>Image</th><th>Title</th><th>Price</th><th>Seller</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while($row = $listings->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td>
                                <?php if($row['image'] && file_exists("uploads/".$row['image'])): ?>
                                    <img src="uploads/<?php echo $row['image']; ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">
                                <?php else: ?>📷<?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td>R<?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td>
                                <a href="product.php?id=<?php echo $row['id']; ?>" class="btn btn-outline" style="padding: 0.2rem 0.6rem;">View</a>
                                <a href="?delete_listing=<?php echo $row['id']; ?>&tab=listings" class="btn btn-danger" style="padding: 0.2rem 0.6rem;" onclick="return confirm('Delete this listing?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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
</body>
</html>