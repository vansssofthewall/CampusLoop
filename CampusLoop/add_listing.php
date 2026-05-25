<?php
session_start();
include("config.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $price = $_POST["price"];
    $description = trim($_POST["description"]);
    $category = $_POST["category"];
    $listing_type = $_POST["listing_type"];
    $service_duration = isset($_POST["service_duration"]) ? $_POST["service_duration"] : null;
    $service_location = isset($_POST["service_location"]) ? $_POST["service_location"] : null;
    $availability = isset($_POST["availability"]) ? trim($_POST["availability"]) : null;
    
    if (empty($title) || empty($price)) {
        $error = "Please fill in all required fields.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Please enter a valid price.";
    } elseif (!isset($_FILES["image"]) || $_FILES["image"]["error"] != 0) {
        $error = "Please select an image.";
    } else {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $error = "Only JPG, PNG, GIF, WEBP images allowed.";
        } else {
            $imageName = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", basename($_FILES["image"]["name"]));
            $target = "uploads/" . $imageName;
            
            if (!is_dir("uploads")) mkdir("uploads", 0755, true);
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {
                $user_id = $_SESSION["user_id"];
                $stmt = $conn->prepare("INSERT INTO listings (title, price, description, category, image, user_id, listing_type, service_duration, service_location, availability) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sdsssissss", $title, $price, $description, $category, $imageName, $user_id, $listing_type, $service_duration, $service_location, $availability);
                
                if ($stmt->execute()) {
                    $message = "Listing created successfully!";
                } else {
                    $error = "Database error.";
                }
            } else {
                $error = "Failed to upload image.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell an Item - CampusLoop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Form layout fixes */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .form-group.full-width,
        .full-width {
            grid-column: span 2;
        }
        .service-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
            grid-column: span 2;
        }
        .service-full-width {
            grid-column: span 2;
        }
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full-width,
            .full-width {
                grid-column: span 1;
            }
            .service-grid {
                grid-template-columns: 1fr;
            }
            .service-full-width {
                grid-column: span 1;
            }
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
                <a href="add_listing.php" style="color: var(--accent-gold);">Sell</a>
                <a href="my_listings.php">My Items</a>
                <a href="messages.php">Messages</a>
                <a href="order_history.php">Orders</a>
            </div>
            <div class="nav-actions">
                <div class="theme-toggle-container"></div>
                <a href="cart.php" class="btn btn-outline">🛒 Cart <span id="cartCount">0</span></a>
                <span>👋 <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            </div>
        </div>
    </nav>

    <div class="form-page">
        <div class="form-card">
            <h2>Sell an item or service</h2>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">List your item for students to discover</p>
            
            <?php if ($message): ?>
                <div style="background: var(--accent-gold-glow); color: var(--accent-gold-dark); padding: 0.8rem; border-radius: var(--radius); margin-bottom: 1rem;">
                    ✅ <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div style="background: rgba(239, 68, 68, 0.1); color: var(--error); padding: 0.8rem; border-radius: var(--radius); margin-bottom: 1rem;">
                    ❌ <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <!-- Title - full width -->
                    <div class="form-group full-width">
                        <label>Title *</label>
                        <input type="text" name="title" placeholder="e.g., Calculus Textbook or Math Tutoring" required>
                    </div>
                    
                    <!-- Price - half width -->
                    <div class="form-group">
                        <label>Price *</label>
                        <input type="number" step="0.01" name="price" placeholder="0.00" required>
                        <small style="color: var(--text-muted);">For services: price per hour/session</small>
                    </div>
                    
                    <!-- Listing Type - half width -->
                    <div class="form-group">
                        <label>Listing Type *</label>
                        <select name="listing_type" id="listingType" required>
                            <option value="product">📦 Physical Product</option>
                            <option value="service">🎓 Service (Tutoring, etc.)</option>
                        </select>
                    </div>
                    
                    <!-- Category - full width -->
                    <div class="form-group full-width">
                        <label>Category *</label>
                        <select name="category" required>
                            <option value="Textbooks">📚 Textbooks</option>
                            <option value="Notes">📝 Study Notes</option>
                            <option value="Electronics">💻 Electronics</option>
                            <option value="Furniture">🪑 Furniture</option>
                            <option value="Clothing">👕 Clothing</option>
                            <option value="Tutoring">🎓 Tutoring</option>
                            <option value="Other">📦 Other</option>
                        </select>
                    </div>
                    
                    <!-- Service Fields Container -->
                    <div id="serviceFields" style="display: none;" class="full-width">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem;">
                            <div class="form-group">
                                <label>Duration</label>
                                <select name="service_duration">
                                    <option value="30 mins">30 minutes</option>
                                    <option value="1 hour">1 hour</option>
                                    <option value="1.5 hours">1.5 hours</option>
                                    <option value="2 hours">2 hours</option>
                                    <option value="Custom">Custom (message seller)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Location</label>
                                <select name="service_location">
                                    <option value="Online">💻 Online (Zoom/Google Meet)</option>
                                    <option value="Library">📚 Campus Library</option>
                                    <option value="Coffee Shop">☕ Coffee Shop</option>
                                    <option value="Student Center">🏛️ Student Center</option>
                                    <option value="Other">📍 Other (message seller)</option>
                                </select>
                            </div>
                            
                            <div class="form-group" style="grid-column: span 2;">
                                <label>Availability</label>
                                <input type="text" name="availability" placeholder="e.g., Weekdays 3-6pm, Weekends flexible">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description - full width -->
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" rows="5" placeholder="Describe your item or service..."></textarea>
                    </div>
                    
                    <!-- Image - full width -->
                    <div class="form-group full-width">
                        <label>Image *</label>
                        <input type="file" name="image" accept="image/*" required>
                        <small style="color: var(--text-muted);">Accepted formats: JPG, PNG, GIF, WEBP (Max 5MB)</small>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Publish Listing →</button>
                    <a href="index.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
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
        // Toggle service fields based on listing type
        const listingType = document.getElementById('listingType');
        const serviceFields = document.getElementById('serviceFields');
        
        function toggleServiceFields() {
            if (listingType.value === 'service') {
                serviceFields.style.display = 'block';
            } else {
                serviceFields.style.display = 'none';
            }
        }
        
        listingType.addEventListener('change', toggleServiceFields);
        toggleServiceFields();
        
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