<?php

// Charger la config + PDO (sans array retourné)
$data = require __DIR__ . '/secret/database.php'; 
$pdo = $data['pdo'];     // pdo retourné dans database.php
$config = $data['config'];

require_once __DIR__ . '/src/models/UserManager.php';

// NE PAS démarrer la session maintenant

$userManager = new UserManager($pdo);
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1️⃣ Vérification CAPTCHA
    $response = $_POST['g-recaptcha-response'] ?? null;

    if (!$response) {
        $message = "Captcha manquant.";
    } else {

        $verify = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret={$config['RECAPTCHA_SECRET_KEY']}&response=$response"
        );

        $captcha = json_decode($verify, true);

        if (!$captcha['success']) {
            $message = "Captcha invalide.";
        } else {

            // 2️⃣ IDENTIFIER (email ou login)
            $identifier = trim($_POST['identifier']);
            $password   = trim($_POST['password']);

            $user = $userManager->getUserByLoginOrEmail($identifier);

            if (!$user) {
                $message = "Identifiants incorrects.";
            }
            elseif (!$user['is_active']) {
                $message = "Votre compte n'est pas activé. Vérifiez vos emails.";
            }
            elseif (!password_verify($password, $user['password'])) {
                $message = "Identifiants incorrects.";
            }
            else {

                // 3️⃣ Connexion → démarrer la session UNIQUEMENT ici
                session_start();
                session_regenerate_id(true); // sécurité anti fixation

                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_login'] = $user['login'];

                header("Location: private_dash.php");
                exit;
            }
        }
    }
}
?>

<h2>Connexion</h2>

<?php if (isset($_GET['logout'])): ?>
    <p style="color:green;">Vous avez été déconnecté avec succès.</p>
<?php endif; ?>

<?php if ($message): ?>
    <p style="color:red;"><?= $message ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="identifier" placeholder="Email ou Login" required><br>
    <input type="password" name="password" placeholder="Mot de passe" required><br>

    <div class="g-recaptcha" data-sitekey="<?= $config['RECAPTCHA_SITE_KEY'] ?>"></div>

    <button type="submit">Se connecter</button>
</form>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
