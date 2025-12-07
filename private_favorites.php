<?php
$pageTitle = 'Mes Favoris - MUSEO';
require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/private_nav.php';

// Connexion à la base de données
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

// Charger les managers
require_once __DIR__ . '/src/models/MuseumManager.php';

$museumManager = new MuseumManager($pdo);
$userId = $_SESSION['user_id'];

// Récupérer les favoris depuis la base de données
$favoritesData = $museumManager->getUserFavorites($userId);

// Transformer les données pour le format attendu
$favorites = [];
foreach ($favoritesData as $fav) {
    $favorites[] = [
        'id' => $fav['id'],
        'name' => $fav['name'],
        'location' => $fav['city'] . ', ' . $fav['country'],
        'description' => $fav['description'] ?? 'Découvrez ce musée exceptionnel',
        'category' => 'Art', // Valeur par défaut
        'rating' => 4.5,
        'price' => $fav['price'] ?? 0.00,
        'image' => $fav['image_url'] ?? 'https://images.unsplash.com/photo-1566438480900-0609be27a4be?w=500&h=300&fit=crop',
        'added_date' => date('Y-m-d') // Date par défaut
    ];
}

// Filtrer par catégorie
$category = $_GET['category'] ?? 'all';
$filteredFavorites = $favorites;
if ($category !== 'all') {
    $filteredFavorites = array_filter($favorites, function($fav) use ($category) {
        return $fav['category'] === $category;
    });
}

// Liste des catégories
$categories = array_unique(array_map(fn($f) => $f['category'], $favorites));
?>

<style>
.view-toggle {
    display: flex;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.05);
    padding: 0.5rem;
    border-radius: var(--border-radius-lg);
    margin-bottom: 2rem;
}

.view-btn {
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
    background: transparent;
    border: none;
    color: var(--gray-600);
    cursor: pointer;
    transition: var(--transition);
}

.view-btn.active {
    background: var(--gradient-primary);
    color: white;
}

.category-filter {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.category-btn {
    padding: 0.5rem 1.25rem;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: var(--gray-600);
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    font-size: 0.875rem;
}

.category-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--secondary-color);
    color: var(--gray-700);
}

.category-btn.active {
    background: var(--gradient-accent);
    border-color: var(--secondary-color);
    color: white;
}

.favorites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
}

.favorites-grid.list-view {
    grid-template-columns: 1fr;
}

.favorite-card {
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-xl);
    overflow: hidden;
    transition: var(--transition);
    position: relative;
}

.favorite-card:hover {
    border-color: var(--secondary-color);
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(212, 175, 55, 0.25);
}

.favorite-image-wrapper {
    position: relative;
    overflow: hidden;
    height: 220px;
}

.favorite-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.favorite-card:hover .favorite-image {
    transform: scale(1.1);
}

.favorite-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    padding: 1.5rem 1rem 1rem;
}

.favorite-category {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: var(--gradient-accent);
    color: white;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.favorite-rating {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    color: var(--secondary-color);
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.favorite-content {
    padding: 1.5rem;
}

.favorite-name {
    color: var(--secondary-color);
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.favorite-location {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.favorite-description {
    color: var(--gray-600);
    font-size: 0.875rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.favorite-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.favorite-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--secondary-color);
}

.favorite-price.free {
    color: #10b981;
}

.favorite-actions {
    display: flex;
    gap: 0.5rem;
}

.action-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-600);
    cursor: pointer;
    transition: var(--transition);
}

.action-icon:hover {
    background: var(--gradient-primary);
    border-color: var(--secondary-color);
    color: white;
    transform: scale(1.1);
}

.action-icon.remove:hover {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

/* List View Styles */
.favorites-grid.list-view .favorite-card {
    display: grid;
    grid-template-columns: 350px 1fr;
}

.favorites-grid.list-view .favorite-image-wrapper {
    height: 100%;
}

.favorites-grid.list-view .favorite-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.stats-banner {
    background: var(--gradient-primary);
    border-radius: var(--border-radius-xl);
    padding: 1.5rem;
    margin-bottom: 2rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 2rem;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: white;
}

.stat-label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--gray-600);
}

.empty-state i {
    font-size: 5rem;
    opacity: 0.3;
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: var(--gray-700);
    margin-bottom: 1rem;
}

