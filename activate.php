<?php
require_once __DIR__ . '/secret/database.php';
require_once __DIR__ . '/src/models/UserManager.php';

$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

$userManager = new UserManager($pdo);

if (!isset($_GET['token'])) {
    die("Token manquant.");
}

$token = $_GET['token'];

$user = $userManager->getUserByActivationToken($token);

if (!$user) {
    die("Lien d’activation invalide ou expiré.");
}

// Activer le compte
$userManager->activateUser($user['email']);

echo "Votre compte est maintenant activé. 
      <a href='login.php'>Cliquez ici pour vous connecter</a>";
