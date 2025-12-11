/**
 * MUSEO - Advanced Styles JavaScript
 * Theme Toggle & Mobile Menu
 */

(function() {
    'use strict';
    
    // ============================================
    // THEME TOGGLE (Day/Dark Mode) - Unified System
    // ============================================
    
    const isDev = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    
    function log(...args) {
        if (isDev) console.log(...args);
    }
    
    function getSystemTheme() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        return 'light';
    }
    
    function applyTheme(theme, broadcast = true) {
        document.documentElement.setAttribute('data-theme', theme);
        document.documentElement.style.colorScheme = theme;
        updateThemeIcon(theme);
        
        // Synchroniser avec les autres onglets
        if (broadcast && window.localStorage) {
            localStorage.setItem('museo_theme_sync', Date.now() + ':' + theme);
        }
    }
    
    function initThemeToggle() {
        const cookieConsent = getCookie('cookie_consent');
        let savedTheme = 'light';
        
        // Respecter le consentement RGPD
        if (cookieConsent === 'accepted') {
            savedTheme = getCookie('museo_theme') || getSystemTheme();
        } else {
            // Sans consentement : vérifier localStorage d'abord, puis préférence système
            if (window.localStorage) {
                const syncData = localStorage.getItem('museo_theme_sync');
                if (syncData) {
                    const parts = syncData.split(':');
                    savedTheme = parts[1] || getSystemTheme();
                } else {
                    savedTheme = getSystemTheme();
                }
            } else {
                savedTheme = getSystemTheme();
            }
        }
        
        log('🎨 Theme au chargement:', savedTheme);
        log('🍪 Cookie consent:', cookieConsent);
        
        applyTheme(savedTheme, false);
        
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                log('🔄 Changement de thème:', currentTheme, '→', newTheme);
                
                // Appliquer le thème visuellement
                document.documentElement.setAttribute('data-theme', newTheme);
                document.documentElement.style.colorScheme = newTheme;
                updateThemeIcon(newTheme);
                
                // Sauvegarder dans localStorage pour synchronisation
                if (window.localStorage) {
                    localStorage.setItem('museo_theme_sync', Date.now() + ':' + newTheme);
                    log('💾 localStorage sauvegardé:', newTheme);
                }
                
                // Sauvegarder dans cookie si consentement accepté
                const consent = getCookie('cookie_consent');
                if (consent === 'accepted') {
                    setCookie('museo_theme', newTheme, 365);
                    log('💾 Cookie sauvegardé:', newTheme);
                } else {
                    log('⚠️ Cookie non sauvegardé (pas de consentement)');
                }
            });
        }
        
        // Synchronisation entre onglets
        if (window.addEventListener && window.localStorage) {
            window.addEventListener('storage', function(e) {
                if (e.key === 'museo_theme_sync' && e.newValue) {
                    const [timestamp, theme] = e.newValue.split(':');
                    if (theme && (theme === 'light' || theme === 'dark')) {
                        log('🔄 Thème synchronisé depuis un autre onglet:', theme);
                        applyTheme(theme, false);
                    }
                }
            });
        }
    }
    
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
    
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
        const isSecure = window.location.protocol === 'https:' ? ';Secure' : '';
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax${isSecure}`;
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
            const isActive = toggler.classList.toggle('active');
            menu.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
            
            // Update aria-expanded for accessibility
            toggler.setAttribute('aria-expanded', isActive);
            
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
                toggler.setAttribute('aria-expanded', 'false');
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
