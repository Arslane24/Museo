<?php

class StatsManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère les statistiques générales d'un utilisateur
     */
    public function getUserStats($userId)
    {
        $stats = [];

        // Total des réservations
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ?");
        $stmt->execute([$userId]);
        $stats['total_reservations'] = $stmt->fetchColumn();

        // Réservations à venir
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND visit_date >= CURDATE()");
        $stmt->execute([$userId]);
        $stats['upcoming'] = $stmt->fetchColumn();

        // Réservations complétées
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND visit_date < CURDATE()");
        $stmt->execute([$userId]);
        $stats['completed'] = $stmt->fetchColumn();

        // Réservations annulées (par défaut 0 car pas de colonne status)
        $stats['cancelled'] = 0;

        // Total dépensé (calculé avec prix adulte des musées * nombre de personnes)
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(people_count), 0) FROM reservations WHERE user_id = ?");
        $stmt->execute([$userId]);
        $stats['total_spent'] = $stmt->fetchColumn() * 15; // Estimation 15€ par personne

        // Nombre de favoris
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ?");
        $stmt->execute([$userId]);
        $stats['favorite_museums'] = $stmt->fetchColumn();

        // Nombre de villes visitées (basé sur museum_name dans reservations)
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT r.museum_name) 
            FROM reservations r
            WHERE r.user_id = ? AND r.visit_date < CURDATE()
        ");
        $stmt->execute([$userId]);
        $stats['cities_visited'] = $stmt->fetchColumn();

        // Prix moyen
        $stats['avg_price'] = $stats['total_reservations'] > 0 ? $stats['total_spent'] / $stats['total_reservations'] : 0;

        // Note moyenne (fictive pour l'instant, à calculer depuis une table reviews si elle existe)
        $stats['avg_rating'] = 4.7;

        return $stats;
    }

    /**
     * Récupère les prochaines visites d'un utilisateur
     */
    public function getUpcomingVisits($userId, $limit = 10)
    {
        $sql = "SELECT r.museum_name as museum, r.visit_date, r.visit_time, 
                       r.people_count, r.message,
                       m.city, m.country, m.image_url
                FROM reservations r
                LEFT JOIN museums m ON r.museum_name = m.name
                WHERE r.user_id = ? AND r.visit_date >= CURDATE()
                ORDER BY r.visit_date ASC, r.visit_time ASC 
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $visits = [];
        while ($row = $stmt->fetch()) {
            $location = ($row['city'] ?? 'France') . ', ' . ($row['country'] ?? 'France');
            $visits[] = [
                'museum' => $row['museum'],
                'date' => $row['visit_date'],
                'time' => substr($row['visit_time'], 0, 5),
                'location' => $location,
                'status' => 'confirmed',
                'tickets' => $row['people_count'] ?? 1,
                'price' => ($row['people_count'] ?? 1) * 15
            ];
        }
        
        return $visits;
    }

    /**
     * Récupère l'activité récente d'un utilisateur
     */
    public function getRecentActivity($userId, $limit = 10)
    {
        $activities = [];
        
        // Réservations récentes
        $sql = "SELECT r.created_at, r.museum_name as museum 
                FROM reservations r
                WHERE r.user_id = ? 
                ORDER BY r.id DESC 
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        while ($row = $stmt->fetch()) {
            $activities[] = [
                'action' => 'Nouvelle réservation',
                'museum' => $row['museum'],
                'date' => $row['created_at'],
                'icon' => 'calendar-plus',
                'color' => 'info'
            ];
        }
        
        // Ajouter les favoris récents (limité à 3)
        $sql = "SELECT m.name as museum, f.added_at
                FROM favorites f 
                INNER JOIN museums m ON f.museum_id = m.id 
                WHERE f.user_id = ? 
                ORDER BY f.id DESC 
                LIMIT 3";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        while ($row = $stmt->fetch()) {
            $activities[] = [
                'action' => 'Musée ajouté aux favoris',
                'museum' => $row['museum'],
                'date' => $row['added_at'] ?? date('Y-m-d'),
                'icon' => 'heart',
                'color' => 'warning'
            ];
        }
        
        return array_slice($activities, 0, $limit);
    }

    /**
     * Récupère les événements du calendrier
     */
    public function getCalendarEvents($userId)
    {
        $sql = "SELECT r.visit_date as date, r.museum_name as museum, r.visit_time as time
                FROM reservations r
                WHERE r.user_id = ? AND r.visit_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) 
                      AND r.visit_date <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH)
                ORDER BY r.visit_date ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        $events = [];
        while ($row = $stmt->fetch()) {
            $events[] = [
                'date' => $row['date'],
                'museum' => $row['museum'],
                'time' => substr($row['time'], 0, 5),
                'type' => 'confirmed'
            ];
        }
        
        return $events;
    }

    /**
     * Récupère les recommandations pour un utilisateur
     */
    public function getRecommendations($userId, $limit = 3)
    {
        $recommendations = [];
        
        // Musées populaires non encore réservés
        $sql = "SELECT m.id, m.name as museum, m.city, m.image_url as image, 
                       COALESCE(m.rating, 4.5) as rating,
                       'Recommandé pour vous' as reason
                FROM museums m 
                WHERE m.id NOT IN (
                    SELECT f.museum_id FROM favorites f WHERE f.user_id = ?
                )
                AND m.is_active = 1
                ORDER BY RAND()
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $recommendations = $stmt->fetchAll();
        
        // Si pas assez de résultats, prendre des musées au hasard
        if (count($recommendations) < $limit) {
            $remaining = $limit - count($recommendations);
            $sql = "SELECT m.id, m.name as museum, m.city, m.image_url as image, 
                           COALESCE(m.rating, 4.3) as rating,
                           'Découverte suggérée' as reason
                    FROM museums m 
                    WHERE m.is_active = 1
                    ORDER BY RAND()
                    LIMIT ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $remaining, PDO::PARAM_INT);
            $stmt->execute();
            $recommendations = array_merge($recommendations, $stmt->fetchAll());
        }
        
        // Si toujours pas assez (aucune réservation), prendre des musées au hasard
        if (count($recommendations) < $limit) {
            $remaining = $limit - count($recommendations);
            $sql = "SELECT m.id, m.name as museum, m.city, m.image_url as image, 4.5 as rating,
                           'Recommandé pour vous' as reason
                    FROM museums m 
                    ORDER BY RAND()
                    LIMIT ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$remaining]);
            $recommendations = array_merge($recommendations, $stmt->fetchAll());
        }
        
        // S'assurer que l'image existe, sinon utiliser une image par défaut
        foreach ($recommendations as &$rec) {
            if (empty($rec['image'])) {
                $rec['image'] = 'https://images.unsplash.com/photo-1566438480900-0609be27a4be?w=300&h=200&fit=crop';
            }
        }
        
        return $recommendations;
    }

    /**
     * Récupère l'historique des visites
     */
    public function getVisitHistory($userId, $limit = 10)
    {
        $sql = "SELECT r.museum_name as museum, r.visit_date as date, 'France' as city, 5 as rating
                FROM reservations r
                WHERE r.user_id = ? AND r.visit_date < CURDATE()
                ORDER BY r.visit_date DESC 
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère les statistiques détaillées des réservations
     */
    public function getReservationStats($userId)
    {
        $stats = [];

        // Total de billets
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(people_count), 0) FROM reservations WHERE user_id = ?");
        $stmt->execute([$userId]);
        $stats['total_tickets'] = $stmt->fetchColumn();

        // Répartition par statut (basé sur les dates)
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND visit_date >= CURDATE()");
        $stmt->execute([$userId]);
        $stats['confirmed'] = $stmt->fetchColumn();

        $stats['pending'] = 0; // Pas de statut pending dans la table

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND visit_date < CURDATE()");
        $stmt->execute([$userId]);
        $stats['completed'] = $stmt->fetchColumn();

        $stats['cancelled'] = 0; // Pas de statut cancelled dans la table

        $stats['all'] = $stats['confirmed'] + $stats['pending'] + $stats['completed'] + $stats['cancelled'];

        // Total dépensé (estimation)
        $stats['total_spent'] = $stats['total_tickets'] * 15;

        // Prix moyen
        $stats['avg_price'] = $stats['all'] > 0 ? $stats['total_spent'] / $stats['all'] : 0;

        return $stats;
    }
}
