<?php

require_once __DIR__ . '/secret/api_keys.php';
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

require_once __DIR__ . '/src/models/MuseumManager.php';

$museumManager = new MuseumManager($pdo);

// SEO & Page info
$page_title = 'MuseoLink - Explorer et Réserver les Musées du Monde';
$page_description = 'Explorez avec MuseoLink les plus grands musées du monde : Louvre Paris, MoMA New York, British Museum Londres. Réservez vos billets de musée en ligne par pays, ville ou catégorie. Visites culturelles simplifiées.';
$page_keywords = 'museolink, explorer musées, musées monde, réservation musée par pays, musées Paris, musées Londres, musées New York, billets musée en ligne, visites guidées musées';

// Récupérer les pays disponibles
$countries = $museumManager->getCountries();

// Include header

if (isset($_SESSION['user_id'])) {
    // connecté → header privé
    require_once 'private_nav.php';
} else {
    // visiteur → header public
    require_once 'include/header.php';
}
?>

<!-- CSS personnalisé pour cette page -->
<link href="css/explorer.css?v=20251207" rel="stylesheet">

<!-- Leaflet CSS pour la carte interactive -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>

<main id="main-content">
<!-- Hero Section avec image de fond + search card intégrée -->
<section class="explorer-hero" aria-label="Section d'exploration des musées">
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
                        Des collections exceptionnelles, des chefs-d'œuvre inestimables.<br>
                        Réservez votre visite dès maintenant.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-pattern"></div>
    <!-- Search and Filters Card intégrée dans le hero pour éviter bande sombre -->
    <div class="search-section">
    <div class="container">
        <div class="search-card shadow-lg" data-aos="fade-up">
            <div class="row g-3 align-items-center">
                <!-- Recherche textuelle -->
                <div class="col-lg-5">
                    <div class="search-box position-relative">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" 
                               id="searchInput" 
                               class="form-control" 
                               placeholder="Rechercher un musée, une ville, un pays..."
                               aria-label="Rechercher un musée, une ville ou un pays"
                               autocomplete="off">
                        <!-- Dropdown des suggestions -->
                        <div id="searchSuggestions" class="search-suggestions"></div>
                    </div>
                </div>
                
                <!-- Custom Country Dropdown -->
                <div class="col-lg-3">
                    <div class="custom-country-filter">
                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                        <div class="country-select-wrapper">
                            <div class="country-select-trigger" id="countryTrigger" role="button" aria-label="Sélectionner un pays" aria-haspopup="listbox" aria-expanded="false" tabindex="0">Tous les pays</div>
                            <div class="country-options-dropdown" id="countryDropdown" role="listbox" aria-label="Liste des pays">
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
                
                <!-- Bouton de recherche -->
                <div class="col-lg-2">
                    <button id="searchBtn" class="btn btn-gradient w-100" aria-label="Rechercher un musée">
                        <i class="fas fa-search" aria-hidden="true"></i> Rechercher
                    </button>
                </div>
                
                <!-- Bouton reset -->
                <div class="col-lg-2">
                    <button id="resetBtn" class="btn btn-outline-secondary w-100" aria-label="Réinitialiser les filtres">
                        <i class="fas fa-redo" aria-hidden="true"></i> Réinitialiser
                    </button>
                </div>
            </div>
            
            <!-- Category Filters -->
            <div class="filters-container mt-4" data-aos="fade-up" data-aos-delay="100" role="group" aria-label="Filtres de catégorie">
                <button class="filter-btn active" data-category="all" aria-pressed="true" aria-label="Tous les musées">
                    <i class="fas fa-globe" aria-hidden="true"></i>
                    <span>Tous</span>
                </button>
                <button class="filter-btn" data-category="art">
                    <i class="fas fa-palette"></i>
                    <span>Art Classique</span>
                </button>
                <button class="filter-btn" data-category="modern">
                    <i class="fas fa-paint-brush"></i>
                    <span>Art Moderne</span>
                </button>
                <button class="filter-btn" data-category="contemporary">
                    <i class="fas fa-images"></i>
                    <span>Contemporain</span>
                </button>
                <button class="filter-btn" data-category="history">
                    <i class="fas fa-landmark"></i>
                    <span>Histoire</span>
                </button>
                <button class="filter-btn" data-category="science">
                    <i class="fas fa-flask"></i>
                    <span>Science</span>
                </button>
            </div>
            </div>
        </div>
    </div>
