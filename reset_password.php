<?php
session_start();

$data = require __DIR__ . "/secret/database.php";
$pdo = $data['pdo'];

require_once __DIR__ . "/src/models/UserManager.php";

$userManager = new UserManager($pdo);
$message = "";

if (!isset($_GET['token'])) {
    die("Token manquant.");
}

$token = $_GET['token'];

// Vérifier que le token est valide et pas expiré
$user = $userManager->validateResetToken($token);

if (!$user) {
    die("Lien invalide ou expiré.");
}

// Si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $password = trim($_POST['password']);

    if (strlen($password) < 6) {
        $message = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        // Mise à jour du mot de passe
        $userManager->updatePassword($user['email'], $password);
        $message = "Votre mot de passe a été réinitialisé.";

        // Redirection après 2 sec
        header("Refresh: 2; URL=login.php");
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe - MUSEO</title>
    <link rel="icon" type="image/x-icon" href="public/images/logo.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/auth-forms.css" rel="stylesheet">
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
                    <i class="fas fa-lock"></i>
                </div>
                <h1 class="auth-title">Nouveau mot de passe</h1>
                <p class="auth-subtitle">Choisissez un mot de passe sécurisé</p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= strpos($message, 'réinitialisé') !== false ? 'success' : 'danger' ?> text-center">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="resetPasswordForm">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Nouveau mot de passe
                    </label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Minimum 6 caractères" required>
                        <i class="fas fa-eye password-toggle"></i>
                    </div>

                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="password-strength-text" id="strengthText"></div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Confirmer le mot de passe
                    </label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Retapez votre mot de passe" required>
                        <i class="fas fa-eye password-toggle"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-auth">
                    <i class="fas fa-check me-2"></i>Changer le mot de passe
                </button>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="js/auth-forms.js"></script>
</body>
</html>
