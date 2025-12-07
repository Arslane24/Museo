<?php
require_once __DIR__ . '/include/auth.php';  // protège la page

// Récupérer l'avatar de l'utilisateur depuis la session
$userAvatar = $_SESSION['user_avatar'] ?? null;
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
        .private-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-xl);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .private-card h1, .private-card h2 {
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
        }
        .private-card p, .private-card li {
            color: var(--gray-700);
            line-height: 1.8;
        }
        .private-card ul {
            list-style: none;
            padding: 0;
        }
        .private-card ul li {
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .private-card ul li:last-child {
            border-bottom: none;
        }
        .private-card b {
            color: var(--secondary-color);
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .private-nav .container {
                flex-direction: column;
                align-items: flex-start;
            }
            .nav-links {
                width: 100%;
                justify-content: space-between;
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
                        <img src="<?= htmlspecialchars($userAvatar) ?>" alt="Avatar">
                    <?php else: ?>
                        <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <span class="nav-welcome">
                    Bienvenue, <b><?= htmlspecialchars($_SESSION['user_name']) ?></b>
                </span>
            </div>
            <div class="nav-links">
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
            </div>
        </div>
    </nav>
