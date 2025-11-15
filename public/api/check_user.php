<?php
require_once __DIR__ . '/../../secret/database.php';
require_once __DIR__ . '/../../src/models/UserManager.php';

header('Content-Type: application/json');

$userManager = new UserManager($pdo);

$response = ["login" => false, "email" => false];

if (isset($_GET['login'])) {
    $login = trim($_GET['login']);
    $response["login"] = $userManager->loginExists($login);
}

if (isset($_GET['email'])) {
    $email = trim($_GET['email']);
    $response["email"] = $userManager->userExists($email);
}

echo json_encode($response);
exit;
