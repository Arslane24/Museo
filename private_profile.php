<?php
$pageTitle = 'Mon Profil - MUSEO';
require_once __DIR__ . '/include/auth.php';  // protège la page
require_once __DIR__ . '/private_nav.php';

// Connexion à la base de données
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

// Charger les managers
require_once __DIR__ . '/src/models/StatsManager.php';

$statsManager = new StatsManager($pdo);
$userId = $_SESSION['user_id'];

// Récupérer l'avatar de l'utilisateur depuis la session
$userAvatar = $_SESSION['user_avatar'] ?? null;

// Message de succès si avatar mis à jour
$avatarUpdated = isset($_GET['avatar_updated']) && $_GET['avatar_updated'] == 1;

// Récupérer les informations utilisateur depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Récupérer la date de création du compte (utiliser une colonne existante ou date par défaut)
$memberSince = date('Y-m-d', strtotime('-6 months')); // Par défaut: il y a 6 mois

// Récupérer les statistiques
$stats = $statsManager->getUserStats($userId);

// Données utilisateur avec informations supplémentaires
$userInfo = [
    'name' => $_SESSION['user_name'],
    'email' => $_SESSION['user_email'],
    'login' => $_SESSION['user_login'],
    'avatar' => $userAvatar,
    'member_since' => $memberSince,
    'last_login' => date('Y-m-d'),
    'total_visits' => $stats['completed'],
    'favorite_museum' => 'Musée du Louvre',
    'preferred_language' => 'Français',
    'notifications_enabled' => true,
    'phone' => '+33 6 12 34 56 78',
    'country' => 'France',
    'city' => 'Paris',
    'bio' => 'Passionné d\'art et de culture, j\'aime découvrir de nouveaux musées à travers le monde.'
];

$badges = [
    ['name' => 'Explorateur', 'icon' => 'compass', 'color' => '#3b82f6', 'description' => '5+ visites', 'unlocked' => $stats['completed'] >= 5],
    ['name' => 'Passionné d\'Art', 'icon' => 'palette', 'color' => '#d4af37', 'description' => 'Visite de 3 musées d\'art', 'unlocked' => $stats['completed'] >= 3],
    ['name' => 'Membre Premium', 'icon' => 'crown', 'color' => '#f59e0b', 'description' => 'Compte actif depuis 1 an', 'unlocked' => strtotime($memberSince) < strtotime('-1 year')],
    ['name' => 'Collectionneur', 'icon' => 'gem', 'color' => '#8b5cf6', 'description' => '10+ favoris', 'unlocked' => $stats['favorite_museums'] >= 10],
    ['name' => 'Globe-trotter', 'icon' => 'globe', 'color' => '#06b6d4', 'description' => 'Visité 5 pays', 'unlocked' => $stats['cities_visited'] >= 5],
    ['name' => 'Critique', 'icon' => 'comment', 'color' => '#f43f5e', 'description' => '10+ avis publiés', 'unlocked' => false]
];

$visitHistory = $statsManager->getVisitHistory($userId, 4);
?>

<style>
.profile-header {
    background: var(--gradient-primary);
    border-radius: var(--border-radius-xl);
    padding: 2rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.2) 0%, transparent 70%);
    border-radius: 50%;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--gradient-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: white;
    border: 4px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 1;
    overflow: hidden;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-edit-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 40px;
    height: 40px;
    background: var(--gradient-accent);
    border-radius: 50%;
    border: 3px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    z-index: 2;
    text-decoration: none;
    color: white;
}

.avatar-edit-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
    color: white;
}

