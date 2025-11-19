<?php
// Chargement config + BDD
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];
$config = $data['config'];

require_once __DIR__ . '/src/models/UserManager.php';
require_once __DIR__ . '/src/services/MailService.php';
require_once __DIR__ . '/src/utils/TokenGenerator.php';

$userManager = new UserManager($pdo);
$mail = new MailService($config);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Normaliser les champs
    $login  = strtolower(trim($_POST['login'] ?? ""));
    $nom    = ucfirst(strtolower(trim($_POST['nom'] ?? "")));
    $prenom = ucfirst(strtolower(trim($_POST['prenom'] ?? "")));
    $name   = trim($nom . " " . $prenom);
    $email  = strtolower(trim($_POST['email'] ?? ""));
    $password = trim($_POST['password'] ?? "");

    // CAPTCHA
    $response = $_POST['g-recaptcha-response'] ?? null;
    if (!$response) {
        $message = "Veuillez valider le Captcha.";
    } else {

        $verify = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret={$config['RECAPTCHA_SECRET_KEY']}&response=$response"
        );

        if (!json_decode($verify)->success) {
            $message = "Captcha invalide.";
        } else {

            // Vérif login
            if (!preg_match('/^[a-z][a-z0-9]{2,19}$/', $login)) {
                $message = "Login invalide : minuscules, 3–20 caractères, commence par une lettre.";
            } elseif ($userManager->loginExists($login)) {
                $message = "Ce login est déjà utilisé.";
            }
            // Vérif email
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "Adresse email invalide.";
            } elseif ($userManager->userExists($email)) {
                $message = "Un compte existe déjà avec cet email.";
            }
            // Vérif MDP identiques
            elseif ($_POST['password'] !== $_POST['confirm_password']) {
                $message = "Les mots de passe ne correspondent pas.";
            }
            // Vérif règles légales
            elseif (!isset($_POST['terms'])) {
                $message = "Vous devez accepter les conditions d'utilisation.";
            } else {

                $userManager->cleanOldUnactivatedAccounts();
                $token = TokenGenerator::generate();

                try {
                    $userManager->createUser($name, $login, $email, $password, $token);

                    $mail->sendActivationMail($email, $token);

                    $message = "Compte créé ! Un email d’activation vous a été envoyé.";
                } catch (Exception $e) {
                    $message = $e->getMessage();
                }
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
    <title>Inscription - MUSEO</title>
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
        <div class="auth-card wide" data-aos="zoom-in" data-aos-duration="800">

            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="auth-title">Inscription</h1>
                <p class="auth-subtitle">Créez votre compte MUSEO</p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-info text-center">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="registerForm">

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user-tag me-1"></i>Login (pseudo)</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control" name="login" id="login" required>
                    </div>
                    <p id="login-status"></p>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-user me-1"></i>Nom</label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" name="nom" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-user me-1"></i>Prénom</label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" name="prenom" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-envelope me-1"></i>Email</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <p id="email-status"></p>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-lock me-1"></i>Mot de passe</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <i class="fas fa-eye password-toggle"></i>
                    </div>

                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="password-strength-text" id="strengthText"></div>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-lock me-1"></i>Confirmer</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <i class="fas fa-eye password-toggle"></i>
                    </div>
                </div>

                <div class="g-recaptcha mb-3" data-sitekey="<?= $config['RECAPTCHA_SITE_KEY'] ?>"></div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="terms" required>
                    <label class="form-check-label">J'accepte les conditions d'utilisation</label>
                </div>

                <button type="submit" class="btn btn-auth">
                    <i class="fas fa-user-plus me-2"></i>Créer mon compte
                </button>
            </form>

            <div class="auth-divider"><span>OU</span></div>

            <div class="auth-links">
                <p style="color: var(--gray-600);">
                    Déjà inscrit ?
                    <a href="login.php" class="auth-link">Se connecter</a>
                </p>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="js/auth-forms.js"></script>

    <script>
        // Vérification AJAX login/email
        document.getElementById("login").addEventListener("input", () => {
            let loginValue = document.getElementById("login").value;

            fetch("include/check_user.php?login=" + encodeURIComponent(loginValue))
                .then(r => r.json())
                .then(d => {
                    document.getElementById("login-status").innerHTML =
                        d.login ? "<span style='color:red;'>Login déjà utilisé</span>"
                                : "<span style='color:green;'>Login disponible</span>";
                });
        });

        document.getElementById("email").addEventListener("input", () => {
            let emailValue = document.getElementById("email").value;

            fetch("include/check_user.php?email=" + encodeURIComponent(emailValue))
                .then(r => r.json())
                .then(d => {
                    document.getElementById("email-status").innerHTML =
                        d.email ? "<span style='color:red;'>Email déjà utilisé</span>"
                                : "<span style='color:green;'>Email disponible</span>";
                });
        });
    </script>

</body>

</html>