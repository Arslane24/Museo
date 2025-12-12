<?php
session_start();

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit("Missing ID");
}

$userId = (int) $_GET['id'];

$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

// Récupérer avatar + mime depuis la BD
$stmt = $pdo->prepare("SELECT avatar, avatar_mime FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si pas d'avatar → renvoyer une image vide ou rien
if (!$user || empty($user['avatar'])) {
    http_response_code(404);
    exit("No avatar");
}

// Définir le bon type MIME
header("Content-Type: " . $user['avatar_mime']);
header("Content-Length: " . strlen($user['avatar']));

// Envoyer l'image
echo $user['avatar'];
exit;
