/**
 * MUSEO - Private Pages Theme Toggle
 * Version simplifiée pour les pages privées (pas besoin de consentement cookie)
 */

(function() {
    'use strict';
    
    // ============================================
    // THEME TOGGLE (Day/Dark Mode)
    // ============================================
    
    function initThemeToggle() {
        // Pour les pages privées, on charge toujours le thème depuis le cookie
        let savedTheme = getCookie('museo_theme') || 'light';
        
        console.log('🎨 Theme au chargement (private):', savedTheme);
        
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);
        
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                console.log('🔄 Changement de thème:', currentTheme, '→', newTheme);
                
                document.documentElement.setAttribute('data-theme', newTheme);
                
                // Sauvegarder le thème (l'utilisateur est authentifié)
                setCookie('museo_theme', newTheme, 365);
                console.log('💾 Cookie museo_theme sauvegardé:', newTheme);
                
                updateThemeIcon(newTheme);
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
    // INITIALIZE ON DOM READY
    // ============================================
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initThemeToggle);
    } else {
        initThemeToggle();
    }
    
})();
