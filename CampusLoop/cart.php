<?php
session_start();
include("config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - CampusLoop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cart-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .cart-item {
            display: grid;
            grid-template-columns: 80px 2fr 1fr 1fr auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            background: var(--bg-card);
        }
        .cart-item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: var(--radius);
        }
        .cart-item-title {
            font-weight: 500;
        }
        .cart-item-price {
            color: var(--accent-gold);
            font-weight: 600;
        }
        .cart-item-quantity input {
            width: 60px;
            padding: 0.3rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
        }
        .cart-summary {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-top: 1rem;
            border: 1px solid var(--border);
        }
        .empty-cart {
            text-align: center;
            padding: 3rem;
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
        }
        .payfast-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }
        .btn-gold {
            background: var(--accent-gold);
            color: #111;
        }
        .btn-gold:hover {
            background: var(--accent-gold-light);
            transform: translateY(-1px);
        }
        @media (max-width: 768px) {
            .cart-item {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 0.8rem;
            }
            .cart-item-image {
                margin: 0 auto;
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
                <a href="cart.php" class="btn btn-primary">🛒 Cart <span id="cartCount">0</span></a>
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

    <div class="container cart-container page-content">
        <h1 style="margin: 1.5rem 0;">Your Cart</h1>
        <div id="cartContent"></div>
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
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        
        function renderCart() {
            const container = document.getElementById('cartContent');
            
            if (cart.length === 0) {
                container.innerHTML = '<div class="empty-cart">' +
                    '<span style="font-size: 3rem;">🛒</span>' +
                    '<h3 style="margin: 1rem 0;">Your cart is empty</h3>' +
                    '<p>Looks like you haven\'t added anything yet.</p>' +
                    '<a href="index.php" class="btn btn-primary" style="margin-top: 1rem;">Browse Marketplace →</a>' +
                    '</div>';
                updateCartCount();
                return;
            }
            
            let subtotal = 0;
            let html = '<div style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border); overflow: hidden;">';
            
            for (let i = 0; i < cart.length; i++) {
                const item = cart[i];
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                const imageUrl = item.image_url || '';
                let imageHtml = '';
                if (imageUrl) {
                    imageHtml = '<img src="' + imageUrl + '" class="cart-item-image" onerror="this.src=\'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%2394a3b8%22%3E%3Cpath d=%22M4 4h16v16H4V4z%22/%3E%3C/svg%3E\'">';
                } else {
                    imageHtml = '<div class="cart-item-image" style="display: flex; align-items: center; justify-content: center; background: var(--bg-secondary);"><span style="font-size: 1.5rem;">📦</span></div>';
                }
                
                html += '<div class="cart-item">' +
                    '<div>' + imageHtml + '</div>' +
                    '<div class="cart-item-title">' + escapeHtml(item.title) + '</div>' +
                    '<div class="cart-item-price">R ' + item.price.toFixed(2) + '</div>' +
                    '<div class="cart-item-quantity"><input type="number" value="' + item.quantity + '" min="1" max="99" onchange="updateQuantity(' + i + ', this.value)"></div>' +
                    '<div style="font-weight: 600;">R ' + itemTotal.toFixed(2) + '</div>' +
                    '<div><button class="btn btn-outline" style="padding: 0.3rem 0.8rem;" onclick="removeItem(' + i + ')">Remove</button></div>' +
                    '</div>';
            }
            
            html += '</div>' +
                '<div class="cart-summary">' +
                '<div style="font-size: 1.2rem; margin-bottom: 0.5rem;"><strong>Subtotal: R ' + subtotal.toFixed(2) + '</strong></div>' +
                '<p style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 1rem;">Taxes and shipping calculated at checkout</p>' +
                '<div class="payfast-section">' +
                '<h3 style="margin-bottom: 0.5rem;">💳 Pay with PayFast</h3>' +
                '<p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">Test card: 4111 1111 1111 1111 | Exp: Any future date | CVV: 123</p>' +
                '<form method="POST" action="payfast_checkout.php" id="payfastForm">' +
                '<input type="hidden" name="amount" id="payfastAmount" value="">' +
                '<input type="hidden" name="item_name" id="payfastItemName" value="CampusLoop Order">' +
                '<input type="hidden" name="item_description" id="payfastItemDesc" value="">' +
                '<input type="hidden" name="cart_data" id="payfastCartData" value="">' +
                '<button type="submit" class="btn btn-gold" style="width: 100%; padding: 0.8rem;">' +
                '💳 Proceed to PayFast Checkout →' +
                '</button>' +
                '</form>' +
                '</div>' +
                '</div>';
            
            container.innerHTML = html;
            updateCartCount();
            updatePayfastAmount();
        }
        
        function updateQuantity(index, newQuantity) {
            newQuantity = parseInt(newQuantity);
            if (isNaN(newQuantity) || newQuantity < 1) newQuantity = 1;
            if (newQuantity > 99) newQuantity = 99;
            cart[index].quantity = newQuantity;
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
        }
        
        function removeItem(index) {
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
        }
        
        function updatePayfastAmount() {
            const total = cart.reduce(function(sum, item) {
                return sum + (item.price * item.quantity);
            }, 0);
            const amountInput = document.getElementById('payfastAmount');
            const descInput = document.getElementById('payfastItemDesc');
            const cartDataInput = document.getElementById('payfastCartData');
            
            if (amountInput) amountInput.value = total.toFixed(2);
            if (descInput) {
                descInput.value = cart.map(function(item) {
                    return item.title + ' x' + item.quantity;
                }).join(', ');
            }
            if (cartDataInput) cartDataInput.value = JSON.stringify(cart);
        }
        
        function updateCartCount() {
            let count = 0;
            for (let i = 0; i < cart.length; i++) {
                count = count + cart[i].quantity;
            }
            let spans = document.querySelectorAll('#cartCount');
            for (let i = 0; i < spans.length; i++) {
                spans[i].textContent = count;
            }
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        renderCart();
    </script>
</body>
</html>