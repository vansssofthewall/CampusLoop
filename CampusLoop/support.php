<?php
session_start();
include("config.php");

$message = "";
$error = "";

//handle support form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $order_id = trim($_POST['order_id']);
    $subject = trim($_POST['subject']);
    $support_message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($subject) || empty($support_message)) {
        $error = "Please fill in all required fields.";
    } else {
        // in a real system, this would trigger an email system 
        // we will just show a success messsage
        $message = "Thank you for contacting support. We will get back to you shortly.";

        // clear form
        $name = $email = $order_id = $subject = $support_message = "";
    }
}

// get user's orders if logged in 
$user_orders = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $orders_result = $conn->query("SELECT id, order_number, created_at FROM orders WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 10");
    while ($order = $orders_result->fetch_assoc()) {
        $user_orders[] = $order;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support - CampusLoop</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .support-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .support-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        .info-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            border: 1px solid var(--border);
            margin-bottom: 1.5rem;
        }
        .info-card h3 {
            margin-bottom: 1rem;
            color: var(--accent-gold);
        }
        .info-card p {
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        .faq-item {
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--border-light);
        }
        .faq-item strong {
            display: block;
            margin-bottom: 0.3rem;
        }
        .faq-item p {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }
        .form-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            border: 1px solid var(--border);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.3rem;
            font-weight: 500;
            font-size: 0.8rem;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent-gold);
        }
        .required:after {
            content: " *";
            color: #ef4444;
        }
        @media (max-width: 768px) {
            .support-grid {
                grid-template-columns: 1fr;
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
                <a href="add_listing.php">Sell</a>
                <a href="my_listings.php">My Items</a>
                <a href="messages.php">Messages</a>
                <a href="order_history.php">Orders</a>
            </div>
            <div class="nav-actions">
                <div class="theme-toggle-container"></div>
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

    <div class="container support-container page-content">
        <h1 style="margin-bottom: 0.5rem;">Support Center</h1>
        <p style="color: var(--text-secondary); margin-bottom: 2rem;">Need help with an order? We're here for you.</p>

        <div class="support-grid">
            <!-- Left Column: Info & FAQ -->
            <div>
                <div class="info-card">
                    <h3>📞 Contact Information</h3>
                    <p><strong>Email:</strong> support@campusloop.com</p>
                    <p><strong>Response Time:</strong> Within 24 hours</p>
                    <p><strong>Hours:</strong> Monday - Friday, 9am - 5pm</p>
                </div>

                <div class="info-card">
                    <h3>❓ Frequently Asked Questions</h3>
                    <div class="faq-item">
                        <strong>How do I track my order?</strong>
                        <p>Go to your <a href="order_history.php" style="color: var(--accent-gold);">Order History</a> to view your orders. After payment, sellers will contact you via the messaging system.</p>
                    </div>
                    <div class="faq-item">
                        <strong>Item not as described?</strong>
                        <p>Contact the seller through messages first. If unresolved, submit a support ticket and we'll help mediate.</p>
                    </div>
                    <div class="faq-item">
                        <strong>Payment failed?</strong>
                        <p>Try again with a different card or contact your bank. PayFast Sandbox is for testing only.</p>
                    </div>
                    <div class="faq-item">
                        <strong>Can I cancel my order?</strong>
                        <p>Contact the seller directly via messages. If they agree to cancel, they can refund you directly.</p>
                    </div>
                    <div class="faq-item">
                        <strong>How do I get a refund?</strong>
                        <p>Refunds are handled directly between buyer and seller. We recommend discussing with the seller first.</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Support Form -->
            <div class="form-card">
                <h3 style="margin-bottom: 1rem;">Submit a Support Ticket</h3>
                
                <?php if ($message): ?>
                    <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.8rem; border-radius: var(--radius); margin-bottom: 1rem;">
                        ✅ <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 0.8rem; border-radius: var(--radius); margin-bottom: 1rem;">
                        ❌ <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="required">Your Name</label>
                        <input type="text" name="name" value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="required">Email Address</label>
                        <input type="email" name="email" value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) . '@student.com' : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Order Number (optional)</label>
                        <select name="order_id">
                            <option value="">-- Select an order (optional) --</option>
                            <?php foreach ($user_orders as $order): ?>
                                <option value="<?php echo $order['order_number']; ?>">
                                    <?php echo $order['order_number']; ?> (<?php echo date('M d, Y', strtotime($order['created_at'])); ?>)
                                </option>
                            <?php endforeach; ?>
                            <option value="other">My order isn't listed</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="required">Subject</label>
                        <select name="subject" required>
                            <option value="">-- Select a topic --</option>
                            <option value="payment">Payment Issue</option>
                            <option value="delivery">Delivery / Pickup</option>
                            <option value="item">Item Problem</option>
                            <option value="seller">Seller Issue</option>
                            <option value="account">Account Issue</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="required">Message</label>
                        <textarea name="message" rows="5" placeholder="Please describe your issue in detail..." required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Ticket →</button>
                </form>
                
                <p style="font-size: 0.7rem; color: var(--text-muted); margin-top: 1rem; text-align: center;">
                    We typically respond within 24 hours. For urgent issues, email us directly.
                </p>
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

    <script src="theme.js"></script>
</body>
</html>