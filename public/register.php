<?php
require "../src/models/UserManager.php";
require "../src/utils/TokenGenerator.php";
require "../src/services/MailService.php";

$pdo = new PDO("mysql:host=localhost;dbname=museo", "root", "");

$userManager = new UserManager($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if ($userManager->userExists($email)) {
        echo "L'email existe déjà !";
        exit;
    }

    $token = TokenGenerator::generate();
    $userManager->createUser($email, $password, $token);

    $mailService = new MailService();
    $mailService->sendActivationMail($email, $token);

    echo "Un email d'activation vous a été envoyé.";
}
?>

<form method="POST">
    <input type="email" name="email" required placeholder="Email">
    <input type="password" name="password" required placeholder="Mot de passe">
    <button type="submit">Créer un compte</button>
</form>
