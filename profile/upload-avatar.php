<?php
session_start();

require_once __DIR__ . '/../include/auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit;
}

$avatarsDir = __DIR__ . '/../public/images/avatars';
if (!is_dir($avatarsDir)) {
    @mkdir($avatarsDir, 0775, true);
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Fichier manquant ou invalide']);
    exit;
}

$file = $_FILES['avatar'];

if ($file['size'] > 2 * 1024 * 1024) {
    header('Content-Type: application/json');
    http_response_code(413);
    echo json_encode(['success' => false, 'error' => 'Taille max 2MB']);
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

$allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
];

if (!isset($allowed[$mime])) {
    header('Content-Type: application/json');
    http_response_code(415);
    echo json_encode(['success' => false, 'error' => 'Type non autorisé']);
    exit;
}

$ext = $allowed[$mime];

// Vérifier que c'est bien une image
$imgInfo = @getimagesize($file['tmp_name']);
if ($imgInfo === false) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Image invalide']);
    exit;
}

// Redimension à 256px max
$maxSize = 256;
$srcW = $imgInfo[0];
$srcH = $imgInfo[1];
$ratio = min($maxSize / $srcW, $maxSize / $srcH, 1);

$newW = (int) floor($srcW * $ratio);
$newH = (int) floor($srcH * $ratio);

switch ($mime) {
    case 'image/jpeg': $src = imagecreatefromjpeg($file['tmp_name']); break;
    case 'image/png':  $src = imagecreatefrompng($file['tmp_name']); break;
    case 'image/webp': $src = imagecreatefromwebp($file['tmp_name']); break;
}

$dst = imagecreatetruecolor($newW, $newH);
imagealphablending($dst, false);
imagesavealpha($dst, true);

imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

$userId = (int) $_SESSION['user_id'];
$filename = $userId . '.' . $ext;
$path = $avatarsDir . '/' . $filename;

if ($mime === 'image/jpeg') imagejpeg($dst, $path, 85);
elseif ($mime === 'image/png') imagepng($dst, $path, 6);
else imagewebp($dst, $path, 85);

imagedestroy($src);
imagedestroy($dst);

// Enregistrer dans la BD
$data = require __DIR__ . '/../secret/database.php';
$pdo = $data['pdo'];

$publicUrl = '/public/images/avatars/' . $filename . '?v=' . time();

$stmt = $pdo->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
$stmt->execute([$publicUrl, $userId]);

// Mettre à jour la session
$_SESSION['user_avatar'] = $publicUrl;

header('Content-Type: application/json');
echo json_encode(['success' => true, 'url' => $publicUrl]);
exit;