@media (max-width: 968px) {
    .favorites-grid {
        grid-template-columns: 1fr;
    }
    
    .favorites-grid.list-view .favorite-card {
        grid-template-columns: 1fr;
    }
    
    .favorites-grid.list-view .favorite-image-wrapper {
        height: 220px;
    }
    
    .category-filter {
        overflow-x: auto;
        flex-wrap: nowrap;
    }
}
</style>

<div class="container py-4">
    <div class="private-card">
        <div class="section-header">
            <h1 class="section-title">
                <i class="fas fa-heart"></i>
                Mes musées favoris
            </h1>
            <a href="index.php#search" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Découvrir plus
            </a>
        </div>

        <!-- Stats Banner -->
        <div class="stats-banner">
            <div class="stat-item">
                <div class="stat-value"><?= count($favorites) ?></div>
                <div class="stat-label">Musées favoris</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= count($categories) ?></div>
                <div class="stat-label">Catégories</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= count($favorites) > 0 ? number_format(array_sum(array_column($favorites, 'rating')) / count($favorites), 1) : '0.0' ?></div>
                <div class="stat-label">Note moyenne</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= count(array_filter($favorites, fn($f) => $f['price'] == 0)) ?></div>
                <div class="stat-label">Entrées gratuites</div>
            </div>
        </div>

        <!-- View Toggle and Filters -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div class="category-filter">
                <a href="?category=all" class="category-btn <?= $category === 'all' ? 'active' : '' ?>">
                    <i class="fas fa-th"></i> Tous
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="?category=<?= urlencode($cat) ?>" class="category-btn <?= $category === $cat ? 'active' : '' ?>">
                        <?= htmlspecialchars($cat) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="view-toggle">
                <button class="view-btn active" onclick="toggleView('grid')" id="grid-btn">
                    <i class="fas fa-th"></i>
                </button>
                <button class="view-btn" onclick="toggleView('list')" id="list-btn">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        <!-- Favorites Grid -->
        <?php if (empty($filteredFavorites)): ?>
            <div class="empty-state">
                <i class="fas fa-heart-broken"></i>
                <h3>Aucun musée favori dans cette catégorie</h3>
                <p>Explorez nos musées et ajoutez-les à vos favoris !</p>
                <a href="index.php" class="btn btn-primary" style="margin-top: 1.5rem;">
                    <i class="fas fa-compass"></i>
                    Explorer les musées
                </a>
            </div>
        <?php else: ?>
            <div class="favorites-grid" id="favorites-grid">
                <?php foreach ($filteredFavorites as $fav): ?>
                    <div class="favorite-card">
                        <div class="favorite-image-wrapper">
                            <img src="<?= htmlspecialchars($fav['image']) ?>" alt="<?= htmlspecialchars($fav['name']) ?>" class="favorite-image">
                            <div class="favorite-rating">
                                <i class="fas fa-star"></i>
                                <?= $fav['rating'] ?>
                            </div>
                            <div class="favorite-overlay">
                                <span class="favorite-category"><?= htmlspecialchars($fav['category']) ?></span>
                            </div>
                        </div>
                        
                        <div class="favorite-content">
                            <div>
                                <h3 class="favorite-name"><?= htmlspecialchars($fav['name']) ?></h3>
                                <p class="favorite-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($fav['location']) ?>
                                </p>
                                <p class="favorite-description"><?= htmlspecialchars($fav['description']) ?></p>
                            </div>

                            <div class="favorite-footer">
                                <div class="favorite-price <?= $fav['price'] == 0 ? 'free' : '' ?>">
                                    <?= $fav['price'] == 0 ? 'Gratuit' : number_format($fav['price'], 2) . '€' ?>
                                </div>
                                <div class="favorite-actions">
                                    <button class="action-icon" title="Réserver une visite" onclick="window.location.href='reserver.php'">
                                        <i class="fas fa-ticket-alt"></i>
                                    </button>
                                    <button class="action-icon" title="Voir les détails">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                    <button class="action-icon" title="Partager">
                                        <i class="fas fa-share-alt"></i>
                                    </button>
                                    <button class="action-icon remove" title="Retirer des favoris" onclick="return confirm('Retirer ce musée de vos favoris ?')">
                                        <i class="fas fa-heart-broken"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleView(view) {
    const grid = document.getElementById('favorites-grid');
    const gridBtn = document.getElementById('grid-btn');
    const listBtn = document.getElementById('list-btn');
    
    if (view === 'list') {
        grid.classList.add('list-view');
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
    } else {
        grid.classList.remove('list-view');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
    }
}

// Animation au chargement
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.favorite-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
</body>
</html>
