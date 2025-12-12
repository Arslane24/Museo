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
    <link href="css/style.css" rel="stylesheet">
    <link href="css/private-light-mode.css" rel="stylesheet">
    <style>
        body {
            background: var(--dark-color);
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
            flex-wrap: wrap;
            gap: 1rem;
        }
        .nav-user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
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
        }
        .nav-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .nav-welcome {
            color: var(--gray-700);
            font-size: 0.95rem;
        }
        .nav-welcome b {
            color: var(--secondary-color);
            font-weight: 600;
        }
        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .nav-links a {
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .nav-links a:hover {
            color: var(--secondary-color);
        }
        .nav-links a.active {
            color: var(--secondary-color);
        }
        .nav-links a.logout {
            color: #ef4444;
        }
        .nav-links a.logout:hover {
            color: #dc2626;
        }
        
        /* Theme Toggle Button */
        .theme-toggle {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--gray-700);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.1);
        }
        
        .theme-toggle i {
            font-size: 1.1rem;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--gray-700);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            gap: 4px;
            transition: all 0.3s ease;
        }
        
        .mobile-menu-toggle span {
            width: 20px;
            height: 2px;
            background: var(--gray-700);
            transition: all 0.3s ease;
        }
        
        .mobile-menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        
        .mobile-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }
        
        .mobile-menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }

        @media (max-width: 991px) {
            .mobile-menu-toggle {
                display: flex;
            }
            
            .private-nav .container {
                flex-direction: row;
                justify-content: space-between;
            }
            
            .nav-links {
                position: fixed;
                top: 73px;
                left: -100%;
                width: 280px;
                height: calc(100vh - 73px);
                background: rgba(15, 23, 42, 0.98);
                backdrop-filter: blur(20px);
                flex-direction: column;
                align-items: flex-start;
                padding: 2rem 1.5rem;
                gap: 1rem;
                transition: left 0.3s ease;
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
                overflow-y: auto;
                z-index: 999;
            }
            
            .nav-links.active {
                left: 0;
            }
            
            .nav-links a {
                width: 100%;
                padding: 0.75rem 1rem;
                border-radius: 8px;
                transition: all 0.2s ease;
            }
            
            .nav-links a:hover {
                background: rgba(255, 255, 255, 0.05);
            }
            
            .theme-toggle {
                margin-top: auto;
            }
            
            /* Overlay when menu is open */
            .mobile-overlay {
                position: fixed;
                top: 73px;
                left: 0;
                width: 100%;
                height: calc(100vh - 73px);
                background: rgba(0, 0, 0, 0.5);
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                z-index: 998;
            }
            
            .mobile-overlay.active {
                opacity: 1;
                visibility: visible;
            }
        }

        @media (max-width: 480px) {
            .nav-welcome {
                font-size: 0.85rem;
            }
            
            .nav-avatar {
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>
<body>
    <nav class="private-nav">
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

            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="nav-links" id="navLinks">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home"></i> Accueil
                </a>
                <a href="private_dash.php" class="<?= basename($_SERVER['PHP_SELF']) == 'private_dash.php' ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="private_reservations.php" class="<?= basename($_SERVER['PHP_SELF']) == 'private_reservations.php' ? 'active' : '' ?>">
                    <i class="fas fa-ticket-alt"></i> Réservations
                </a>
                <a href="private_favorites.php" class="<?= basename($_SERVER['PHP_SELF']) == 'private_favorites.php' ? 'active' : '' ?>">
                    <i class="fas fa-heart"></i> Favoris
                </a>
                <a href="private_profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'private_profile.php' ? 'active' : '' ?>">
                    <i class="fas fa-user"></i> Profil
                </a>
                <a href="private_settings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'private_settings.php' ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i> Paramètres
                </a>
                <a href="logout.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
                
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </nav>

    <div class="mobile-overlay" id="mobileOverlay"></div>

    <script src="js/styles_advanced.js"></script>
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('mobileMenuToggle');
            const navLinks = document.getElementById('navLinks');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            if (menuToggle && navLinks) {
                menuToggle.addEventListener('click', function() {
                    this.classList.toggle('active');
                    navLinks.classList.toggle('active');
                    mobileOverlay.classList.toggle('active');
                });
                
                // Close menu when clicking overlay
                mobileOverlay.addEventListener('click', function() {
                    menuToggle.classList.remove('active');
                    navLinks.classList.remove('active');
                    mobileOverlay.classList.remove('active');
                });
                
                // Close menu when clicking on a link
                const links = navLinks.querySelectorAll('a');
                links.forEach(link => {
                    link.addEventListener('click', function() {
                        menuToggle.classList.remove('active');
                        navLinks.classList.remove('active');
                        mobileOverlay.classList.remove('active');
                    });
                });
            }
        });
    </script>