.success-message {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: var(--border-radius-lg);
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    color: #10b981;
    display: flex;
    align-items: center;
    gap: 1rem;
    animation: slideInDown 0.5s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.profile-info {
    flex: 1;
    position: relative;
    z-index: 1;
}

.profile-name {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.5rem;
}

.profile-meta {
    color: rgba(255, 255, 255, 0.9);
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.profile-meta span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.info-card {
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    transition: var(--transition);
}

.info-card:hover {
    border-color: var(--secondary-color);
    transform: translateY(-3px);
}

.info-label {
    color: var(--gray-600);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-value {
    color: var(--gray-700);
    font-size: 1.125rem;
    font-weight: 600;
}

.badges-section {
    margin-bottom: 2rem;
}

.badges-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.badge-card {
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.badge-card.locked {
    opacity: 0.5;
    filter: grayscale(1);
}

.badge-locked-text {
    display: block;
    color: var(--gray-500);
    font-size: 0.75rem;
    margin-top: 0.5rem;
}

.badge-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: var(--gradient-accent);
    opacity: 0.05;
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.badge-card:hover {
    transform: scale(1.05);
    border-color: var(--secondary-color);
}

.badge-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: white;
    flex-shrink: 0;
}

.badge-info h3 {
    color: var(--gray-700);
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.badge-info p {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin: 0;
}

.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.mini-stat {
    text-align: center;
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    transition: var(--transition);
}

.mini-stat:hover {
    border-color: var(--secondary-color);
    transform: translateY(-5px);
}

.mini-stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--secondary-color);
    margin-bottom: 0.5rem;
}

.mini-stat-label {
    color: var(--gray-600);
    font-size: 0.875rem;
}

.mini-stat i {
    font-size: 2rem;
    color: var(--secondary-color);
    opacity: 0.5;
    margin-bottom: 1rem;
}

.edit-profile-btn {
    background: var(--gradient-accent);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: var(--border-radius-lg);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.edit-profile-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
    color: white;
}

.preferences-section {
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
}

.preference-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.preference-item:last-child {
    border-bottom: none;
}

.preference-label {
    color: var(--gray-700);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.preference-value {
    color: var(--gray-600);
    font-size: 0.95rem;
}

.toggle-switch {
    width: 50px;
    height: 26px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 13px;
    position: relative;
    cursor: pointer;
    transition: var(--transition);
}

.toggle-switch.active {
    background: var(--secondary-color);
}

.toggle-switch::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    transition: var(--transition);
}

.toggle-switch.active::after {
    left: 27px;
}

.history-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.history-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.history-item:hover {
    border-color: rgba(212, 175, 55, 0.3);
    background: rgba(255, 255, 255, 0.05);
}

.history-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: var(--gradient-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.history-content {
    flex: 1;
}

.history-content h4 {
    color: var(--gray-700);
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.history-content p {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin: 0;
}

.history-rating {
    display: flex;
    gap: 0.25rem;
}

.view-all-link {
    color: var(--gray-600);
    text-decoration: none;
    font-size: 0.875rem;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.view-all-link:hover {
    color: var(--secondary-color);
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-meta {
        justify-content: center;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}

/* Ensure readable labels/inputs in avatar modal */
.modal-content .form-label {
    color: #111 !important;
}
.modal-content .form-control {
    background-color: #fff !important;
    color: #111 !important;
    border-color: rgba(0,0,0,0.2);
}
.modal-content .btn.btn-gradient {
    background: var(--gradient-accent);
    color: #fff;
}
</style>

<div class="container py-4">
    <!-- Success Message -->
    <?php if ($avatarUpdated): ?>
        <div class="success-message">
            <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
            <div>
                <strong>Avatar mis à jour avec succès !</strong>
                <p style="margin: 0; font-size: 0.875rem; opacity: 0.9;">Votre nouveau profil est maintenant visible.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            <?php if ($userAvatar): ?>
                <img src="<?= htmlspecialchars($userAvatar) ?>" alt="Avatar de <?= htmlspecialchars($_SESSION['user_name']) ?>">
            <?php else: ?>
                <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
            <?php endif; ?>
                        <a href="#" class="avatar-edit-btn" title="Changer l'avatar" data-bs-toggle="modal" data-bs-target="#avatarModal">
                                <i class="fas fa-camera"></i>
                        </a>
        </div>
        
                <!-- Modal Upload Avatar -->
                <div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-camera me-2"></i>Changer ma photo de profil</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <?php $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>
                                <form id="avatarForm" action="<?= htmlspecialchars($basePath) ?>/profile/upload-avatar.php" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label">Choisir une image (JPEG/PNG/WebP, max 2MB)</label>
                                        <input type="file" name="avatar" id="avatarInput" class="form-control" accept="image/jpeg,image/png,image/webp" required>
                                    </div>
                                    
                                    <div class="mt-3 d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-gradient">Enregistrer</button>
                                    </div>
                                </form>
                                <div id="avatarMsg" class="mt-3" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
        <div class="profile-info">
            <h1 class="profile-name"><?= htmlspecialchars($_SESSION['user_name']) ?></h1>
            <div class="profile-meta">
                <span>
                    <i class="fas fa-user-circle"></i>
                    @<?= htmlspecialchars($_SESSION['user_login']) ?>
                </span>
                <span>
                    <i class="fas fa-calendar"></i>
                    Membre depuis <?= date('F Y', strtotime($userInfo['member_since'])) ?>
                </span>
                <span>
                    <i class="fas fa-clock"></i>
                    Dernière connexion: <?= date('d/m/Y', strtotime($userInfo['last_login'])) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-overview">
        <div class="mini-stat">
            <i class="fas fa-ticket-alt"></i>
            <div class="mini-stat-value"><?= $userInfo['total_visits'] ?></div>
            <div class="mini-stat-label">Visites effectuées</div>
        </div>
        <div class="mini-stat">
            <i class="fas fa-heart"></i>
            <div class="mini-stat-value">8</div>
            <div class="mini-stat-label">Musées favoris</div>
        </div>
        <div class="mini-stat">
            <i class="fas fa-star"></i>
            <div class="mini-stat-value">3</div>
            <div class="mini-stat-label">Badges gagnés</div>
        </div>
        <div class="mini-stat">
            <i class="fas fa-trophy"></i>
            <div class="mini-stat-value">450</div>
            <div class="mini-stat-label">Points culture</div>
        </div>
    </div>

    <!-- Badges Section -->
    <div class="private-card badges-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-award"></i>
                Mes badges (<?= count(array_filter($badges, fn($b) => $b['unlocked'])) ?>/<?= count($badges) ?>)
            </h2>
        </div>
        <div class="badges-grid">
            <?php foreach ($badges as $badge): ?>
                <div class="badge-card <?= $badge['unlocked'] ? '' : 'locked' ?>">
                    <div class="badge-icon" style="background: <?= $badge['color'] ?>;">
                        <i class="fas fa-<?= $badge['icon'] ?>"></i>
                    </div>
                    <div class="badge-info">
                        <h3><?= htmlspecialchars($badge['name']) ?></h3>
                        <p><?= htmlspecialchars($badge['description']) ?></p>
                        <?php if (!$badge['unlocked']): ?>
                            <span class="badge-locked-text">
                                <i class="fas fa-lock"></i> Verrouillé
                            </span>
                        <?php else: ?>
                            <span class="badge-locked-text" style="color: #10b981;">
                                <i class="fas fa-check-circle"></i> Débloqué
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Historique des visites -->
        <div class="col-lg-6">
            <div class="private-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-history"></i>
                        Historique récent
                    </h2>
                    <a href="private_reservations.php?filter=completed" class="view-all-link">
                        Voir tout <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="history-list">
                    <?php foreach ($visitHistory as $visit): ?>
                        <div class="history-item">
                            <div class="history-icon">
                                <i class="fas fa-museum"></i>
                            </div>
                            <div class="history-content">
                                <h4><?= htmlspecialchars($visit['museum']) ?></h4>
                                <p>
                                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($visit['city']) ?>
                                    <span style="margin: 0 0.5rem;">•</span>
                                    <i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($visit['date'])) ?>
                                </p>
                            </div>
                            <div class="history-rating">
                                <?php for($i = 0; $i < $visit['rating']; $i++): ?>
                                    <i class="fas fa-star" style="color: #fbbf24;"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Informations personnelles -->
        <div class="col-lg-6">
            <div class="private-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-user-circle"></i>
                        À propos de moi
                    </h2>
                    <a href="private_settings.php" class="edit-profile-btn">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                
                <div class="bio-section" style="margin-bottom: 1.5rem;">
                    <p style="color: var(--gray-600); line-height: 1.8;">
                        <?= htmlspecialchars($userInfo['bio']) ?>
                    </p>
                </div>

                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-phone"></i>
                            Téléphone
                        </div>
                        <div class="info-value"><?= htmlspecialchars($userInfo['phone']) ?></div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Localisation
                        </div>
                        <div class="info-value"><?= htmlspecialchars($userInfo['city']) ?>, <?= htmlspecialchars($userInfo['country']) ?></div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-museum"></i>
                            Musée préféré
                        </div>
                        <div class="info-value"><?= htmlspecialchars($userInfo['favorite_museum']) ?></div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-language"></i>
                            Langue
                        </div>
                        <div class="info-value"><?= htmlspecialchars($userInfo['preferred_language']) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information -->
    <div class="private-card">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-info-circle"></i>
                Informations du compte
            </h2>
            <a href="private_settings.php" class="edit-profile-btn">
                <i class="fas fa-edit"></i>
                Modifier
            </a>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">
                    <i class="fas fa-user"></i>
                    Nom complet
                </div>
                <div class="info-value"><?= htmlspecialchars($userInfo['name']) ?></div>
            </div>

            <div class="info-card">
                <div class="info-label">
                    <i class="fas fa-at"></i>
                    Nom d'utilisateur
                </div>
                <div class="info-value">@<?= htmlspecialchars($userInfo['login']) ?></div>
            </div>

            <div class="info-card">
                <div class="info-label">
                    <i class="fas fa-envelope"></i>
                    Email
                </div>
                <div class="info-value"><?= htmlspecialchars($userInfo['email']) ?></div>
            </div>

            <div class="info-card">
                <div class="info-label">
                    <i class="fas fa-museum"></i>
                    Musée préféré
                </div>
                <div class="info-value"><?= htmlspecialchars($userInfo['favorite_museum']) ?></div>
            </div>

            <div class="info-card">
                <div class="info-label">
                    <i class="fas fa-language"></i>
                    Langue préférée
                </div>
                <div class="info-value"><?= htmlspecialchars($userInfo['preferred_language']) ?></div>
            </div>

            <div class="info-card">
                <div class="info-label">
                    <i class="fas fa-calendar-check"></i>
                    Date d'inscription
                </div>
                <div class="info-value"><?= date('d/m/Y', strtotime($userInfo['member_since'])) ?></div>
            </div>
        </div>
    </div>

    <!-- Preferences -->
    <div class="private-card">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-sliders-h"></i>
                Préférences
            </h2>
        </div>
        
        <div class="preferences-section">
            <div class="preference-item">
                <div class="preference-label">
                    <i class="fas fa-bell"></i>
                    Notifications par email
                </div>
                <div class="toggle-switch <?= $userInfo['notifications_enabled'] ? 'active' : '' ?>" onclick="togglePreference(this)"></div>
            </div>

            <div class="preference-item">
                <div class="preference-label">
                    <i class="fas fa-envelope"></i>
                    Newsletter mensuelle
                </div>
                <div class="toggle-switch active" onclick="togglePreference(this)"></div>
            </div>

            <div class="preference-item">
                <div class="preference-label">
                    <i class="fas fa-mobile-alt"></i>
                    Notifications push
                </div>
                <div class="toggle-switch" onclick="togglePreference(this)"></div>
            </div>

            <div class="preference-item">
                <div class="preference-label">
                    <i class="fas fa-eye"></i>
                    Profil public
                </div>
                <div class="toggle-switch" onclick="togglePreference(this)"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePreference(element) {
    element.classList.toggle('active');
    // Ici vous pouvez ajouter un appel AJAX pour sauvegarder la préférence
}

// Animation des stats au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Avatar upload handler
    const avatarInput = document.getElementById('avatarInput');
    const avatarForm = document.getElementById('avatarForm');
    const avatarMsg = document.getElementById('avatarMsg');
    
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e){
            const file = e.target.files[0];
            if (!file) return;
            avatarMsg.style.display = 'none';
            if (file.size > 2 * 1024 * 1024) {
                avatarMsg.textContent = 'Fichier trop volumineux (max 2MB)';
                avatarMsg.className = 'alert alert-warning';
                avatarMsg.style.display = 'block';
                e.target.value = '';
                return;
            }
            const allowed = ['image/jpeg','image/png','image/webp'];
            if (!allowed.includes(file.type)) {
                avatarMsg.textContent = 'Type de fichier non autorisé';
                avatarMsg.className = 'alert alert-warning';
                avatarMsg.style.display = 'block';
                e.target.value = '';
                return;
            }
        });
    }
    
    if (avatarForm) {
        avatarForm.addEventListener('submit', async function(e){
            e.preventDefault();
            avatarMsg.style.display = 'none';
            const fd = new FormData(avatarForm);
            try {
                const res = await fetch(avatarForm.action, { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    // Close modal
                    const modalEl = document.getElementById('avatarModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    // Update avatar image
                    const avatarImg = document.querySelector('.profile-avatar img');
                    if (avatarImg) {
                        avatarImg.src = data.url;
                    } else {
                        // No img yet, create one
                        const avatarDiv = document.querySelector('.profile-avatar');
                        if (avatarDiv) {
                            avatarDiv.innerHTML = '<img src="' + data.url + '" alt="Avatar">';
                        }
                    }
                } else {
                    avatarMsg.textContent = data.error || 'Erreur lors de l\'upload';
                    avatarMsg.className = 'alert alert-danger';
                    avatarMsg.style.display = 'block';
                }
            } catch(err) {
                avatarMsg.textContent = 'Erreur réseau';
                avatarMsg.className = 'alert alert-danger';
                avatarMsg.style.display = 'block';
            }
        });
    }
    
    const statValues = document.querySelectorAll('.mini-stat-value');
    
    statValues.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
        let currentValue = 0;
        const increment = finalValue / 40;
        
        const updateCounter = () => {
            if (currentValue < finalValue) {
                currentValue += increment;
                stat.textContent = Math.ceil(currentValue);
                requestAnimationFrame(updateCounter);
            } else {
                stat.textContent = finalValue;
            }
        };
        
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
