(function() {
    // Staggered card reveal on scroll
    const cards = document.querySelectorAll('.product-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('revealed');
                }, index * 50);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
    
    cards.forEach(card => {
        observer.observe(card);
    });
    
    // Navbar shrink on scroll
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Cart count update
    window.updateCartCount = function() {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        let count = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
        let spans = document.querySelectorAll('#cartCount');
        spans.forEach(span => {
            if (span) {
                span.textContent = count;
                if (count > 0) {
                    span.style.animation = 'cartBounce 0.3s ease';
                    setTimeout(() => span.style.animation = '', 300);
                }
            }
        });
    };
    
    updateCartCount();
    
    // Add to cart with animation
    window.addToCartWithAnimation = function(id, title, price, imageUrl) {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        let existing = cart.find(item => item.id === id);
        
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({ id, title, price, quantity: 1, image_url: imageUrl });
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        
        // Visual feedback
        const btn = event?.target;
        if (btn) {
            btn.style.transform = 'scale(0.95)';
            setTimeout(() => btn.style.transform = '', 200);
        }
        
        return true;
    };
})();