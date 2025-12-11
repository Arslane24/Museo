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

// Transformer les données pour le format attendu (sans infos fictives)
$favorites = [];
foreach ($favoritesData as $fav) {
    $favorites[] = [
        'id'         => $fav['id'],
        'slug'       => $fav['slug'] ?? null,
        'name'       => $fav['name'],
        'location'   => trim(($fav['city'] ?? '') . ', ' . ($fav['country'] ?? ''), ', '),
        'description'=> $fav['description'] ?? 'Découvrez ce musée exceptionnel',
        'category'   => $fav['category'] ?? 'Autre',
        'rating'     => $fav['rating'] ?? '—',
        'price'      => $fav['price'] ?? 0.00,
        'image'      => $fav['image_url'] ?? 'https://images.unsplash.com/photo-1566438480900-0609be27a4be?w=500&h=300&fit=crop'
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
<main id="main-content" role="main">

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

@media (max-width: 768px) {
    .favorites-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }
}

@media (max-width: 576px) {
    .favorites-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .favorite-image-wrapper {
        height: 200px;
    }
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

</style>


<div class="container py-4">
    <div class="private-card">
        <div class="section-header">
            <h1 class="section-title">
                <i class="fas fa-heart"></i>
                Mes musées favoris
            </h1>
            <a href="Explorer.php" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Découvrir plus
            </a>
        </div>

        <!-- Favorites Grid -->
        <?php if (empty($filteredFavorites)): ?>
            <div class="empty-state">
                <i class="fas fa-heart-broken"></i>
                <h3>Aucun musée favori</h3>
                <p>Ajoutez des musées depuis la page Explorer.</p>
            </div>

        <?php else: ?>
            <div class="favorites-grid" id="favorites-grid">

                <?php foreach ($filteredFavorites as $fav): ?>
                <div class="favorite-card">

                    <div class="favorite-image-wrapper">
                        <img src="<?= htmlspecialchars($fav['image']) ?>" 
                             alt="<?= htmlspecialchars($fav['name']) ?>" 
                             class="favorite-image">

                        <div class="favorite-rating">
                            <i class="fas fa-star"></i>
                            <?= $fav['rating'] ?>
                        </div>

                        <div class="favorite-overlay">
                            <span class="favorite-category"><?= htmlspecialchars($fav['category']) ?></span>
                        </div>
                    </div>

                    <div class="favorite-content">
                        <h3 class="favorite-name"><?= htmlspecialchars($fav['name']) ?></h3>

                        <p class="favorite-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($fav['location']) ?>
                        </p>

                        <p class="favorite-description"><?= htmlspecialchars($fav['description']) ?></p>

                        <div class="favorite-footer">


                            <div class="favorite-actions">

                               
                                <button class="action-icon"
                                        title="Réserver une visite"
                                        onclick="window.location.href='reserver.php?museum_id=<?= $fav['id'] ?>'">
                                    <i class="fas fa-ticket-alt"></i>
                                </button>

                              
                                <button class="action-icon"
                                        title="Voir les détails"
                                        onclick="window.location.href='musee-detail.php?slug=<?= urlencode($fav['slug']) ?>'">
                                    <i class="fas fa-info-circle"></i>
                                </button>

                          
                                <button class="action-icon remove" 
                                        data-id="<?= $fav['id'] ?>" 
                                        onclick="removeFavorite(this)"
                                        title="Retirer des favoris">
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

<script>
async function removeFavorite(btn) {
    if (!confirm("Retirer ce musée de vos favoris ?")) return;

    const id = btn.dataset.id;
    const formData = new FormData();
    formData.append("museum_id", id);

    const response = await fetch("api/favorites-remove.php", {
        method: "POST",
        body: formData
    });

    const data = await response.json();
    if (data.success) {
        const card = btn.closest(".favorite-card");
        card.style.opacity = "0";
        card.style.transform = "scale(0.9)";
        setTimeout(() => card.remove(), 200);
    }
}
</script>
</main>

</body>
</html>
