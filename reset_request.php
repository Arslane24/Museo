<?php
session_start();

$data = require __DIR__ . "/secret/database.php";
$pdo = $data['pdo'];
$config = $data['config'];

require_once __DIR__ . "/src/models/UserManager.php";
require_once __DIR__ . "/src/services/MailService.php";
require_once __DIR__ . "/src/utils/TokenGenerator.php";

$userManager = new UserManager($pdo);
$mailer = new MailService($config);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $identifier = trim($_POST['identifier']); // email ou login

    // On cherche par email OU login
    $user = $userManager->getUserByLoginOrEmail($identifier);

    // On donne TOUJOURS la même réponse (sécurité)
    $message = "Si un compte existe, un email de réinitialisation a été envoyé.";

    if ($user) {

        $token = TokenGenerator::generate();

        // On stocke le token
        $userManager->setResetToken($user['email'], $token);

        // On envoie le mail
        try {
            $mailer->sendResetMail($user['email'], $token);
        }
        catch (Exception $e) {
            // On ne dit pas que ça a échoué → même message (sécurité)
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - MuseoLink</title>
    <link rel="icon" type="image/x-icon" href="public/images/logo.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/auth-forms.css" rel="stylesheet">
    <script>
        // Détecter et appliquer le thème au chargement de la page
        (function() {
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
                return null;
            }
            
            const cookieConsent = getCookie('cookie_consent');
            let savedTheme = 'light';
            
            if (cookieConsent === 'accepted') {
                savedTheme = getCookie('museo_theme') || 'light';
            }
            
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>
    <a href="login.php" class="back-home">
        <i class="fas fa-arrow-left"></i>
        <span>Retour à la connexion</span>
    </a>

    <div class="auth-container">
        <div class="auth-card" data-aos="zoom-in" data-aos-duration="800">

            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-key"></i>
                </div>
                <h1 class="auth-title">Mot de passe oublié</h1>
                <p class="auth-subtitle">Entrez votre email ou login pour réinitialiser</p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-info text-center">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="resetRequestForm">
                <div class="form-group">
                    <label class="form-label" for="identifier">
                        <i class="fas fa-envelope me-2"></i>Email ou Login
                    </label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="text" class="form-control" name="identifier" id="identifier" 
                               placeholder="Votre email ou login" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-auth">
                    <i class="fas fa-paper-plane me-2"></i>Envoyer le lien
                </button>
            </form>

            <div class="auth-divider"><span>OU</span></div>

            <div class="auth-links">
                <p style="color: var(--gray-600);">
                    Vous vous souvenez de votre mot de passe ?
                    <a href="login.php" class="auth-link">Se connecter</a>
                </p>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="js/auth-forms.js"></script>
</body>
</html>
