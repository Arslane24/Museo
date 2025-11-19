<?php
$pageTitle = 'Dashboard - MUSEO';
require_once __DIR__ . '/include/auth.php';  // protège la page
require_once __DIR__ . '/private_nav.php';
?>

<div class="private-container">
    <div class="private-card">
        <h1><i class="fas fa-th-large me-2"></i>Tableau de bord</h1>
        <p>Bienvenue dans votre espace privé.</p>
    </div>

    <div class="private-card">
        <h2><i class="fas fa-info-circle me-2"></i>Informations du compte</h2>
        <ul>
            <li><b>Email :</b> <?= htmlspecialchars($_SESSION['user_email']) ?></li>
            <li><b>Nom :</b> <?= htmlspecialchars($_SESSION['user_name']) ?></li>
            <li><b>Login :</b> <?= htmlspecialchars($_SESSION['user_login']) ?></li>
        </ul>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
