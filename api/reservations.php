<?php
/**
 * API ENDPOINT - Gestion des réservations
 * 
 * Méthodes supportées :
 * - GET : Récupérer les réservations d'un utilisateur
 * - POST : Créer une nouvelle réservation
 * - DELETE : Annuler une réservation
 * 
 * Retourne JSON
 */

// Nettoyer tout output buffer
if (ob_get_level()) ob_end_clean();

session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Gérer preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Vous devez être connecté pour gérer les réservations'
    ]);
    exit;
}

try {
    $data = require __DIR__ . '/../secret/database.php';
    $pdo = $data['pdo'];
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de connexion à la base de données',
        'message' => $e->getMessage()
    ]);
    exit;
}

$userId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        
        // ==========================================
        // GET - Récupérer les réservations
        // ==========================================
        case 'GET':
            $stmt = $pdo->prepare("
                SELECT 
                    r.*,
                    m.city as museum_city,
                    m.country as museum_country,
                    m.image_url as museum_image
                FROM reservations r
                LEFT JOIN museums m ON r.museum_name = m.name
                WHERE r.user_id = ?
                ORDER BY r.visit_date DESC
            ");
            $stmt->execute([$userId]);
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'reservations' => $reservations,
                'count' => count($reservations)
            ]);
            break;
        
        // ==========================================
        // POST - Créer une réservation
        // ==========================================
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $museumId = $input['museum_id'] ?? null;
            $visitDate = $input['visit_date'] ?? null;
            $visitTime = $input['visit_time'] ?? null;
            $numberOfPeople = $input['number_of_people'] ?? 1;
            $message = $input['message'] ?? '';
            
            // Validation
            if (!$museumId || !$visitDate) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'ID du musée et date de visite requis'
                ]);
                exit;
            }
            
            // Vérifier que le musée existe et récupérer ses infos
            $stmt = $pdo->prepare("SELECT id, name, city, country FROM museums WHERE id = ?");
            $stmt->execute([$museumId]);
            $museum = $stmt->fetch();
            
            if (!$museum) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Musée introuvable'
                ]);
                exit;
            }
            
            // Insert reservation
            $stmt = $pdo->prepare("
                INSERT INTO reservations 
                (user_id, museum_name, visit_date, visit_time, people_count, message, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                $museum['name'],
                $visitDate,
                $visitTime,
                $numberOfPeople,
                $message
            ]);
            
            $reservationId = $pdo->lastInsertId();
            
            // Générer un code de réservation (pas dans la BDD, juste pour l'affichage)
            $reservationCode = 'MUSEOLINK-' . strtoupper(uniqid());
            
            // Récupérer l'email de l'utilisateur
            $stmt = $pdo->prepare("SELECT email, name FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            // Envoyer l'email de confirmation
            try {
                require_once __DIR__ . '/../src/services/MailService.php';
                $apiKeys = require __DIR__ . '/../secret/api_keys.php';
                
                $mailService = new MailService($apiKeys);
                $mailService->sendReservationConfirmationMail($user['email'], [
                    'museum_name' => $museum['name'],
                    'museum_city' => $museum['city'],
                    'museum_country' => $museum['country'],
                    'visit_date' => $visitDate,
                    'visit_time' => $visitTime,
                    'number_of_people' => $numberOfPeople,
                    'reservation_code' => $reservationCode
                ]);
            } catch (Exception $mailException) {
                // Log l'erreur mais ne fait pas échouer la réservation
                error_log('Erreur envoi email réservation: ' . $mailException->getMessage());
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Réservation créée avec succès',
                'reservation' => [
                    'id' => $reservationId,
                    'reservation_code' => $reservationCode,
                    'museum_name' => $museum['name'],
                    'visit_date' => $visitDate,
                    'visit_time' => $visitTime,
                    'number_of_people' => $numberOfPeople
                ]
            ]);
            break;
        
        // ==========================================
        // DELETE - Annuler une réservation
        // ==========================================
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            $reservationId = $input['reservation_id'] ?? null;
            
            if (!$reservationId) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'ID de réservation requis'
                ]);
                exit;
            }
            
            // Vérifier que la réservation appartient à l'utilisateur
            $stmt = $pdo->prepare("
                SELECT id FROM reservations 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$reservationId, $userId]);
            $reservation = $stmt->fetch();
            
            if (!$reservation) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Réservation introuvable ou non autorisée'
                ]);
                exit;
            }
            
            // Supprimer la réservation (pas de colonne status dans la table)
            $stmt = $pdo->prepare("
                DELETE FROM reservations 
                WHERE id = ?
            ");
            $stmt->execute([$reservationId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Réservation annulée avec succès'
            ]);
            break;
        
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'error' => 'Méthode non autorisée'
            ]);
    }
    
} catch (Exception $e) {
    error_log('Exception in reservations API: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
