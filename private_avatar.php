<?php
$pageTitle = 'Choisir un avatar - MUSEO';
require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/private_nav.php';

// Traitement de la sélection d'avatar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['avatar'])) {
    $_SESSION['user_avatar'] = $_POST['avatar'];
    header('Location: private_profile.php?avatar_updated=1');
    exit;
}

// Liste des avatars disponibles
$avatars = [
    // Avatars professionnels
    ['id' => 'avatar-1', 'url' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Felix&backgroundColor=b6e3f4', 'category' => 'Professionnel'],
    ['id' => 'avatar-2', 'url' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Aneka&backgroundColor=c0aede', 'category' => 'Professionnel'],
    ['id' => 'avatar-3', 'url' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Jasmine&backgroundColor=ffd5dc', 'category' => 'Professionnel'],
    ['id' => 'avatar-4', 'url' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Oscar&backgroundColor=d1d4f9', 'category' => 'Professionnel'],
    ['id' => 'avatar-5', 'url' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Lily&backgroundColor=ffdfbf', 'category' => 'Professionnel'],
    ['id' => 'avatar-6', 'url' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Max&backgroundColor=d4f1f4', 'category' => 'Professionnel'],
    
    // Avatars créatifs
    ['id' => 'avatar-7', 'url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=Felix&backgroundColor=b6e3f4', 'category' => 'Créatif'],
    ['id' => 'avatar-8', 'url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=Aneka&backgroundColor=c0aede', 'category' => 'Créatif'],
    ['id' => 'avatar-9', 'url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=Jasmine&backgroundColor=ffd5dc', 'category' => 'Créatif'],
    ['id' => 'avatar-10', 'url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=Oscar&backgroundColor=d1d4f9', 'category' => 'Créatif'],
    ['id' => 'avatar-11', 'url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=Lily&backgroundColor=ffdfbf', 'category' => 'Créatif'],
    ['id' => 'avatar-12', 'url' => 'https://api.dicebear.com/7.x/bottts/svg?seed=Max&backgroundColor=d4f1f4', 'category' => 'Créatif'],
    
    // Avatars amusants
    ['id' => 'avatar-13', 'url' => 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=Felix&backgroundColor=b6e3f4', 'category' => 'Amusant'],
    ['id' => 'avatar-14', 'url' => 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=Aneka&backgroundColor=c0aede', 'category' => 'Amusant'],
    ['id' => 'avatar-15', 'url' => 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=Jasmine&backgroundColor=ffd5dc', 'category' => 'Amusant'],
    ['id' => 'avatar-16', 'url' => 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=Oscar&backgroundColor=d1d4f9', 'category' => 'Amusant'],
    ['id' => 'avatar-17', 'url' => 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=Lily&backgroundColor=ffdfbf', 'category' => 'Amusant'],
    ['id' => 'avatar-18', 'url' => 'https://api.dicebear.com/7.x/fun-emoji/svg?seed=Max&backgroundColor=d4f1f4', 'category' => 'Amusant'],
    
    // Avatars abstraits
    ['id' => 'avatar-19', 'url' => 'https://api.dicebear.com/7.x/shapes/svg?seed=Felix&backgroundColor=b6e3f4', 'category' => 'Abstrait'],
    ['id' => 'avatar-20', 'url' => 'https://api.dicebear.com/7.x/shapes/svg?seed=Aneka&backgroundColor=c0aede', 'category' => 'Abstrait'],
    ['id' => 'avatar-21', 'url' => 'https://api.dicebear.com/7.x/shapes/svg?seed=Jasmine&backgroundColor=ffd5dc', 'category' => 'Abstrait'],
    ['id' => 'avatar-22', 'url' => 'https://api.dicebear.com/7.x/shapes/svg?seed=Oscar&backgroundColor=d1d4f9', 'category' => 'Abstrait'],
    ['id' => 'avatar-23', 'url' => 'https://api.dicebear.com/7.x/shapes/svg?seed=Lily&backgroundColor=ffdfbf', 'category' => 'Abstrait'],
    ['id' => 'avatar-24', 'url' => 'https://api.dicebear.com/7.x/shapes/svg?seed=Max&backgroundColor=d4f1f4', 'category' => 'Abstrait'],
];

// Grouper par catégorie
$avatarsByCategory = [];
foreach ($avatars as $avatar) {
    $avatarsByCategory[$avatar['category']][] = $avatar;
}

$currentAvatar = $_SESSION['user_avatar'] ?? null;
?>

<style>
.current-avatar-preview {
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

.current-avatar-preview::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.2) 0%, transparent 70%);
    border-radius: 50%;
}

.current-avatar-display {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: white;
    padding: 0.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 1;
    overflow: hidden;
}

.current-avatar-display img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.current-avatar-info {
    flex: 1;
    position: relative;
    z-index: 1;
}

.current-avatar-info h2 {
    color: white;
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
}

.current-avatar-info p {
    color: rgba(255, 255, 255, 0.9);
}

.category-section {
    margin-bottom: 3rem;
}

.category-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.category-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: var(--gradient-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.category-title {
    color: var(--secondary-color);
    font-size: 1.5rem;
    font-weight: 600;
}

.avatars-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
}

.avatar-option {
    position: relative;
    cursor: pointer;
    transition: var(--transition);
}

.avatar-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.avatar-box {
    background: var(--gradient-card);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-lg);
    padding: 0.75rem;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.avatar-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--gradient-accent);
    opacity: 0;
    transition: var(--transition);
}

.avatar-option:hover .avatar-box {
    transform: translateY(-5px);
    border-color: var(--secondary-color);
    box-shadow: 0 10px 30px rgba(212, 175, 55, 0.2);
}

.avatar-option input[type="radio"]:checked + .avatar-box {
    border-color: var(--secondary-color);
    border-width: 3px;
}

.avatar-option input[type="radio"]:checked + .avatar-box::before {
    opacity: 0.1;
}

.avatar-image-wrapper {
    width: 100%;
    aspect-ratio: 1;
    border-radius: var(--border-radius);
    overflow: hidden;
    background: white;
    position: relative;
    z-index: 1;
}

.avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-check {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--secondary-color);
    display: none;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    z-index: 2;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.avatar-option input[type="radio"]:checked ~ .avatar-check {
    display: flex;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.btn {
    padding: 0.875rem 2rem;
    border-radius: var(--border-radius-lg);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
}

.btn-primary {
    background: var(--gradient-primary);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(26, 77, 122, 0.3);
}

.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.btn-outline {
    background: transparent;
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: var(--gray-700);
}

.btn-outline:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: var(--secondary-color);
    color: var(--secondary-color);
}

.info-box {
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: var(--border-radius-lg);
    padding: 1rem 1.5rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    color: #3b82f6;
}

.info-box i {
    font-size: 1.5rem;
}

@media (max-width: 768px) {
    .current-avatar-preview {
        flex-direction: column;
        text-align: center;
    }
    
    .avatars-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}

/* Animation d'entrée */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.avatar-option {
    animation: fadeInUp 0.5s ease-out forwards;
}

.avatar-option:nth-child(1) { animation-delay: 0.05s; }
.avatar-option:nth-child(2) { animation-delay: 0.1s; }
.avatar-option:nth-child(3) { animation-delay: 0.15s; }
.avatar-option:nth-child(4) { animation-delay: 0.2s; }
.avatar-option:nth-child(5) { animation-delay: 0.25s; }
.avatar-option:nth-child(6) { animation-delay: 0.3s; }
</style>

<div class="container py-4">
    <div class="private-card">
        <div class="section-header">
            <h1 class="section-title">
                <i class="fas fa-user-circle"></i>
                Choisir un avatar
            </h1>
        </div>

        <!-- Current Avatar Preview -->
        <div class="current-avatar-preview">
            <div class="current-avatar-display">
                <?php if ($currentAvatar): ?>
                    <img src="<?= htmlspecialchars($currentAvatar) ?>" alt="Avatar actuel">
                <?php else: ?>
                    <div style="width: 100%; height: 100%; background: var(--gradient-accent); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; border-radius: 50%;">
                        <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="current-avatar-info">
                <h2><?= $currentAvatar ? 'Votre avatar actuel' : 'Aucun avatar sélectionné' ?></h2>
                <p>
                    <?= $currentAvatar ? 'Choisissez un nouvel avatar ci-dessous pour le modifier.' : 'Sélectionnez un avatar dans la galerie ci-dessous.' ?>
                </p>
            </div>
        </div>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Astuce :</strong> Votre avatar sera visible sur votre profil et dans toutes vos interactions sur MUSEO.
            </div>
        </div>

        <form method="POST" id="avatar-form">
            <?php foreach ($avatarsByCategory as $category => $categoryAvatars): ?>
                <div class="category-section">
                    <div class="category-header">
                        <div class="category-icon">
                            <i class="fas fa-<?= $category === 'Professionnel' ? 'user-tie' : ($category === 'Créatif' ? 'robot' : ($category === 'Amusant' ? 'smile' : 'shapes')) ?>"></i>
                        </div>
                        <h2 class="category-title"><?= htmlspecialchars($category) ?></h2>
                    </div>

                    <div class="avatars-grid">
                        <?php foreach ($categoryAvatars as $avatar): ?>
                            <label class="avatar-option">
                                <input 
                                    type="radio" 
                                    name="avatar" 
                                    value="<?= htmlspecialchars($avatar['url']) ?>"
                                    <?= $currentAvatar === $avatar['url'] ? 'checked' : '' ?>
                                    onchange="document.getElementById('save-btn').disabled = false"
                                >
                                <div class="avatar-box">
                                    <div class="avatar-image-wrapper">
                                        <img src="<?= htmlspecialchars($avatar['url']) ?>" alt="<?= htmlspecialchars($avatar['id']) ?>" class="avatar-image">
                                    </div>
                                </div>
                                <div class="avatar-check">
                                    <i class="fas fa-check"></i>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="action-buttons">
                <button type="submit" class="btn btn-primary" id="save-btn" disabled>
                    <i class="fas fa-save"></i>
                    Enregistrer l'avatar
                </button>
                <a href="private_profile.php" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Activer le bouton si un avatar différent est sélectionné
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('avatar-form');
    const saveBtn = document.getElementById('save-btn');
    const currentAvatar = <?= json_encode($currentAvatar) ?>;
    
    // Prévisualisation en temps réel
    const radioButtons = document.querySelectorAll('input[type="radio"][name="avatar"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            const preview = document.querySelector('.current-avatar-display');
            if (this.value) {
                preview.innerHTML = `<img src="${this.value}" alt="Aperçu">`;
            }
            
            // Activer le bouton uniquement si l'avatar est différent
            if (this.value !== currentAvatar) {
                saveBtn.disabled = false;
            }
        });
    });
    
    // Animation lors de la soumission
    form.addEventListener('submit', function() {
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
        saveBtn.disabled = true;
    });
});
</script>
<!-- Script de thème intégré dans private_nav.php -->
</body>
</html>
