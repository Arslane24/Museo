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

<h2>Mot de passe oublié</h2>

<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="identifier" placeholder="Email ou Login" required>
    <button type="submit">Réinitialiser</button>
</form>
