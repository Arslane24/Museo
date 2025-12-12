<?php
// Configuration de session avant de la démarrer
require_once __DIR__ . '/include/session_config.php';

// Démarrer la session AVANT toute sortie HTML
session_start();

// -------------------------
// TIMEOUT DE SESSION - 5 minutes d'inactivité
// -------------------------
$timeout_duration = 300; // 5 minutes en secondes

if (isset($_SESSION['user_id']) && isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    if ($elapsed_time > $timeout_duration) {
        // Session expirée - Déconnexion complète
        
        // Charger la DB pour supprimer le remember_token
        $data = require __DIR__ . '/secret/database.php';
        $pdo = $data['pdo'];
        
        // Supprimer le remember_token de la base de données
        if (isset($_COOKIE['remember_token'])) {
            $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE remember_token = ?");
            $stmt->execute([$_COOKIE['remember_token']]);
            
            // Supprimer le cookie remember_token
            setcookie('remember_token', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
        
        // Détruire la session
        session_unset();
        session_destroy();
        
        // Supprimer le cookie PHPSESSID
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Rediriger vers login avec message
        header("Location: login.php?timeout=1");
        exit;
    }
}

// Mettre à jour le timestamp de dernière activité
$_SESSION['last_activity'] = time();

// Sécuriser la page : si pas connecté → retour version publique
if (!isset($_SESSION['user_id'])) {
    header("Location: Explorer.php");
    exit;
}

// Charger la configuration
require_once __DIR__ . '/secret/api_keys.php';
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

require_once __DIR__ . '/src/models/MuseumManager.php';

$museumManager = new MuseumManager($pdo);

// SEO & page info
$page_title = 'MuseoLink - Explorer (Connecté)';
$page_description = 'Explorez les musées du monde et ajoutez-les à vos favoris.';
$page_keywords = 'museolink, favoris, explorer, musée, connecté';

// Récupérer les pays disponibles
$countries = $museumManager->getCountries();

// Charger header (session active)
require_once 'include/header.php';
?>

<!-- CSS personnalisé -->
<link href="css/explorer.css?v=20251207" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<!-- Hero Section -->
<section class="explorer-hero">
    <div class="hero-overlay"></div>
    <div class="container position-relative">
        <div class="row align-items-center" style="min-height: 400px;">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content" data-aos="fade-up">
                    <span class="hero-badge">
                        <i class="fas fa-globe-americas"></i> Découvrez le monde
                    </span>
                    <h1 class="display-3 fw-bold mb-4">
                        Explorez les <span class="text-gradient">musées du monde</span>
                    </h1>
                    <p class="lead mb-0">
                        Ajoutez vos musées préférés en favoris 🌟
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="hero-pattern"></div>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <div class="search-card shadow-lg" data-aos="fade-up">

                <div class="row g-3 align-items-center">
                    <!-- Recherche -->
                    <div class="col-lg-5">
                        <div class="search-box position-relative">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="searchInput" class="form-control"
                                placeholder="Rechercher un musée, une ville, un pays..." autocomplete="off">
                            <div id="searchSuggestions" class="search-suggestions"></div>
                        </div>
                    </div>

                    <!-- Country Dropdown -->
                    <div class="col-lg-3">
                        <div class="custom-country-filter">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="country-select-wrapper">
                                <div class="country-select-trigger" id="countryTrigger">Tous les pays</div>
                                <div class="country-options-dropdown" id="countryDropdown">
                                    <div class="country-option" data-value="">Tous les pays</div>
                                    <?php foreach ($countries as $country): ?>
                                        <div class="country-option" data-value="<?= htmlspecialchars($country) ?>">
                                            <?= htmlspecialchars($country) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <select id="countryFilter" class="form-select" style="display:none;">
                                <option value="">Tous les pays</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?= htmlspecialchars($country) ?>">
                                        <?= htmlspecialchars($country) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Bouton Rechercher -->
                    <div class="col-lg-2">
                        <button id="searchBtn" class="btn btn-gradient w-100">
                            <i class="fas fa-search me-2"></i>Rechercher
                        </button>
                    </div>

                    <!-- Reset -->
                    <div class="col-lg-2">
                        <button id="resetBtn" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo me-2"></i>Réinitialiser
                        </button>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="filters-container mt-4" data-aos="fade-up" data-aos-delay="100">
                    <button class="filter-btn active" data-category="all">
                        <i class="fas fa-globe"></i> <span>Tous</span>
                    </button>
                    <button class="filter-btn" data-category="art">
                        <i class="fas fa-palette"></i> <span>Art Classique</span>
                    </button>
                    <button class="filter-btn" data-category="modern">
                        <i class="fas fa-paint-brush"></i> <span>Art Moderne</span>
                    </button>
                    <button class="filter-btn" data-category="contemporary">
                        <i class="fas fa-images"></i> <span>Contemporain</span>
                    </button>
                    <button class="filter-btn" data-category="history">
                        <i class="fas fa-landmark"></i> <span>Histoire</span>
                    </button>
                    <button class="filter-btn" data-category="science">
                        <i class="fas fa-flask"></i> <span>Science</span>
                    </button>
                </div>

            </div>
        </div>
    </section>
</section>

<!-- MUSÉES -->
<section class="museums-section py-5">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-0">
                    <i class="fas fa-th-large me-2"></i>
                    <span id="resultsTitle">Tous les musées</span>
                </h2>
                <p class="text-muted mb-0" id="resultsCount">Chargement...</p>
            </div>
        </div>

        <div class="museum-grid" id="museumGrid">
            <div class="skeleton-loader">
                <?php for ($i=0; $i<6; $i++): ?>
                    <div class="skeleton-card">
                        <div class="skeleton-image"></div>
                        <div class="skeleton-content">
                            <div class="skeleton-line w-75"></div>
                            <div class="skeleton-line w-50"></div>
                            <div class="skeleton-line w-100"></div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="pagination-container mt-5" id="paginationContainer"></div>
    </div>
</section>

<!-- API JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="js/external-apis.js"></script>
<script src="js/explorer.js"></script>

<?php require_once 'include/footer.php'; ?>
