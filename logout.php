<?php
// Configuration de session avant de la démarrer
require_once __DIR__ . '/include/session_config.php';

session_start();

$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

// Supprimer token en DB
if (isset($_COOKIE['remember_token'])) {
    $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE remember_token = ?");
    $stmt->execute([$_COOKIE['remember_token']]);
}

// Vider toutes les variables de session
$_SESSION = array();

// Détruire le cookie de session PHPSESSID de manière agressive
if (isset($_COOKIE[session_name()])) {
    $params = session_get_cookie_params();
    
    // Supprimer avec tous les paramètres possibles
    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

// Supprimer le cookie remember_token de manière agressive  
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

// Détruire complètement la session
session_unset();
session_destroy();

// Redirection immédiate
header("Location: login.php?logout=1");
exit;
?>
