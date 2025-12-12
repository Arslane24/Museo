<?php
/**
 * API ENDPOINT - Recherche de musées AJAX
 */

error_reporting(E_ALL);
ini_set('display_errors', 0); // Ne pas afficher les erreurs en HTML
header('Content-Type: application/json');

// Chargement de la base de données
$data = require __DIR__ . '/../secret/database.php';
$pdo = $data['pdo'];

require_once __DIR__ . '/../src/models/MuseumManager.php';

$museumManager = new MuseumManager($pdo);

// Récupération des paramètres
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? 'all';
$country = $_GET['country'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = isset($_GET['limit']) ? min(1000, max(1, (int)$_GET['limit'])) : 12;
$offset = ($page - 1) * $limit;

// Paramètre pour musée spécifique
$museumId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Mode suggestions (autocomplétion) - si le paramètre 'q' est présent
$suggestionMode = isset($_GET['q']);

try {
    // MODE MUSÉE SPÉCIFIQUE - Charger un seul musée par ID
    if ($museumId) {
        $museum = $museumManager->getMuseumById($museumId);
        
        if (!$museum) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Musée non trouvé'
            ]);
            exit;
        }
        
        // Ajouter l'info favoris
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $museum['is_favorite'] = $museumManager->isFavorite($userId, $museumId);
        }
        
        echo json_encode([
            'success' => true,
            'museum' => $museum
        ]);
        exit;
    }
    
    // MODE SUGGESTIONS - Autocomplétion pour recherche rapide
    if ($suggestionMode) {
        $query = trim($_GET['q']);
        
        if (strlen($query) < 2) {
            echo json_encode([
                'success' => true,
                'suggestions' => []
            ]);
            exit;
        }
        
        // Recherche rapide pour suggestions
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
                is_active = 1 AND (
                    name LIKE :query 
                    OR city LIKE :query 
                    OR country LIKE :query
                    OR address LIKE :query
                )
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
        exit;
    }
    
    // MODE RECHERCHE NORMALE - Avec pagination
    // Recherche des musées
    $museums = $museumManager->searchMuseums($search, $category, $country, $limit, $offset);
    $total = $museumManager->countMuseums($search, $category, $country);
    
    // Vérifier si l'utilisateur est connecté pour les favoris
    $userId = $_SESSION['user_id'] ?? null;
    
    // Ajouter l'info favoris pour chaque musée
    if ($userId) {
        foreach ($museums as &$museum) {
            $museum['is_favorite'] = $museumManager->isFavorite($userId, $museum['id']);
        }
    }
    
    echo json_encode([
        'success' => true,
        'museums' => $museums,
        'pagination' => [
            'current_page' => $page,
            'total_results' => $total,
            'total_pages' => ceil($total / $limit),
            'per_page' => $limit
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la recherche: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}
