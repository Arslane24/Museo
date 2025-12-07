<?php
$pageTitle = 'Dashboard - MUSEO';
require_once __DIR__ . '/include/auth.php';  // protège la page
require_once __DIR__ . '/private_nav.php';

// Connexion à la base de données
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

// Charger les managers
require_once __DIR__ . '/src/models/StatsManager.php';
require_once __DIR__ . '/src/models/MuseumManager.php';

$statsManager = new StatsManager($pdo);
$userId = $_SESSION['user_id'];

// Récupérer l'avatar de l'utilisateur depuis la session
$userAvatar = $_SESSION['user_avatar'] ?? null;

// Récupérer les données depuis la base de données
$stats = $statsManager->getUserStats($userId);
$upcomingVisits = $statsManager->getUpcomingVisits($userId, 3);
$recentActivity = $statsManager->getRecentActivity($userId, 5);
$calendarEvents = $statsManager->getCalendarEvents($userId);
$recommendations = $statsManager->getRecommendations($userId, 3);
?>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-xl);
    padding: 1.5rem;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: var(--gradient-accent);
    opacity: 0.1;
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: var(--secondary-color);
    box-shadow: 0 10px 30px rgba(212, 175, 55, 0.2);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon.primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon.danger { background: linear-gradient(135deg, #ef4444, #dc2626); }

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--secondary-color);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--gray-600);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.action-btn {
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-lg);
    padding: 1rem 1.5rem;
    color: var(--gray-700);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: var(--transition);
    font-weight: 500;
}

.action-btn:hover {
    background: var(--gradient-primary);
    color: white;
    transform: translateX(5px);
    border-color: var(--secondary-color);
}

.action-btn i {
    font-size: 1.5rem;
    color: var(--secondary-color);
}

.action-btn:hover i {
    color: white;
}

.upcoming-visits {
    margin-bottom: 2rem;
}

.visit-item {
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-lg);
    padding: 1.25rem;
    margin-bottom: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    transition: var(--transition);
}

.visit-item:hover {
    border-color: var(--secondary-color);
    background: rgba(212, 175, 55, 0.05);
}

.visit-info {
    flex: 1;
}

.visit-museum {
    color: var(--secondary-color);
    font-weight: 600;
    font-size: 1.125rem;
    margin-bottom: 0.5rem;
}

