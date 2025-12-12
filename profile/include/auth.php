<?php
// Configuration de session avant de la démarrer
require_once __DIR__ . '/session_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger la DB peu importe l’endroit où se trouve auth.php
$root = dirname(__DIR__);  // remonte d’un cran vers /museo
$data = require $root . '/secret/database.php';
$pdo = $data['pdo'];

// -------------------------
// 0️⃣ TIMEOUT DE SESSION - 5 minutes d'inactivité
// -------------------------
$timeout_duration = 300; // 5 minutes en secondes

if (isset($_SESSION['user_id']) && isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    if ($elapsed_time > $timeout_duration) {
        // Session expirée - Déconnexion complète
        
        // Supprimer le remember_token de la base de données
        if (isset($_COOKIE['remember_token'])) {
            $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE remember_token = ?");
            $stmt->execute([$_COOKIE['remember_token']]);
            
            // Supprimer le cookie remember_token
            setcookie('remember_token', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
        
        // Détruire la session
        session_unset();
        session_destroy();
        
        // Supprimer le cookie PHPSESSID
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Rediriger vers login avec message
        header("Location: /login.php?timeout=1");
        exit;
    }
}

// Mettre à jour le timestamp de dernière activité
$_SESSION['last_activity'] = time();

// -------------------------
// 1️⃣ Reconnexion automatique
// -------------------------
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {

    $stmt = $pdo->prepare("SELECT id, name, login 
                           FROM users 
                           WHERE remember_token = ?");
    $stmt->execute([$_COOKIE['remember_token']]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_login'] = $user['login'];
        $_SESSION['user_avatar'] = "avatar.php?id=" . $user['id'];
        $_SESSION['last_activity'] = time(); // Initialiser le timestamp
    }
}

// -------------------------
// 2️⃣ Pas connecté → redirection
// -------------------------
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

// -------------------------
// 3️⃣ Protection fingerprint
// -------------------------
$fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''));

if (!isset($_SESSION['fingerprint'])) {
    $_SESSION['fingerprint'] = $fingerprint;
} elseif ($_SESSION['fingerprint'] !== $fingerprint) {
    session_unset();
    session_destroy();
    header("Location: /login.php");
    exit;
}

// -------------------------
// 4️⃣ Timeout session
// -------------------------
$timeout = 1800;

if (isset($_SESSION['last_activity']) &&
    time() - $_SESSION['last_activity'] > $timeout) {

    session_unset();
    session_destroy();
    header("Location: /login.php?timeout=1");
    exit;
}

$_SESSION['last_activity'] = time();
