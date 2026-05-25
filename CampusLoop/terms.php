<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - CampusLoop</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .terms-content {
            max-width: 800px;
            margin: 0 auto;
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            padding: 2.5rem;
            border: 1 px solid var(--border);
        }
        .terms-content h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .terms-content .last-updated {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }
        .terms-content h2 {
            font-size: 1.2rem;
            margin: 1.5rem 0 0.5rem;
            color: var(--accent-gold);
        }
        .terms-content p {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .terms-content ul {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }
        .accept-btn {
            margin-top: 2rem;
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
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
                <a href="add_listings.php">Sell</a>
                <a href="my_listings.php">My Items</a>
                <a href="messages.php">Messages</a>
                <a href="order_history.php">Orders</a>
            </div>
            <div class="nav-action">
                <div class="theme-toggle-container"></div>
                <?php if (isset($_SESSION["username"])): ?>
                    <span>👋 <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <a href="logout.php" class="btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline">Login</a>
                    <a href="register.php" class="btn btn-primary">Sign up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container page-content">
        <div class="terms-content">
            <h1>Terms of Service</h1>
            <div class="last-updated">Last updated: May 20226</div>

            <h2>1. Acceptance of Terms</h2>
            <p>By accessing or using CampusLoop, you agree to be bound by these Terms of Service. If you do not agree, please do not use the platform.</p>

            <h2>2. Eligibility</h2>
            <p>CampusLoop is exclusively for registered university and college students. You must be a currently enrolled student to create an account and use the platorm. False representaion of student status is grounds for immediate termination.</p>

            <h2>3. User Accounts</h2>
            <p>You are responsible for maintaining the confidentiality of your login credentials. You agree to notify us immediately of any unauthorized use of your account. You are solely responsible for all activities that occur under your account.</p>

            <h2>4. Listing Items</h2>
            <ul>
                <li>All items listed must be physical goods or legitimate services (e.g., tutoring).</li>
                <li>Prohibited items include(but are not limited to): illegal goods, weapons, drugs, alcohol, counterfeit items, and stolen property.</li>
                <li>You must describe your items and their conditions accurately.</li>
                <li>You maintain ownership of your listings and can edit or delte them at any time.<li>
            </ul>

            <h2>5. Transactions and Payments</h2>
            <p>CampusLoop is a marketplace connecting buyers and sellers. All transactions occur between users. We do not handle or record payments directly but offer payment methods with PayFast for safe checkouts. Buyers and sellers are responsible for their transactions and any disputes that may arise. We recommend using secure payment methods and communicating clearly with other users. Buyers and sellers are responsible for arrangements of pickup or delivery.</p>

            <h2>6. Fees</h2>
            <p>CampusLoop is completely free to use. No listing fees, no commission fees, no subscription fees. We are committed to keeping our platform accessible and affordable for all students.</p>

            <h2>7. User Responsibilities</h2>
            <ul>
                <li><strong>Buyers:</strong>
                <ul>
                    <li>Inspect items carefully before purchasing.</li>
                    <li>Ask questions through the messaging system.</li>
                    <li>Leave honest feedback after transactions.</li>
                </ul>
                <li><strong>Sellers:</strong>
                <ul>
                    <li>Reply to messages promptly.</li>
                    <li>Description of listed items should be accurate.</li>
                    <li>Mark items as sold when they are no longer available.</li>
                </ul>
                <li><strong>Meeting up:</strong>We recommend meeting in public campus locations during daylight hours.</li>
            </ul>

            <h2>8. Reviews & Ratings</h2>
            <p>Users may leave reviews for sellers after a transaction. Review should be truthful and respectful. False or malicious reviews may result in account suspension.</p>

            <h2>9. Prohibited Conduct</h2>
            <ul>
                <li>Harassing, threatening, or abusing other users.</li>
                <li>Posting false or misleading information</li>
                <li>Trying to defruad other users</li>
                <li>Violating any applicable rules or regulation.</li>
            </ul>

            <h2>10. Content ownership</h2>
            <p>You maintain all rights to the content you post, e.g., listings, messages, reviews. By posting, you allow CampusLoop a non-exclusive license to showcase your content on the platform.</p>

             <h2>11. Moderation & Termination</h2>
            <p>We reserve the right to remove any listing or user account that violates these Terms. Admin decisions are final. We may also suspend accounts for suspicious activity.</p>

            <h2>12. Disclaimer of Warranties</h2>
            <p>CampusLoop is provided "as is" without warranties of any kind. We do not guarantee the accuracy of listings or the reliability of users. Transactions are at your own risk.</p>

            <h2>13. Limitation of Liability</h2>
            <p>To the maximum extent permitted by law, CampusLoop shall not be liable for any indirect, incidental, or consequential damages arising from your use of the Platform.</p>

            <h2>14. Changes to Terms</h2>
            <p>We may update these Terms from time to time. Continued use of the Platform constitutes acceptance of the updated Terms.</p>

            <h2>15. Contact Us</h2>
            <p>If you have any queries about these terms, please contact us at: <a href="support.php" style="color: var(--accent-gold);">support@campusloop.com</a></p>

            <div class="accept-btn">
                <?php if (isset($_SESSION["username"])): ?>
                    <a href="index.php" class="btn btn-primary">I Accept the Terms →</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">Accept & Create Account</a>
                <?php endif; ?>
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













