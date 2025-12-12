<?php
session_start();

// Connexion à la base de données
$data = require __DIR__ . '/../secret/database.php';
$pdo = $data['pdo'];

// Vérifier l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit;
}

// Vérifier le fichier envoyé
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Fichier manquant ou invalide']);
    exit;
}

$file = $_FILES['avatar'];

// Taille max = 2MB
if ($file['size'] > 2 * 1024 * 1024) {
    header('Content-Type: application/json');
    http_response_code(413);
    echo json_encode(['success' => false, 'error' => 'Taille max 2MB']);
    exit;
}

// Vérification du type MIME
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

$allowed = [
    'image/jpeg',
    'image/png',
    'image/webp'
];

if (!in_array($mime, $allowed)) {
    header('Content-Type: application/json');
    http_response_code(415);
    echo json_encode(['success' => false, 'error' => 'Type non autorisé']);
    exit;
}

// Vérifier que c'est bien une image
$imgInfo = @getimagesize($file['tmp_name']);
if ($imgInfo === false) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Image invalide']);
    exit;
}

// Charger l'image dans une variable binaire (BLOB)
$binaryData = file_get_contents($file['tmp_name']);

$stmt = $pdo->prepare("
    UPDATE users 
    SET avatar = ?, avatar_mime = ?
    WHERE id = ?
");

$stmt->execute([$binaryData, $mime, $_SESSION['user_id']]);

// Mettre à jour la session (URL virtuelle de récupération)
$_SESSION['user_avatar'] = "avatar.php?id=" . $_SESSION['user_id'];

// Réponse JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'url' => $_SESSION['user_avatar']
]);
exit;
