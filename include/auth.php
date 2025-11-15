<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1️⃣ Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

// 2️⃣ Protection contre vol de session (fingerprint)
$fingerprintData = $_SERVER['HTTP_USER_AGENT'];
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $fingerprintData .= $_SERVER['HTTP_ACCEPT_LANGUAGE'];
}
$_fingerprint = hash('sha256', $fingerprintData);

if (!isset($_SESSION['fingerprint'])) {
    $_SESSION['fingerprint'] = $_fingerprint;
} elseif ($_SESSION['fingerprint'] !== $_fingerprint) {
    session_unset();
    session_destroy();
    header("Location: /login.php");
    exit;
}

// 3️⃣ Expiration de session (30 minutes d'inactivité)
$maxInactivity = 1800; // 30 minutes

if (isset($_SESSION['last_activity']) &&
    time() - $_SESSION['last_activity'] > $maxInactivity) {

    session_unset();
    session_destroy();
    header("Location: /login.php?timeout=1");
    exit;
}

$_SESSION['last_activity'] = time();
