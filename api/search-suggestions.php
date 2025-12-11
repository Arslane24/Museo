<?php
/**
 * API AJAX - Suggestions de recherche pour l'autocomplétion
 * Retourne les musées correspondant à la recherche en temps réel
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../secret/database.php';

try {
    $data = require __DIR__ . '/../secret/database.php';
    $pdo = $data['pdo'];
    
    // Récupérer le terme de recherche
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    if (strlen($query) < 2) {
        echo json_encode([
            'success' => true,
            'suggestions' => []
        ]);
        exit;
    }
    
    // Rechercher dans la base de données
    $stmt = $pdo->prepare("
        SELECT 
            id,
            name,
            city,
            country,
            address,
            latitude,
            longitude
        FROM museums 
        WHERE 
            name LIKE :query 
            OR city LIKE :query 
            OR country LIKE :query
            OR address LIKE :query
        LIMIT 10
    ");
    
    $searchTerm = '%' . $query . '%';
    $stmt->execute(['query' => $searchTerm]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater les résultats
    $suggestions = array_map(function($museum) {
        return [
            'id' => $museum['id'],
            'name' => $museum['name'],
            'city' => $museum['city'],
            'country' => $museum['country'],
            'address' => $museum['address'],
            'latitude' => $museum['latitude'],
            'longitude' => $museum['longitude'],
            'display' => $museum['name'] . ' - ' . $museum['city'] . ', ' . $museum['country']
        ];
    }, $results);
    
    echo json_encode([
        'success' => true,
        'suggestions' => $suggestions
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
