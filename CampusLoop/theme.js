(function() {
    const savedTheme = localStorage.getItem('theme');
    
    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
    }
    
    function createThemeToggle() {
        const container = document.querySelector('.theme-toggle-container');
        if (!container) return;
        
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        
        container.innerHTML = `
            <button class="theme-toggle-btn" id="themeToggle">
                <span style="font-size: 1rem;">${isDark ? '🌙' : '☀️'}</span>
            </button>
        `;
        
        const btn = document.getElementById('themeToggle');
        if (btn) {
            btn.addEventListener('click', () => {
                const current = document.documentElement.getAttribute('data-theme');
                const newTheme = current === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                const icon = btn.querySelector('span');
                if (icon) {
                    icon.textContent = newTheme === 'dark' ? '🌙' : '☀️';
                }
            });
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createThemeToggle);
    } else {
        createThemeToggle();
    }
})();