.visit-details {
    color: var(--gray-600);
    font-size: 0.875rem;
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.visit-details span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.visit-badge {
    padding: 0.375rem 0.875rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-confirmed {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.badge-pending {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.activity-timeline {
    position: relative;
    padding-left: 2rem;
}

.activity-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.activity-item::before {
    content: '';
    position: absolute;
    left: -1.875rem;
    top: 0.5rem;
    width: 2px;
    height: calc(100% - 0.5rem);
    background: rgba(255, 255, 255, 0.1);
}

.activity-item:last-child::before {
    display: none;
}

.activity-icon {
    position: absolute;
    left: -2.25rem;
    top: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    border: 2px solid var(--dark-color);
}

.activity-icon.success { background: #10b981; color: white; }
.activity-icon.info { background: #3b82f6; color: white; }
.activity-icon.warning { background: #f59e0b; color: white; }

.activity-content h4 {
    color: var(--gray-700);
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.activity-content p {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin: 0;
}

.activity-date {
    color: var(--gray-500);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-title {
    color: var(--secondary-color);
    font-size: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.view-all-link {
    color: var(--gray-600);
    text-decoration: none;
    font-size: 0.875rem;
    transition: var(--transition);
}

.view-all-link:hover {
    color: var(--secondary-color);
}

.welcome-banner {
    background: var(--gradient-primary);
    border-radius: var(--border-radius-xl);
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.welcome-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.3) 0%, transparent 70%);
    border-radius: 50%;
}

.welcome-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: white;
    padding: 0.25rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    margin-bottom: 1rem;
}

.welcome-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.welcome-avatar-letter {
    width: 100%;
    height: 100%;
    background: var(--gradient-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    border-radius: 50%;
}

.welcome-content h1 {
    color: white;
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.welcome-content p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.125rem;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .visit-item {
        flex-direction: column;
        align-items: flex-start;
    }
}

/* Styles pour le calendrier */
#calendar {
    background: rgba(255, 255, 255, 0.02);
    border-radius: var(--border-radius);
    padding: 1rem;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.calendar-header h3 {
    color: var(--secondary-color);
    font-size: 1.125rem;
    margin: 0;
}

.calendar-nav {
    display: flex;
    gap: 0.5rem;
}

.calendar-nav button {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: var(--gray-700);
    width: 32px;
    height: 32px;
    border-radius: 6px;
    cursor: pointer;
    transition: var(--transition);
}

.calendar-nav button:hover {
    background: var(--gradient-accent);
    color: white;
    border-color: var(--secondary-color);
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.calendar-weekday {
    text-align: center;
    color: var(--gray-600);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    padding: 0.5rem 0;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    color: var(--gray-700);
    font-size: 0.875rem;
    cursor: pointer;
    transition: var(--transition);
    position: relative;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid transparent;
}

.calendar-day:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(212, 175, 55, 0.3);
}

.calendar-day.today {
    border-color: var(--secondary-color);
    font-weight: 700;
}

.calendar-day.other-month {
    color: var(--gray-500);
    opacity: 0.5;
}

.calendar-day.has-event {
    background: rgba(16, 185, 129, 0.2);
    border-color: #10b981;
}

.calendar-day.has-event.pending {
    background: rgba(245, 158, 11, 0.2);
    border-color: #f59e0b;
}

.calendar-day.has-event::after {
    content: '';
    position: absolute;
    bottom: 4px;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: #10b981;
}

.calendar-day.has-event.pending::after {
    background: #f59e0b;
}

.calendar-legend {
    display: flex;
    gap: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray-600);
    font-size: 0.875rem;
}

.legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

/* Statistiques détaillées */
.stats-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-detail-item {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius);
    padding: 1rem;
    transition: var(--transition);
}

.stat-detail-item:hover {
    border-color: rgba(212, 175, 55, 0.3);
    background: rgba(255, 255, 255, 0.05);
}

.stat-detail-label {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stat-detail-value {
    color: var(--secondary-color);
    font-size: 1.5rem;
    font-weight: 700;
}

/* Recommandations */
.recommendation-card {
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    transition: var(--transition);
    height: 100%;
}

.recommendation-card:hover {
    transform: translateY(-5px);
    border-color: var(--secondary-color);
    box-shadow: 0 10px 30px rgba(212, 175, 55, 0.2);
}

.recommendation-image {
    height: 150px;
    background-size: cover;
    background-position: center;
    position: relative;
}

.recommendation-badge {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
    color: #fbbf24;
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.recommendation-content {
    padding: 1rem;
}

.recommendation-content h4 {
    color: var(--gray-700);
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.recommendation-content p {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.btn-recommendation {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--gradient-accent);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    transition: var(--transition);
}

.btn-recommendation:hover {
    transform: translateX(5px);
    color: white;
}
</style>

<div class="container py-4">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="welcome-content">
            <div class="welcome-avatar">
                <?php if ($userAvatar): ?>
                    <img src="<?= htmlspecialchars($userAvatar) ?>" alt="Avatar">
                <?php else: ?>
                    <div class="welcome-avatar-letter">
                        <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <h1>Bonjour, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?> 👋</h1>
            <p>Bienvenue dans votre espace personnel MUSEO</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value"><?= $stats['total_reservations'] ?></div>
                    <div class="stat-label">Réservations totales</div>
                </div>
                <div class="stat-icon primary">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value"><?= $stats['upcoming'] ?></div>
                    <div class="stat-label">Visites à venir</div>
                </div>
                <div class="stat-icon success">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value"><?= $stats['completed'] ?></div>
                    <div class="stat-label">Visites complétées</div>
                </div>
                <div class="stat-icon warning">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value"><?= count(['Musée Rodin', 'Musée Picasso', 'Musée Carnavalet']) ?></div>
                    <div class="stat-label">Musées favoris</div>
                </div>
                <div class="stat-icon danger">
                    <i class="fas fa-heart"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value"><?= number_format($stats['total_spent'], 2) ?>€</div>
                    <div class="stat-label">Dépenses totales</div>
                </div>
                <div class="stat-icon primary">
                    <i class="fas fa-euro-sign"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value"><?= $stats['cities_visited'] ?></div>
                    <div class="stat-label">Villes visitées</div>
                </div>
                <div class="stat-icon success">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value"><?= $stats['avg_rating'] ?><span style="font-size: 1rem;">/5</span></div>
                    <div class="stat-label">Note moyenne</div>
                </div>
                <div class="stat-icon warning">
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendrier et informations en 2 colonnes -->
    <div class="row g-4 mb-4">
        <!-- Calendrier -->
        <div class="col-lg-6">
            <div class="private-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-calendar-alt"></i>
                        Calendrier des visites
                    </h2>
                </div>
                <div id="calendar"></div>
                <div class="calendar-legend mt-3">
                    <span class="legend-item">
                        <span class="legend-dot" style="background: #10b981;"></span>
                        Confirmé
                    </span>
                    <span class="legend-item">
                        <span class="legend-dot" style="background: #f59e0b;"></span>
                        En attente
                    </span>
                </div>
            </div>
        </div>

        <!-- Statistiques détaillées -->
        <div class="col-lg-6">
            <div class="private-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-chart-pie"></i>
                        Statistiques détaillées
                    </h2>
                </div>
                
                <div class="stats-details">
                    <div class="stat-detail-item">
                        <div class="stat-detail-label">
                            <i class="fas fa-ticket-alt text-primary"></i>
                            Billets achetés
                        </div>
                        <div class="stat-detail-value">
                            <?php 
                            $totalTickets = array_sum(array_column($upcomingVisits, 'tickets'));
                            echo $totalTickets + 15; // 15 billets passés
                            ?>
                        </div>
                    </div>
                    
                    <div class="stat-detail-item">
                        <div class="stat-detail-label">
                            <i class="fas fa-wallet text-success"></i>
                            Budget moyen par visite
                        </div>
                        <div class="stat-detail-value">
                            <?= $stats['total_reservations'] > 0 ? number_format($stats['total_spent'] / $stats['total_reservations'], 2) : '0.00' ?>€
                        </div>
                    </div>
                    
                    <div class="stat-detail-item">
                        <div class="stat-detail-label">
                            <i class="fas fa-trophy text-warning"></i>
                            Rang du membre
                        </div>
                        <div class="stat-detail-value">
                            <span class="badge" style="background: linear-gradient(135deg, #d4af37, #f4e5a1); color: #1a4d7a; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600;">
                                <i class="fas fa-crown"></i> Premium
                            </span>
                        </div>
                    </div>
                    
                    <div class="stat-detail-item">
                        <div class="stat-detail-label">
                            <i class="fas fa-percent text-info"></i>
                            Taux de complétion
                        </div>
                        <div class="stat-detail-value">
                            <?php $completionRate = $stats['total_reservations'] > 0 ? round(($stats['completed'] / $stats['total_reservations']) * 100) : 0; ?>
                            <?= $completionRate ?>%
                        </div>
                        <div class="progress mt-2" style="height: 8px; background: rgba(255,255,255,0.1);">
                            <div class="progress-bar" style="width: <?= $completionRate ?>%; background: linear-gradient(90deg, #10b981, #059669);"></div>
                        </div>
                    </div>

                    <div class="stat-detail-item">
                        <div class="stat-detail-label">
                            <i class="fas fa-clock text-danger"></i>
                            Prochaine visite dans
                        </div>
                        <div class="stat-detail-value">
                            <?php
                            if (!empty($upcomingVisits) && isset($upcomingVisits[0]['date'])) {
                                $nextVisit = strtotime($upcomingVisits[0]['date']);
                                $today = time();
                                $diff = $nextVisit - $today;
                                $days = floor($diff / (60 * 60 * 24));
                                echo $days . ' jour' . ($days > 1 ? 's' : '');
                            } else {
                                echo 'Aucune visite prévue';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommandations personnalisées -->
    <div class="private-card mb-4">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-magic"></i>
                Recommandations pour vous
            </h2>
        </div>
        <div class="row g-3">
            <?php foreach ($recommendations as $rec): ?>
            <div class="col-md-4">
                <div class="recommendation-card">
                    <div class="recommendation-image" style="background-image: url('<?= $rec['image'] ?>');">
                        <span class="recommendation-badge">
                            <i class="fas fa-star"></i> <?= $rec['rating'] ?>
                        </span>
                    </div>
                    <div class="recommendation-content">
                        <h4><?= htmlspecialchars($rec['museum']) ?></h4>
                        <p><i class="fas fa-lightbulb"></i> <?= $rec['reason'] ?></p>
                        <a href="Explorer.php" class="btn-recommendation">
                            Découvrir <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="private-card">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-bolt"></i>
                Actions rapides
            </h2>
        </div>
        <div class="quick-actions">
            <a href="reserver.php" class="action-btn">
                <i class="fas fa-plus-circle"></i>
                <span>Nouvelle réservation</span>
            </a>
            <a href="private_reservations.php" class="action-btn">
                <i class="fas fa-list"></i>
                <span>Mes réservations</span>
            </a>
            <a href="private_favorites.php" class="action-btn">
                <i class="fas fa-heart"></i>
                <span>Mes favoris</span>
            </a>
            <a href="index.php" class="action-btn">
                <i class="fas fa-compass"></i>
                <span>Explorer</span>
            </a>
        </div>
    </div>

    <!-- Upcoming Visits -->
    <div class="private-card upcoming-visits">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-calendar-check"></i>
                Prochaines visites (<?= count($upcomingVisits) ?>)
            </h2>
            <a href="private_reservations.php" class="view-all-link">
                Voir tout <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <?php if (empty($upcomingVisits)): ?>
            <p style="color: var(--gray-600); text-align: center; padding: 2rem;">
                <i class="fas fa-calendar-times" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.3;"></i>
                Aucune visite planifiée. Explorez nos musées et réservez votre prochaine visite !
            </p>
        <?php else: ?>
            <?php foreach ($upcomingVisits as $visit): ?>
                <div class="visit-item">
                    <div class="visit-info">
                        <div class="visit-museum"><?= htmlspecialchars($visit['museum']) ?></div>
                        <div class="visit-details">
                            <span>
                                <i class="fas fa-calendar"></i>
                                <?= date('d/m/Y', strtotime($visit['date'])) ?>
                            </span>
                            <span>
                                <i class="fas fa-clock"></i>
                                <?= $visit['time'] ?>
                            </span>
                            <span>
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($visit['location']) ?>
                            </span>
                            <span>
                                <i class="fas fa-ticket-alt"></i>
                                <?= $visit['tickets'] ?> billet<?= $visit['tickets'] > 1 ? 's' : '' ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="visit-badge badge-<?= $visit['status'] ?>" style="margin-bottom: 0.5rem;">
                            <?= $visit['status'] === 'confirmed' ? 'Confirmée' : 'En attente' ?>
                        </div>
                        <div style="color: var(--secondary-color); font-weight: 700; font-size: 1.125rem;">
                            <?= number_format($visit['price'], 2) ?>€
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <!-- Recent Activity -->
        <div class="col-lg-12">
            <div class="private-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-history"></i>
                        Activité récente
                    </h2>
                </div>
                <div class="activity-timeline">
                    <?php foreach ($recentActivity as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon <?= $activity['color'] ?>">
                                <i class="fas fa-<?= $activity['icon'] ?>"></i>
                            </div>
                            <div class="activity-content">
                                <h4><?= htmlspecialchars($activity['action']) ?></h4>
                                <p><?= htmlspecialchars($activity['museum']) ?></p>
                                <div class="activity-date">
                                    <i class="fas fa-clock"></i> <?= date('d/m/Y', strtotime($activity['date'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Données du calendrier
const calendarEvents = <?= json_encode($calendarEvents) ?>;

// Générer le calendrier
function generateCalendar() {
    const calendar = document.getElementById('calendar');
    const now = new Date();
    const currentMonth = now.getMonth();
    const currentYear = now.getFullYear();
    
    const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                       'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    
    const weekdays = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    
    // En-tête
    let html = `
        <div class="calendar-header">
            <h3>${monthNames[currentMonth]} ${currentYear}</h3>
            <div class="calendar-nav">
                <button onclick="alert('Navigation précédent')"><i class="fas fa-chevron-left"></i></button>
                <button onclick="alert('Navigation suivant')"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    `;
    
    // Jours de la semaine
    html += '<div class="calendar-weekdays">';
    weekdays.forEach(day => {
        html += `<div class="calendar-weekday">${day}</div>`;
    });
    html += '</div>';
    
    // Jours du mois
    const firstDay = new Date(currentYear, currentMonth, 1);
    const lastDay = new Date(currentYear, currentMonth + 1, 0);
    const startingDayOfWeek = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1;
    const monthLength = lastDay.getDate();
    
    html += '<div class="calendar-days">';
    
    // Jours du mois précédent
    const prevMonthLastDay = new Date(currentYear, currentMonth, 0).getDate();
    for (let i = startingDayOfWeek - 1; i >= 0; i--) {
        html += `<div class="calendar-day other-month">${prevMonthLastDay - i}</div>`;
    }
    
    // Jours du mois actuel
    for (let day = 1; day <= monthLength; day++) {
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const event = calendarEvents.find(e => e.date === dateStr);
        
        let classes = 'calendar-day';
        if (day === now.getDate() && currentMonth === now.getMonth() && currentYear === now.getFullYear()) {
            classes += ' today';
        }
        if (event) {
            classes += ` has-event ${event.type}`;
        }
        
        const title = event ? `${event.museum} - ${event.time}` : '';
        html += `<div class="${classes}" title="${title}">${day}</div>`;
    }
    
    // Jours du mois suivant
    const remainingDays = 42 - (startingDayOfWeek + monthLength);
    for (let day = 1; day <= remainingDays; day++) {
        html += `<div class="calendar-day other-month">${day}</div>`;
    }
    
    html += '</div>';
    calendar.innerHTML = html;
}

// Animation au scroll
document.addEventListener('DOMContentLoaded', function() {
    // Générer le calendrier
    generateCalendar();
    
    // Animation des compteurs
    const stats = document.querySelectorAll('.stat-value');
    
    stats.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
        let currentValue = 0;
        const increment = finalValue / 50;
        
        const updateCounter = () => {
            if (currentValue < finalValue) {
                currentValue += increment;
                stat.textContent = Math.ceil(currentValue);
                requestAnimationFrame(updateCounter);
            } else {
                stat.textContent = finalValue;
            }
        };
        
        // Observer pour déclencher l'animation
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(stat);
    });
});
</script>
</body>
</html>
