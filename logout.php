<?php
session_start();

$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

// Supprimer token en DB
if (isset($_COOKIE['remember_token'])) {
    $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE remember_token = ?");
    $stmt->execute([$_COOKIE['remember_token']]);
}

// Supprimer le cookie remember_token avec les mêmes paramètres
setcookie("remember_token", "", time() - 3600, "/", "", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', true);

// Vider toutes les variables de session
$_SESSION = array();

// Supprimer le cookie de session PHPSESSID
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        [
            'expires' => time() - 42000,
            'path' => $params["path"],
            'domain' => $params["domain"],
            'secure' => $params["secure"],
            'httponly' => $params["httponly"],
            'samesite' => 'Lax'
        ]
    );
}

// Détruire la session
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Déconnexion...</title>
</head>
<body>
<script>
// Supprimer tous les cookies côté client avant la redirection
function deleteCookie(name) {
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
}

// Supprimer les cookies
deleteCookie('remember_token');
deleteCookie('PHPSESSID');

// Rediriger immédiatement
window.location.href = 'login.php?logout=1';
</script>
</body>
</html>
