<?php
require_once __DIR__ . '/../secret/database.php';
require_once __DIR__ . '/../src/models/UserManager.php';
require_once __DIR__ . '/../src/services/MailService.php';
require_once __DIR__ . '/../src/utils/TokenGenerator.php';

$userManager = new UserManager($pdo);
$mail = new MailService($config);
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Normaliser le login (toujours en minuscules)
    $login = strtolower(trim($_POST['login']));

    // 1️⃣ Vérification CAPTCHA
    $response = $_POST['g-recaptcha-response'] ?? null;
    if (!$response) {
        $message = "Captcha manquant.";
    } else {
        $verify = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret={$config['RECAPTCHA_SECRET_KEY']}&response=$response"
        );

        if (!json_decode($verify)->success) {
            $message = "Captcha invalide.";
        } else {

            // 2️⃣ Données utilisateur
            $name = trim($_POST['name']);
            $email = strtolower(trim($_POST['email'])); // normaliser email
            $password = trim($_POST['password']);

            // 3️⃣ Vérification LOGIN
            if (!preg_match('/^[a-z][a-z0-9]{2,19}$/', $login)) {
                $message = "Login invalide : minuscules uniquement, commence par une lettre, 3 à 20 caractères.";
            } elseif ($userManager->loginExists($login)) {
                $message = "Ce login est déjà utilisé.";
            }

            // 4️⃣ Vérification EMAIL
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "Adresse email invalide.";
            } elseif ($userManager->userExists($email)) {
                $message = "Un compte existe déjà avec cet email.";
            } else {

                // 5️⃣ Nettoyage des comptes non activés
                $userManager->cleanOldUnactivatedAccounts();

                // 6️⃣ Token
                $token = TokenGenerator::generate();

                // 7️⃣ Création user + envoi mail
                try {
                    // Créer user
                    $userManager->createUser($name, $login, $email, $password, $token);

                    // Envoyer mail
                    $mail->sendActivationMail($email, $token);

                    // Afficher message
                    $message = "Compte créé. Un email d’activation vous a été envoyé.";

                } catch (Exception $e) {

                    // Toutes les erreurs (login invalide, nom invalide, mot de passe trop court, SMTP error…)
                    $message = $e->getMessage();
                }
            }
        }
    }
}
?>

<h2>Créer un compte</h2>

<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="login" id="login" placeholder="Login (minuscules)" required>
    <p id="login-status"></p>

    <input type="text" name="name" placeholder="Nom complet" required><br>

    <input type="email" name="email" id="email" placeholder="Email" required>
    <p id="email-status"></p>

    <input type="password" name="password" placeholder="Mot de passe" required><br>

    <div class="g-recaptcha" data-sitekey="<?= $config['RECAPTCHA_SITE_KEY'] ?>"></div>

    <button type="submit">Créer mon compte</button>
</form>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
    let loginController = null;

    // Petit délai pour éviter de spammer le serveur
    let loginTimeout = null;

    document.getElementById("login").addEventListener("input", function () {
        let login = this.value.toLowerCase();
        let status = document.getElementById("login-status");

        // Normaliser en minuscules
        this.value = login;

        // Règle regex
        let regex = /^[a-z][a-z0-9]{2,19}$/;

        // Si format invalide → on affiche directement, pas d'appel AJAX
        if (!regex.test(login)) {
            status.innerHTML = "<span style='color:red;'>Format invalide (a-z, 0-9, minuscule, 3-20)</span>";
            return;
        }

        // ANNULER la requête précédente si elle existe
        if (loginController) {
            loginController.abort();
        }

        // Nouveau contrôleur pour la nouvelle requête
        loginController = new AbortController();

        // Anti-spam du serveur (petit délai 250ms)
        clearTimeout(loginTimeout);
        loginTimeout = setTimeout(() => {

            fetch("/public/api/check_user.php?login=" + encodeURIComponent(login), {
                signal: loginController.signal
            })
                .then(res => res.json())
                .then(data => {
                    if (data.login) {
                        status.innerHTML = "<span style='color:red;'>Login déjà utilisé</span>";
                    } else {
                        status.innerHTML = "<span style='color:green;'>Login disponible</span>";
                    }
                })
                .catch(err => {
                    // Normal si une requête précédente est annulée
                    if (err.name !== "AbortError")
                        console.error(err);
                });

        }, 250);
    });

    // Vérification email AJAX
    let emailController = null;
    let emailTimeout = null;

    document.getElementById("email").addEventListener("input", function () {
        let email = this.value.trim();
        let status = document.getElementById("email-status");

        // Tant que moins de 5 caractères, on ne dit rien
        if (email.length < 5) {
            status.innerHTML = "";
            return;
        }

        // Regex email professionnelle RFC5322 simplifiée
        let regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        // Format email invalide → message direct
        if (!regex.test(email)) {
            status.innerHTML = "<span style='color:red;'>Format email invalide</span>";
            return;
        }

        // Annuler requête précédente
        if (emailController) {
            emailController.abort();
        }
        emailController = new AbortController();

        // Debounce anti-spam
        clearTimeout(emailTimeout);
        emailTimeout = setTimeout(() => {

            fetch("/public/api/check_user.php?email=" + encodeURIComponent(email), {
                signal: emailController.signal
            })
                .then(res => res.json())
                .then(data => {
                    // data.email === true → email existe déjà
                    if (data.email) {
                        status.innerHTML = "<span style='color:red;'>Cet email est déjà utilisé</span>";
                    } else {
                        status.innerHTML = "<span style='color:green;'>Email disponible</span>";
                    }
                })
                .catch(err => {
                    if (err.name !== "AbortError") {
                        console.error(err);
                    }
                });

        }, 250);
    });
</script>