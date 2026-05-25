document.addEventListener('DOMContentLoaded', function() {
    
    // hover scale and glow on buttons and cards
    const hoverElements = document.querySelectorAll('.btn, .card, .category-tag');
    hoverElements.forEach(el => {
        el.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1)';
        });
    });
    
    // fade-in sequences on headings
    const fadeElements = document.querySelectorAll('.hero h1, .hero p, .hero-badge, .section-header, .hero-buttons');
    fadeElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.animation = `fadeInUp 0.6s ease forwards ${index * 0.1}s`;
    });
    
    // fadeInUp keyframes
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 2px 8px rgba(212, 163, 115, 0.15);
            }
            50% {
                box-shadow: 0 4px 16px rgba(212, 163, 115, 0.35);
            }
        }
        
        .btn-primary {
            animation: pulseGlow 2s ease-in-out infinite;
        }
        
        .card {
            transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.12);
        }
        
        .card:hover .card-image img {
            transform: scale(1.05);
        }
        
        .category-tag {
            transition: all 0.2s ease;
        }
        
        .category-tag:hover {
            transform: translateY(-3px);
            background: #7d9b7a;
            color: white;
        }
    `;
    document.head.appendChild(style);
    
    // slide-in cards (on scroll)
    const slideElements = document.querySelectorAll('.card, .feature-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                entry.target.style.animation = `fadeInUp 0.5s ease forwards ${index * 0.05}s`;
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    slideElements.forEach(el => {
        el.style.opacity = '0';
        observer.observe(el);
    });
    
    // smooth scroll parallax
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const hero = document.querySelector('.hero');
        if (hero) {
            hero.style.transform = `translateY(${scrolled * 0.3}px)`;
            hero.style.opacity = `${1 - scrolled * 0.002}`;
        }
    });
    
    // navbar scroll effect
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.style.background = '#ffffff';
            navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.05)';
        } else {
            navbar.style.background = '#ffffff';
            navbar.style.boxShadow = '0 1px 0 rgba(0, 0, 0, 0.05)';
        }
    });
    
    // cart count animation
    function updateCartCount() {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        let count = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
        const cartCount = document.getElementById('cartCount');
        if (cartCount) {
            cartCount.textContent = count;
            if (count > 0) {
                cartCount.style.display = 'inline-block';
                cartCount.style.animation = 'pulseGlow 0.3s ease';
                setTimeout(() => {
                    cartCount.style.animation = '';
                }, 300);
            } else {
                cartCount.style.display = 'none';
            }
        }
    }
    updateCartCount();
    
    // success message auto-hide
    const successMsg = document.querySelector('.success-message');
    if (successMsg) {
        setTimeout(() => {
            successMsg.style.opacity = '0';
            setTimeout(() => successMsg.style.display = 'none', 300);
        }, 3000);
    }
    
    // button ripple effect
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            const rect = this.getBoundingClientRect();
            ripple.style.left = (e.clientX - rect.left) + 'px';
            ripple.style.top = (e.clientY - rect.top) + 'px';
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });
    
    // Add ripple CSS
    const rippleStyle = document.createElement('style');
    rippleStyle.textContent = `
        .ripple {
            position: absolute;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            transform: scale(0);
            animation: rippleAnim 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes rippleAnim {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .btn {
            position: relative;
            overflow: hidden;
        }
        
        #cartCount {
            background: #e07a5f;
            color: white;
            border-radius: 50%;
            padding: 0.1rem 0.4rem;
            font-size: 0.7rem;
            margin-left: 0.25rem;
        }
    `;
    document.head.appendChild(rippleStyle);
});