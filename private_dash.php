<?php
require_once __DIR__ . '/include/auth.php';  // protège la page
require_once __DIR__ . '/private_nav.php';
?>

<h1>Tableau de bord</h1>
<p>Bienvenue dans votre espace privé.</p>

<ul>
    <li>Email : <?= htmlspecialchars($_SESSION['user_email']) ?></li>
    <li>Nom : <?= htmlspecialchars($_SESSION['user_name']) ?></li>
</ul>
