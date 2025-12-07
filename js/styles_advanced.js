/**
 * MUSEO - Advanced Styles JavaScript
 * Theme Toggle & Mobile Menu
 */

(function() {
    'use strict';
    
    // ============================================
    // THEME TOGGLE (Day/Dark Mode)
    // ============================================
    
    function initThemeToggle() {
        // Get saved theme from localStorage or default to light
        const savedTheme = localStorage.getItem('museo-theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        
        // Update toggle button icon
        updateThemeIcon(savedTheme);
        
        // Theme toggle button click handler
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('museo-theme', newTheme);
                updateThemeIcon(newTheme);
            });
        }
    }
    
    function updateThemeIcon(theme) {
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                if (theme === 'dark') {
                    icon.className = 'fas fa-sun';
                } else {
                    icon.className = 'fas fa-moon';
                }
            }
        }
    }
    
    // ============================================
    // MOBILE MENU TOGGLE
    // ============================================
    
    function initMobileMenu() {
        const toggler = document.querySelector('.navbar-toggler-advanced');
        const menu = document.querySelector('.navbar-nav-advanced');
        const overlay = document.querySelector('.navbar-overlay');
        
        console.log('🍔 Burger Init:', { toggler, menu, overlay });
        
        if (!toggler || !menu) {
            console.error('❌ Burger elements not found!');
            return;
        }
        
        console.log('Burger elements found, attaching events...');
        
        // Toggle menu
        toggler.addEventListener('click', function(e) {
            console.log('🖱️ Burger clicked!');
            e.preventDefault();
            toggler.classList.toggle('active');
            menu.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
            
            // Prevent body scroll when menu is open
            if (menu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
                console.log('📱 Menu opened');
            } else {
                document.body.style.overflow = '';
                console.log('📱 Menu closed');
            }
        });
        
        // Close menu when clicking overlay
        if (overlay) {
            overlay.addEventListener('click', function() {
                toggler.classList.remove('active');
                menu.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
        
        // Close menu when clicking a link
        const navLinks = menu.querySelectorAll('.nav-link-advanced');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                toggler.classList.remove('active');
                menu.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
        
        // Close menu on window resize to desktop
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 991) {
                    toggler.classList.remove('active');
                    menu.classList.remove('active');
                    if (overlay) overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }, 250);
        });
    }
    
    // ============================================
    // NAVBAR SCROLL EFFECT
    // ============================================
    
    function initNavbarScroll() {
        const navbar = document.querySelector('.navbar-advanced');
        if (!navbar) return;
        
        let lastScroll = 0;
        
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll <= 0) {
                navbar.style.boxShadow = 'var(--shadow-md)';
            } else {
                navbar.style.boxShadow = 'var(--shadow-lg)';
            }
            
            lastScroll = currentScroll;
        });
    }
    
    // ============================================
    // INITIALIZE ON DOM READY
    // ============================================
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initThemeToggle();
            initMobileMenu();
            initNavbarScroll();
        });
    } else {
        initThemeToggle();
        initMobileMenu();
        initNavbarScroll();
    }
    
})();
