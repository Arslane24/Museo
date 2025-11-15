<?php
session_start();

// 1️⃣ Vider toutes les variables de session
$_SESSION = [];

// 2️⃣ Supprimer le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3️⃣ Détruire la session
session_destroy();

// ❗ Ne pas rediriger maintenant → on affiche une page "Déconnexion en cours"
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déconnexion...</title>
    <meta http-equiv="refresh" content="2;url=login.php?logout=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<div class="loader"></div>
<h2>Déconnexion en cours...</h2>
<p>Vous allez être redirigé vers la page de connexion.</p>

</body>
</html>
