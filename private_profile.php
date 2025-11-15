<?php
require_once __DIR__ . '/include/auth.php';  // protège la page
require_once __DIR__ . '/private_nav.php';
?>

<h2>Mon Profil</h2>

<p><b>Nom :</b> <?= htmlspecialchars($_SESSION['user_name']) ?></p>
<p><b>Email :</b> <?= htmlspecialchars($_SESSION['user_email']) ?></p>
