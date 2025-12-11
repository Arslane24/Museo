<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'not_logged'
    ]);
    exit;
}

$data = require __DIR__ . '/../secret/database.php';
$pdo = $data['pdo'];

require_once __DIR__ . '/../src/models/MuseumManager.php';

$museumId = $_POST['museum_id'] ?? null;

if (!$museumId) {
    echo json_encode([
        'success' => false,
        'error' => 'missing_id'
    ]);
    exit;
}

try {
    $manager = new MuseumManager($pdo);
    $manager->removeFromFavorites($_SESSION['user_id'], $museumId);

    echo json_encode([
        'success' => true,
        'removed' => true
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
