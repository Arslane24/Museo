<?php
/**
 * PAGE DÉTAIL MUSÉE - Affichage d'un musée avec ses œuvres
 */
session_start();

// Configuration
require_once __DIR__ . '/secret/api_keys.php';
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

require_once __DIR__ . '/src/models/MuseumManager.php';

$museumManager = new MuseumManager($pdo);

// Récupérer le slug du musée
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    $_SESSION['error'] = 'Aucun musée spécifié';
    header('Location: Explorer.php');
    exit;
}

// Charger les données du musée depuis la BDD
$museum = $museumManager->getMuseumBySlug($slug);

if (!$museum) {
    $_SESSION['error'] = 'Musée introuvable : ' . htmlspecialchars($slug);
    header('Location: Explorer.php');
    exit;
}

// Page title
$page_title = $museum['name'];
$body_class = 'museum-detail-page';

// Préparer le label de localisation pour la météo
$weatherLocationParts = array_filter([
    $museum['city'] ?? null,
    $museum['country'] ?? null
]);
$weatherLocationLabel = implode(', ', $weatherLocationParts);

// Préparer l'image de fond du hero
$heroBackgroundStyle = '';
if (!empty($museum['image_url'])) {
    $imageUrl = htmlspecialchars($museum['image_url'], ENT_QUOTES);
    $heroBackgroundStyle = ' style="--museum-hero-image: url(\'' . $imageUrl . '\')"';
}

// Include header
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/private_nav.php';  // NAV PRIVÉE
} else {
    require_once __DIR__ . '/include/header.php'; // NAV PUBLIQUE
}

?>

<!-- CSS personnalisé pour cette page -->
<link href="/css/musee-detail.css?v=<?php echo time(); ?>" rel="stylesheet">

<!-- Leaflet CSS pour la carte -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>

<main id="main-content" role="main">
<!-- Hero Section avec image de fond du musée -->
<section class="museum-detail-hero"<?= $heroBackgroundStyle ?> aria-label="Détails du musée">
    <div class="container">
        <div class="hero-content">
            <h1 data-aos="fade-up"><?= htmlspecialchars($museum['name']) ?></h1>
            <?php if (!empty($weatherLocationLabel)): ?>
                <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="100">
                    <i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($weatherLocationLabel) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Barre d'informations du musée -->