</section>

<!-- Museums Grid Section -->
<section class="museums-section py-5" id="resultsSection">
    <div class="container">
        <!-- Results Info -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-0">
                    <i class="fas fa-th-large me-2"></i>
                    <span id="resultsTitle">Tous les musées</span>
                </h2>
                <p class="text-muted mb-0" id="resultsCount">Chargement...(cela peut prendre quelques secondes)</p>
            </div>
        </div>
        
        <!-- Museum Grid -->
        <div class="museum-grid" id="museumGrid">
            <!-- Skeleton loading -->
            <div class="skeleton-loader">
                <?php for($i = 0; $i < 6; $i++): ?>
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
        
        <!-- Pagination -->
        <div class="pagination-container mt-5" id="paginationContainer"></div>
    </div>
</section>

<!-- Section Œuvres Met Museum -->
<section class="artworks-section py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge">
                <i class="fas fa-palette"></i> MET MUSEUM API
            </span>
            <h2 class="section-title text-white mt-3">Œuvres d'Art Exceptionnelles</h2>
            <p class="section-subtitle">Collection du Metropolitan Museum of Art de New York</p>
        </div>
        
        <div class="row g-4" id="metArtworksGrid">
            <div class="col-12 text-center">
                <div class="loading-spinner">
                    <i class="fas fa-circle-notch fa-spin fa-3x" style="color: var(--secondary-color);"></i>
                    <p class="mt-3 text-white">Chargement des chefs-d'œuvre...</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <button class="btn btn-lg px-5" id="loadMoreMet" style="background: var(--gradient-accent); color: var(--dark-color); border: none; border-radius: 50px; font-weight: 700;">
                <i class="fas fa-sync-alt me-2"></i> Découvrir plus d'œuvres
            </button>
        </div>
    </div>
</section>

<!-- Section Harvard Art Museums -->
<section class="harvard-section py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge">
                <i class="fas fa-university"></i> HARVARD ART MUSEUMS API
            </span>
            <h2 class="section-title text-white mt-3">Collections Harvard</h2>
            <p class="mt-3 text-white">Découvrez les trésors des musées de Harvard University</p>
        </div>
        
        <div class="row g-4" id="harvardGrid">
            <div class="col-12 text-center">
                <div class="loading-spinner">
                    <i class="fas fa-circle-notch fa-spin fa-3x"></i>
                    <p class="mt-3">Chargement des collections...</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Météo des Grandes Villes -->
<section class="weather-section py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge">
                <i class="fas fa-cloud-sun"></i> MÉTÉO API
            </span>
            <h2 class="section-title text-white mt-3">Météo des Capitales Culturelles</h2>
            <p class="section-subtitle">Conditions météorologiques en temps réel dans le monde</p>
        </div>
        
        <div class="row g-4" id="weatherGrid">
            <div class="col-12 text-center">
                <div class="loading-spinner">
                    <i class="fas fa-circle-notch fa-spin fa-3x" style="color: var(--secondary-color);"></i>
                    <p class="mt-3 text-white">Chargement de la météo...</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Leaflet JS pour la carte interactive -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<!-- External APIs module -->
<script src="js/external-apis.js?v=20251207"></script>

<!-- JavaScript personnalisé pour cette page -->
<script src="js/explorer.js?v=20251207"></script>

</main>

<?php
// Include footer
require_once 'include/footer.php';
?>
