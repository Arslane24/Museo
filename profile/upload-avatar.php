<?php
session_start();

// Require auth
require_once __DIR__ . '/../include/auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit;
}

// Ensure uploads dir exists
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

// MIME validation
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
if (!isset($allowed[$mime])) {
    header('Content-Type: application/json');
    http_response_code(415);
    echo json_encode(['success' => false, 'error' => 'Type non autorisé']);
    exit;
}
$ext = $allowed[$mime];

// Basic image validation
$imgInfo = @getimagesize($file['tmp_name']);
if ($imgInfo === false) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Image invalide']);
    exit;
}

// Resize to max 256x256
$maxSize = 256;
$srcW = $imgInfo[0];
$srcH = $imgInfo[1];
$ratio = min($maxSize / $srcW, $maxSize / $srcH, 1);
$newW = (int) floor($srcW * $ratio);
$newH = (int) floor($srcH * $ratio);

// Create source image
switch ($mime) {
    case 'image/jpeg':
        $src = imagecreatefromjpeg($file['tmp_name']);
        break;
    case 'image/png':
        $src = imagecreatefrompng($file['tmp_name']);
        break;
    case 'image/webp':
        if (!function_exists('imagecreatefromwebp')) {
            header('Content-Type: application/json');
            http_response_code(415);
            echo json_encode(['success' => false, 'error' => 'WEBP non pris en charge']);
            exit;
        }
        $src = imagecreatefromwebp($file['tmp_name']);
        break;
    default:
        header('Content-Type: application/json');
        http_response_code(415);
        echo json_encode(['success' => false, 'error' => 'Type non géré']);
        exit;
}

if (!$src) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Lecture image échouée']);
    exit;
}

$dst = imagecreatetruecolor($newW, $newH);
imagealphablending($dst, false);
imagesavealpha($dst, true);
imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

$userId = (int) $_SESSION['user_id'];
$filename = $userId . '.' . $ext;
$path = $avatarsDir . '/' . $filename;

// Save according to type
$ok = false;
if ($mime === 'image/jpeg') {
    $ok = imagejpeg($dst, $path, 85);
} elseif ($mime === 'image/png') {
    $ok = imagepng($dst, $path, 6);
} else { // webp
    if (function_exists('imagewebp')) {
        $ok = imagewebp($dst, $path, 85);
    }
}

imagedestroy($src);
imagedestroy($dst);

if (!$ok) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Enregistrement échoué']);
    exit;
}

// Update DB avatar_url if column exists
try {
    $data = require __DIR__ . '/../secret/database.php';
    $pdo = $data['pdo'];
    // Check column exists
    $colExists = false;
    $res = $pdo->query("SHOW COLUMNS FROM users LIKE 'avatar_url'");
    if ($res && $res->rowCount() > 0) { $colExists = true; }
    $publicUrl = '/public/images/avatars/' . $filename . '?v=' . time();
    if ($colExists) {
        $stmt = $pdo->prepare('UPDATE users SET avatar_url = ? WHERE id = ?');
        $stmt->execute([$publicUrl, $userId]);
    }
    $_SESSION['user_avatar'] = $publicUrl;
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'url' => $publicUrl]);
    exit;
} catch (Throwable $e) {
    // Still return success with file URL; session updated
    $publicUrl = '/public/images/avatars/' . $filename . '?v=' . time();
    $_SESSION['user_avatar'] = $publicUrl;
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'url' => $publicUrl]);
    exit;
}
