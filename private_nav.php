<?php
require_once __DIR__ . '/include/auth.php';

// Chargement de l’avatar depuis la session
$userAvatar = $_SESSION['user_avatar'] ?? null;

// Ajuster le chemin de l’avatar si besoin
// S’il commence par /public/images/... alors on doit le rendre accessible
if ($userAvatar && str_starts_with($userAvatar, '/public/')) {
    $userAvatar = ltrim($userAvatar, '/'); 
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'MUSEO' ?></title>

    <link rel="icon" type="image/x-icon" href="public/images/logo.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/theme.css" rel="stylesheet">
    
    <!-- Préchargement du thème pour éviter le flash -->
    <script>
        (function() {
            const getCookie = function(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
                return null;
            };
            
            const getSystemTheme = function() {
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    return 'dark';
                }
                return 'light';
            };
            
            const cookieConsent = getCookie('cookie_consent');
            let savedTheme = 'light';
            
            // Respecter le consentement RGPD
            if (cookieConsent === 'accepted') {
                savedTheme = getCookie('museo_theme') || getSystemTheme();
            } else {
                // Sans consentement : utiliser préférence système ou localStorage
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
            
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.documentElement.style.colorScheme = savedTheme;
        })();
    </script>

    <style>
        body {
            background: var(--bg-primary);
            min-height: 100vh;
        }
        
        .private-nav {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .private-nav .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            position: relative;
        }
        
        .nav-user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            z-index: 1001;
        }
        
        .nav-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            border: 2px solid rgba(212, 175, 55, 0.3);
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .nav-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .nav-welcome {
            color: var(--text-secondary);
            font-size: 0.95rem;
            white-space: nowrap;
        }
        
        .nav-welcome b {
            color: var(--brand-secondary);
            font-weight: 600;
        }
        
        /* Burger Menu Button (Hidden on Desktop) */
        .nav-burger {
            display: none;
            flex-direction: column;
            gap: 5px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 8px;
            z-index: 1001;
            transition: all 0.3s ease;
        }
        
        .nav-burger span {
            display: block;
            width: 28px;
            height: 3px;
            background: var(--text-secondary);
            border-radius: 3px;
            transition: all 0.3s ease;
        }
        
        .nav-burger:hover span {
            background: var(--brand-secondary);
        }
        
        .nav-burger.active span:nth-child(1) {
            transform: rotate(45deg) translate(8px, 8px);
        }
        
        .nav-burger.active span:nth-child(2) {
            opacity: 0;
        }
        
        .nav-burger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
        }
        
        /* Navigation Links */
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        
        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 8px;
            white-space: nowrap;
        }
        
        .nav-links a:hover {
            color: var(--brand-secondary);
            background: rgba(201, 169, 97, 0.1);
        }
        
        .nav-links a.active {
            color: var(--brand-secondary);
            background: rgba(201, 169, 97, 0.15);
        }
        
        .nav-links a.logout {
            color: #ef4444;
        }
        
        .nav-links a.logout:hover {
            color: #dc2626;
            background: rgba(239, 68, 68, 0.1);
        }
        
        /* Theme toggle button styles */
        .theme-toggle {
            background: var(--surface-secondary);
            border: 2px solid var(--border-primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition-base);
            color: var(--text-secondary);
            flex-shrink: 0;
        }
        
        .theme-toggle:hover {
            border-color: var(--brand-secondary);
            color: var(--brand-secondary);
            transform: scale(1.05);
        }
        
        .theme-toggle i {
            font-size: 18px;
        }
        
        /* Mobile Overlay */
        .nav-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .nav-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Responsive Styles */
        @media (max-width: 991px) {
            .nav-burger {
                display: flex;
            }
            
            .nav-welcome span {
                display: none;
            }
            
            .nav-welcome::after {
                content: '👋';
                font-size: 1.2rem;
            }
            
            .nav-links {
                position: fixed;
                top: 0;
                right: -100%;
                width: 320px;
                max-width: 85vw;
                height: 100vh;
                background: var(--bg-secondary);
                flex-direction: column;
                align-items: flex-start;
                padding: 5rem 2rem 2rem;
                gap: 0;
                box-shadow: -4px 0 20px rgba(0, 0, 0, 0.3);
                transition: right 0.3s ease;
                overflow-y: auto;
                z-index: 1000;
            }
            
            .nav-links.active {
                right: 0;
            }
            
            .nav-links a {
                width: 100%;
                padding: 1rem;
                font-size: 1.1rem;
                border-bottom: 1px solid var(--border-primary);
            }
            
            .nav-links a i {
                font-size: 1.2rem;
                width: 30px;
            }
            
            .theme-toggle {
                margin-top: 1rem;
                width: 100%;
                height: 50px;
                border-radius: 12px;
                justify-content: flex-start;
                padding-left: 1rem;
                gap: 1rem;
            }
            
            .theme-toggle::after {
                content: 'Changer le thème';
                font-size: 1.1rem;
                font-weight: 500;
            }
            
            .theme-toggle i {
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 576px) {
            .private-nav {
                padding: 0.75rem 0;
            }
            
            .nav-avatar {
                width: 35px;
                height: 35px;
            }
            
            .nav-welcome {
                font-size: 0.85rem;
            }
            
            .nav-links {
                width: 280px;
            }
        }
    </style>
</head>
<body>
    <nav class="private-nav" role="navigation" aria-label="Navigation privée">
        <div class="container">

            <div class="nav-user-info">
                <div class="nav-avatar">
                    <?php if ($userAvatar): ?>
                        <img src="<?= htmlspecialchars($userAvatar) ?>" alt="Avatar utilisateur">
                    <?php else: ?>
                        <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <span class="nav-welcome">
                    Bienvenue, <b><?= htmlspecialchars($_SESSION['user_name']) ?></b>
                </span>
            </div>

            <button class="nav-burger" id="navBurger" aria-label="Toggle menu" aria-expanded="false" aria-controls="navLinks">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="nav-links" id="navLinks" role="menubar">
                <a href="index.php" role="menuitem" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                    <i class="fas fa-home" aria-hidden="true"></i> Accueil
                </a>
                <a href="private_dash.php" role="menuitem" <?= basename($_SERVER['PHP_SELF']) == 'private_dash.php' ? 'aria-current="page"' : '' ?> class="<?= basename($_SERVER['PHP_SELF']) == 'private_dash.php' ? 'active' : '' ?>">
                    <i class="fas fa-th-large" aria-hidden="true"></i> Dashboard
                </a>
                <a href="private_reservations.php" role="menuitem" <?= basename($_SERVER['PHP_SELF']) == 'private_reservations.php' ? 'aria-current="page"' : '' ?> class="<?= basename($_SERVER['PHP_SELF']) == 'private_reservations.php' ? 'active' : '' ?>">
                    <i class="fas fa-ticket-alt" aria-hidden="true"></i> Réservations
                </a>
                <a href="private_favorites.php" role="menuitem" <?= basename($_SERVER['PHP_SELF']) == 'private_favorites.php' ? 'aria-current="page"' : '' ?> class="<?= basename($_SERVER['PHP_SELF']) == 'private_favorites.php' ? 'active' : '' ?>">
                    <i class="fas fa-heart" aria-hidden="true"></i> Favoris
                </a>
                <a href="private_profile.php" role="menuitem" <?= basename($_SERVER['PHP_SELF']) == 'private_profile.php' ? 'aria-current="page"' : '' ?> class="<?= basename($_SERVER['PHP_SELF']) == 'private_profile.php' ? 'active' : '' ?>">
                    <i class="fas fa-user" aria-hidden="true"></i> Profil
                </a>
                <a href="private_settings.php" role="menuitem" <?= basename($_SERVER['PHP_SELF']) == 'private_settings.php' ? 'aria-current="page"' : '' ?> class="<?= basename($_SERVER['PHP_SELF']) == 'private_settings.php' ? 'active' : '' ?>">
                    <i class="fas fa-cog" aria-hidden="true"></i> Paramètres
                </a>
                <a href="logout.php" role="menuitem" class="logout">
                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Déconnexion
                </a>
                <button class="theme-toggle" id="themeToggle" aria-label="Changer le thème">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Overlay -->
    <div class="nav-overlay" id="navOverlay"></div>
    
    <script>
        // Mobile Menu Toggle
        (function() {
            const burger = document.getElementById('navBurger');
            const navLinks = document.getElementById('navLinks');
            const overlay = document.getElementById('navOverlay');
            const links = navLinks.querySelectorAll('a');
            
            if (!burger || !navLinks || !overlay) return;
            
            // Toggle menu
            burger.addEventListener('click', function(e) {
                e.preventDefault();
                const isActive = burger.classList.toggle('active');
                navLinks.classList.toggle('active');
                overlay.classList.toggle('active');
                
                // Update aria-expanded for accessibility
                burger.setAttribute('aria-expanded', isActive);
                
                // Prevent body scroll when menu is open
                if (navLinks.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
            
            // Close menu when clicking overlay
            overlay.addEventListener('click', function() {
                burger.classList.remove('active');
                navLinks.classList.remove('active');
                overlay.classList.remove('active');
                burger.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            });
            
            // Close menu when clicking a link (but NOT the theme toggle button)
            links.forEach(link => {
                link.addEventListener('click', function() {
                    burger.classList.remove('active');
                    navLinks.classList.remove('active');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                });
            });
            
            // Close menu on window resize to desktop
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth > 991) {
                        burger.classList.remove('active');
                        navLinks.classList.remove('active');
                        overlay.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                }, 250);
            });
        })();
        
        // Theme Toggle System
        (function() {
            'use strict';
            
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
                console.log('💾 Cookie définit:', name, '=', value);
                console.log('📋 Tous les cookies:', document.cookie);
            }
            
            function updateThemeIcon(theme) {
                const themeToggle = document.getElementById('themeToggle');
                if (themeToggle) {
                    const icon = themeToggle.querySelector('i');
                    if (icon) {
                        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                    }
                }
            }
            
            function applyTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                document.body.setAttribute('data-theme', theme);
                document.documentElement.style.colorScheme = theme;
                updateThemeIcon(theme);
            }
            
            function getSystemTheme() {
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    return 'dark';
                }
                return 'light';
            }
            
            function toggleTheme() {
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                console.log('🔄 Changement de thème:', currentTheme, '→', newTheme);
                
                applyTheme(newTheme);
                
                // TOUJOURS sauvegarder dans localStorage pour synchronisation entre onglets
                if (window.localStorage) {
                    localStorage.setItem('museo_theme_sync', Date.now() + ':' + newTheme);
                    console.log('💾 localStorage sauvegardé pour sync:', newTheme);
                }
                
                // Sauvegarder dans cookie SEULEMENT si consentement accepté
                const consent = getCookie('cookie_consent');
                if (consent === 'accepted') {
                    setCookie('museo_theme', newTheme, 365);
                    console.log('💾 Cookie sauvegardé:', newTheme);
                } else {
                    console.log('⚠️ Cookie non sauvegardé (pas de consentement)');
                }
            }
            
            function initThemeToggle() {
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                console.log('🎨 Thème au chargement:', currentTheme);
                updateThemeIcon(currentTheme);
                
                const themeToggle = document.getElementById('themeToggle');
                if (themeToggle) {
                    console.log('✅ Bouton de thème trouvé');
                    themeToggle.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('🖱️ Clic détecté sur bouton thème');
                        toggleTheme();
                        return false;
                    };
                } else {
                    console.error('❌ Bouton de thème non trouvé!');
                }
                
                // Synchronisation entre onglets
                if (window.addEventListener && window.localStorage) {
                    window.addEventListener('storage', function(e) {
                        if (e.key === 'museo_theme_sync' && e.newValue) {
                            const parts = e.newValue.split(':');
                            const theme = parts[1];
                            if (theme && (theme === 'light' || theme === 'dark')) {
                                console.log('🔄 Thème synchronisé depuis un autre onglet:', theme);
                                applyTheme(theme);
                            }
                        }
                    });
                }
            }
            
            // Initialiser
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initThemeToggle);
            } else {
                initThemeToggle();
            }
        })();
    </script>
