<?php
require_once __DIR__ . '/include/auth.php';  // protège la page
?>
<nav style="padding:10px; background:#222; color:white;">
    <span>Bienvenue, <b><?= htmlspecialchars($_SESSION['user_name']) ?></b></span>

    | <a href="private_dash.php" style="color:#0bf;">Dashboard</a>
    | <a href="private_profile.php" style="color:#0bf;">Mon Profil</a>
    | <a href="private_settings.php" style="color:#0bf;">Paramètres</a>
    | <a href="logout.php" style="color:#f55;">Déconnexion</a>
</nav>
<br>
