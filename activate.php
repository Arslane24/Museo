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

$success = true;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activation du compte - MuseoLink</title>
    <link rel="icon" type="image/x-icon" href="public/images/logo.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/auth-forms.css" rel="stylesheet">
</head>
<body>
    <a href="index.php" class="back-home">
        <i class="fas fa-arrow-left"></i>
        <span>Retour à l'accueil</span>
    </a>

    <div class="auth-container">
        <div class="auth-card" data-aos="zoom-in" data-aos-duration="800">

            <div class="auth-header">
                <div class="auth-logo" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="auth-title">Compte activé !</h1>
                <p class="auth-subtitle">Votre compte MuseoLink est maintenant actif</p>
            </div>

            <div class="alert alert-success text-center">
                <i class="fas fa-check-circle me-2"></i>
                Votre compte a été activé avec succès. Vous pouvez maintenant vous connecter.
            </div>

            <a href="login.php" class="btn btn-auth">
                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
            </a>

            <div class="auth-divider"><span>OU</span></div>

            <div class="auth-links">
                <p style="color: var(--gray-600);">
                    <a href="index.php" class="auth-link">
                        <i class="fas fa-home me-1"></i>Retour à l'accueil
                    </a>
                </p>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                once: true
            });
        }
    </script>
</body>
</html>
<?php