<section class="museum-info-bar">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex gap-3 flex-wrap justify-content-center justify-content-md-start">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($museum['city'] . ', ' . $museum['country']) ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-tag"></i>
                        <span>
                            <?php 
                                $price = floatval($museum['price_adult'] ?? 0);
                                echo $price === 0.0 ? 'Gratuit' : number_format($price, 2) . '€';
                            ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-palette"></i>
                        <span><?= htmlspecialchars($museum['category'] ?? 'Art') ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
                <a href="#booking" class="btn-reserve">
                    <i class="fas fa-ticket-alt me-2"></i>Réserver
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Données du musée en JSON pour JavaScript (sécurisé) -->
<?php
    $museumJson = json_encode($museum, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    // Sécuriser contre la séquence de fin </script> dans les données
    $museumJsonSafe = str_replace('</', '<\/', $museumJson);
?>
<?php
    // Encodage Base64 pour neutraliser totalement les caractères problématiques
    $museumJson = json_encode($museum, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $museumB64 = base64_encode($museumJson);
?>
<script>
    // Décodage Base64 côté client pour éviter tout token invalide
    (function(){
        const b64 = '<?= $museumB64 ?>';
        try {
            const json = atob(b64);
            window.MUSEUM_DATA = JSON.parse(json);
        } catch (e) {
            console.error('Erreur parsing MUSEUM_DATA:', e);
            window.MUSEUM_DATA = {};
        }
    })();
</script>

<!-- Contenu Principal -->
<section class="detail-content">
    <div class="container">
        <div class="row">
            <!-- Colonne Principale -->
            <div class="col-lg-8">
                
                <!-- Description -->
                <div class="info-card" data-aos="fade-up">
                    <h3><i class="fas fa-info-circle"></i> À propos</h3>
                    <p class="lead"><?= nl2br(htmlspecialchars($museum['description'] ?? 'Aucune description disponible.')) ?></p>
                    <?php if (!empty($museum['address'])): ?>
                    <p class="mt-3">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <strong>Adresse :</strong> <?= htmlspecialchars($museum['address']) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- Widget Météo -->
                <div class="weather-widget" id="weatherWidget" data-aos="fade-up">
                    <div class="weather-header">
                        <div class="weather-header-left">
                            <h4 class="weather-title">
                                <i class="fas fa-cloud-sun"></i> Météo actuelle
                            </h4>
                            <span class="weather-status-badge">
                                <i class="fas fa-bolt"></i> En direct
                            </span>
                            <?php if (!empty($weatherLocationLabel)): ?>
                                <span class="weather-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($weatherLocationLabel) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="weather-date">
                            Mis à jour le <?= date('d/m/Y'); ?>
                        </div>
                    </div>
                    <div class="weather-content">
                        <div class="weather-main">
                            <div class="weather-temp-block">
                                <div class="weather-temp-value">
                                    <span id="weatherTemp">--</span><sup>°C</sup>
                                </div>
                                <p class="weather-description" id="weatherDesc">Chargement...</p>
                            </div>
                            <div class="weather-icon-wrapper">
                                <div class="weather-icon" id="weatherIcon">🌤️</div>
                            </div>
                        </div>
                        <div class="weather-details">
                            <div class="weather-detail-item">
                                <i class="fas fa-tint"></i>
                                <div>
                                    <span class="detail-label">Humidité</span>
                                    <span class="detail-value" id="weatherHumidity">--%</span>
                                </div>
                            </div>
                            <div class="weather-detail-item">
                                <i class="fas fa-wind"></i>
                                <div>
                                    <span class="detail-label">Vent</span>
                                    <span class="detail-value" id="weatherWind">-- km/h</span>
                                </div>
                            </div>
                            <div class="weather-detail-item">
                                <i class="fas fa-temperature-high"></i>
                                <div>
                                    <span class="detail-label">Ressenti</span>
                                    <span class="detail-value" id="weatherFeels">--°C</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Œuvres d'art -->
                <div class="artworks-section" data-aos="fade-up">
                    <h3 class="section-title">
                        <i class="fas fa-palette"></i> Collections d'œuvres
                    </h3>
                    <div class="row g-4" id="artworksContainer">
                        <!-- Chargé par JavaScript -->
                        <div class="col-12 text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte Interactive -->
                <div class="map-section" data-aos="fade-up">
                    <h3 class="section-title">
                        <i class="fas fa-map-marked-alt"></i> Localisation
                    </h3>
                    <div id="museumMap" class="map-container"></div>
                </div>

            </div>

            <!-- Sidebar Réservation -->
            <div class="col-lg-4">
                <div class="booking-card" data-aos="fade-up" id="booking">
                    <h4><i class="fas fa-ticket-alt"></i> Réserver votre visite</h4>
                    
                    <div class="price-display">
                        <span class="price-label">À partir de</span>
                        <span class="price-amount">
                            <?php 
                                $price = floatval($museum['price_adult'] ?? 0);
                                echo $price === 0.0 ? 'Gratuit' : number_format($price, 2) . '€';
                            ?>
                        </span>
                    </div>

                    <form action="reserver.php" method="GET" class="booking-form">
                        <input type="hidden" name="museum_id" value="<?= $museum['id'] ?>">
                        <input type="hidden" name="museum_name" value="<?= htmlspecialchars($museum['name']) ?>">
                        
                        <div class="form-group">
                            <label for="visit_date">
                                <i class="fas fa-calendar"></i> Date de visite
                            </label>
                            <input type="date" 
                                   class="form-control" 
                                   id="visit_date" 
                                   name="visit_date" 
                                   required
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="tickets">
                                <i class="fas fa-users"></i> Nombre de billets
                            </label>
                            <select class="form-control" id="tickets" name="tickets" required>
                                <option value="1">1 billet</option>
                                <option value="2" selected>2 billets</option>
                                <option value="3">3 billets</option>
                                <option value="4">4 billets</option>
                                <option value="5">5 billets</option>
                                <option value="6">6 billets</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-book">
                            <i class="fas fa-ticket-alt"></i> Réserver maintenant
                        </button>
                    </form>

                    <div class="booking-info">
                        <p>
                            <i class="fas fa-info-circle"></i> Annulation gratuite jusqu'à 24h avant
                        </p>
                        <p>
                            <i class="fas fa-clock"></i> Confirmation immédiate
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Leaflet JS pour la carte -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<!-- External APIs module -->
<script src="/js/external-apis.js?v=<?php echo time(); ?>"></script>

<!-- JavaScript personnalisé pour cette page -->
<script src="/js/musee-detail.js?v=<?php echo time(); ?>"></script>
</main>

<?php
// Include footer
require_once 'include/footer.php';
?>
