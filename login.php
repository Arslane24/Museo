<?php
// ------------------------------------
// Chargement système (DB + config)
// ------------------------------------
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];
$config = $data['config'];

require_once __DIR__ . '/src/models/UserManager.php';
$userManager = new UserManager($pdo);

// Si l'utilisateur a un remember_token ou est déjà connecté, vérifier et rediriger
if (isset($_COOKIE['remember_token'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['user_id'])) {
        header("Location: private_dash.php");
        exit;
    }
}

$message = "";

// Message de timeout si redirigé après expiration de session
if (isset($_GET['timeout']) && $_GET['timeout'] == '1') {
    $message = "Votre session a expiré après 5 minutes d'inactivité. Veuillez vous reconnecter.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CAPTCHA
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

            // IDENTIFIANT
            $identifier = trim($_POST['identifier']);
            $password   = trim($_POST['password']);

            $user = $userManager->getUserByLoginOrEmail($identifier);

            if (!$user || !password_verify($password, $user['password'])) {
                $message = "Identifiants incorrects.";
            }
            elseif (!$user['is_active']) {
                $message = "Votre compte n'est pas activé.";
            }
            else {

                // Configuration de session avant de la démarrer
                require_once __DIR__ . '/include/session_config.php';
                
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                session_regenerate_id(true);

                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_login'] = $user['login'];
                $_SESSION['user_avatar'] = "avatar.php?id=" . $user['id'];
                $_SESSION['last_activity'] = time(); // Initialiser le timestamp pour le timeout

                // --------------------
                // REMEMBER ME
                // --------------------
                if (!empty($_POST['remember'])) {

                    // Token sécurisé
                    $token = bin2hex(random_bytes(32));

                    // Cookie 30 jours
                    setcookie(
                        "remember_token",
                        $token,
                        time() + (30 * 24 * 60 * 60),
                        "/",
                        "",
                        false,
                        true
                    );

                    // Sauvegarde en DB
                    $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                    $stmt->execute([$token, $user['id']]);
                }

                header("Location: private_dash.php");
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
    <link href="css/style.css" rel="stylesheet">
    <link href="css/auth-forms.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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

<a href="index.php" class="back-home">
    <i class="fas fa-arrow-left"></i>
    <span>Retour à l'accueil</span>
</a>

<div class="auth-container">
    <div class="auth-card" data-aos="zoom-in">

        <div class="auth-header">
            <div class="auth-logo"><i class="fas fa-user"></i></div>
            <h1 class="auth-title">Connexion</h1>
            <p class="auth-subtitle">Accédez à votre espace personnel</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['logout'])): ?>
            <div class="alert alert-success text-center">Vous avez été déconnecté.</div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label for="identifier">Email ou login</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="text" class="form-control" name="identifier" id="identifier" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>
            </div>

            <!-- Remember me -->
            <div class="form-check my-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>

            <!-- Forgot Password -->
            <div class="forgot-password">
                <a href="reset_request.php">Mot de passe oublié ?</a>
            </div>

            <!-- CAPTCHA -->
            <div class="g-recaptcha mb-3" data-sitekey="<?= $config['RECAPTCHA_SITE_KEY'] ?>"></div>

            <button type="submit" class="btn btn-auth w-100">
                <i class="fas fa-sign-in-alt me-2"></i> Se connecter
            </button>
        </form>

        <div class="auth-divider"><span>OU</span></div>

        <p class="text-center">Pas encore de compte ?
            <a href="register.php" class="auth-link">Créer un compte</a>
        </p>

    </div>
</div>

</body>
</html>
