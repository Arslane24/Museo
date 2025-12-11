<?php
/**
 * MUSEUM MANAGER - Gestion des musées avec la base de données
 */

class MuseumManager {
    
    private $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Recherche avancée de musées avec filtres
     */
    public function searchMuseums($search = '', $category = 'all', $country = '', $limit = 20, $offset = 0) {
        $sql = "SELECT * FROM museums WHERE is_active = 1";
        $params = [];
        
        // Filtre de recherche fulltext
        if (!empty($search)) {
            $sql .= " AND MATCH(name, city, country, description) AGAINST (:search IN NATURAL LANGUAGE MODE)";
            $params[':search'] = $search;
        }
        
        // Filtre par catégorie
        if ($category !== 'all') {
            $sql .= " AND category = :category";
            $params[':category'] = $category;
        }
        
        // Filtre par pays
        if (!empty($country)) {
            $sql .= " AND country = :country";
            $params[':country'] = $country;
        }
        
        $sql .= " ORDER BY rating DESC, total_artworks DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        
        // Bind des paramètres
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Compte le nombre total de musées (pour pagination)
     */
    public function countMuseums($search = '', $category = 'all', $country = '') {
        $sql = "SELECT COUNT(*) as total FROM museums WHERE is_active = 1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND MATCH(name, city, country, description) AGAINST (:search IN NATURAL LANGUAGE MODE)";
            $params[':search'] = $search;
        }
        
        if ($category !== 'all') {
            $sql .= " AND category = :category";
            $params[':category'] = $category;
        }
        
        if (!empty($country)) {
            $sql .= " AND country = :country";
            $params[':country'] = $country;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Récupère un musée par son slug
     */
    public function getMuseumBySlug($slug) {
        $stmt = $this->pdo->prepare("SELECT * FROM museums WHERE slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    /**
     * Récupère un musée par son ID
     */
    public function getMuseumById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM museums WHERE id = ? AND is_active = 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Récupère les pays disponibles
     */
    public function getCountries() {
        $stmt = $this->pdo->query("SELECT DISTINCT country FROM museums WHERE is_active = 1 ORDER BY country");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Récupère les musées par catégorie
     */
    public function getMuseumsByCategory($category, $limit = 6) {
        $stmt = $this->pdo->prepare("SELECT * FROM museums WHERE category = ? AND is_active = 1 ORDER BY rating DESC LIMIT ?");
        $stmt->bindValue(1, $category);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les musées populaires
     */
    public function getPopularMuseums($limit = 6) {
        $stmt = $this->pdo->prepare("SELECT * FROM museums WHERE is_active = 1 ORDER BY rating DESC, total_artworks DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Ajoute un musée aux favoris
     */
    public function addToFavorites($userId, $museumId) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO favorites (user_id, museum_id) VALUES (?, ?)");
            return $stmt->execute([$userId, $museumId]);
        } catch (PDOException $e) {
            // Si déjà en favoris, ignore l'erreur
            return false;
        }
    }
    
    /**
     * Retire un musée des favoris
     */
    public function removeFromFavorites($userId, $museumId) {
        $stmt = $this->pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND museum_id = ?");
        return $stmt->execute([$userId, $museumId]);
    }
    
    /**
     * Vérifie si un musée est en favoris
     */
    public function isFavorite($userId, $museumId) {
        $stmt = $this->pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND museum_id = ?");
        $stmt->execute([$userId, $museumId]);
        return $stmt->fetch() ? true : false;
    }
    
    /**
     * Récupère les favoris d'un utilisateur
     */
    public function getUserFavorites($userId) {
        $sql = "SELECT m.* FROM museums m 
                INNER JOIN favorites f ON m.id = f.museum_id 
                WHERE f.user_id = ? AND m.is_active = 1 
                ORDER BY f.added_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les œuvres d'un musée
     */
    public function getArtworks($museumId, $limit = 12) {
        $stmt = $this->pdo->prepare("SELECT * FROM artworks WHERE museum_id = ? ORDER BY id DESC LIMIT ?");
        $stmt->bindValue(1, (int)$museumId, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Crée une réservation
     */
    public function createBooking($userId, $museumName, $visitDate, $visitTime, $peopleCount, $message = '') {
        
        $sql = "INSERT INTO reservations (user_id, museum_name, visit_date, visit_time, people_count, message) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$userId, $museumName, $visitDate, $visitTime, $peopleCount, $message]);
        
        if ($result) {
            return [
                'success' => true,
                'reservation_id' => $this->pdo->lastInsertId()
            ];
        }
        
        return ['success' => false];
    }
    
    /**
     * Récupère les réservations d'un utilisateur
     */
    public function getUserBookings($userId) {
        $sql = "SELECT r.*, m.city, m.country, m.image_url,
                       m.address, m.latitude, m.longitude
                FROM reservations r
                LEFT JOIN museums m ON r.museum_name = m.name
                WHERE r.user_id = ? 
                ORDER BY r.visit_date DESC, r.visit_time DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
