<?php
// Chargement du système (DB + config)
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];
$config = $data['config'];

require_once __DIR__ . '/src/models/UserManager.php';

$userManager = new UserManager($pdo);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- CAPTCHA ---
    $response = $_POST['g-recaptcha-response'] ?? null;

    if (!$response) {
        $message = "Veuillez valider le Captcha.";
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
                $message = "Votre compte n'est pas activé.";
            }
            elseif (!password_verify($password, $user['password'])) {
                $message = "Identifiants incorrects.";
            }
            else {
                // --- Connexion OK ---
                session_start();
                session_regenerate_id(true);

                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_login'] = $user['login'];

                // Redirect to the page user came from or dashboard
                $redirect = $_GET['redirect'] ?? 'private_dash.php';
                // Security: only allow redirects to local pages
                if (strpos($redirect, 'http') === 0 || strpos($redirect, '//') === 0) {
                    $redirect = 'private_dash.php';
                }
                header("Location: " . $redirect);
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - MuseoLink</title>
    <link rel="icon" type="image/x-icon" href="public/images/logo.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/auth-forms.css" rel="stylesheet">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <a href="index.php" class="back-home">
        <i class="fas fa-arrow-left"></i>
        <span>Retour à l'accueil</span>
    </a>
    
    <div class="auth-container">
        <div class="auth-card" data-aos="zoom-in" data-aos-duration="800">

            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-user"></i>
                </div>
                <h1 class="auth-title">Connexion</h1>
                <p class="auth-subtitle">Accédez à votre espace personnel</p>
            </div>

            <!-- Message d’erreur -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger text-center">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['logout'])): ?>
                <div class="alert alert-success text-center">
                    Vous avez été déconnecté avec succès.
                </div>
            <?php endif; ?>

            <form method="POST" id="loginForm">

                <div class="form-group">
                    <label for="identifier" class="form-label">
                        <i class="fas fa-envelope me-2"></i>Email ou Login
                    </label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="text" class="form-control" id="identifier" name="identifier" 
                               placeholder="email ou login" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Mot de passe
                    </label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Votre mot de passe" required>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>

                    <div class="forgot-password">
                        <a href="reset_request.php">Mot de passe oublié ?</a>
                    </div>
                </div>

                <!-- CAPTCHA -->
                <div class="g-recaptcha mb-3" data-sitekey="<?= $config['RECAPTCHA_SITE_KEY'] ?>"></div>
                
                <button type="submit" class="btn btn-auth">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                </button>
            </form>
            
            <div class="auth-divider">
                <span>OU</span>
            </div>
            
            <div class="auth-links">
                <p class="mb-0" style="color: var(--gray-600);">
                    Pas encore de compte ? 
                    <a href="register.php" class="auth-link">Créer un compte</a>
                </p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="js/auth-forms.js"></script>
</body>
</html>
