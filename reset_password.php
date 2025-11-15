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

<h2>Réinitialisation du mot de passe</h2>

<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>

<form method="POST">
    <input type="password" name="password" placeholder="Nouveau mot de passe" required><br>
    <button type="submit">Changer le mot de passe</button>
</form>
