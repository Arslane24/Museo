<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger la DB peu importe l’endroit où se trouve auth.php
$root = dirname(__DIR__);  // remonte d’un cran vers /museo
$data = require $root . '/secret/database.php';
$pdo = $data['pdo'];

// -------------------------
// 1️⃣ Reconnexion automatique
// -------------------------
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {

    $stmt = $pdo->prepare("SELECT id, name, login, avatar_url 
                           FROM users 
                           WHERE remember_token = ?");
    $stmt->execute([$_COOKIE['remember_token']]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_login'] = $user['login'];
        $_SESSION['user_avatar'] = $user['avatar_url'] ?? null;
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
