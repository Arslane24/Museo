<?php
$pageTitle = 'Choisir un avatar - MUSEO';
require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/private_nav.php';

$currentAvatar = $_SESSION['user_avatar'] ?? null;
?>

<style>
/* ==== STYLE IDENTIQUE MAIS SANS AVATARS PRÉDÉFINIS ==== */
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
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
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
    color: white;
}

.upload-card {
    background: var(--surface-primary);
    padding: 2rem;
    border-radius: var(--border-radius-xl);
    box-shadow: var(--shadow-soft);
    border: 1px solid var(--border-secondary);
}

.file-box {
    border: 2px dashed var(--border-primary);
    padding: 2rem;
    border-radius: var(--border-radius);
    text-align: center;
    transition: .3s ease;
    cursor: pointer;
}

.file-box:hover {
    border-color: var(--brand-secondary);
}

.preview-img {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    object-fit: cover;
    margin: 1rem auto;
    display: none;
}

.btn-submit {
    margin-top: 1.5rem;
    width: 100%;
}
</style>

<div class="container py-4">
    <div class="private-card">

        <div class="section-header">
            <h1 class="section-title">
                <i class="fas fa-user-circle"></i> Modifier mon avatar
            </h1>
        </div>

        <!-- Prévisualisation Avatar -->
        <div class="current-avatar-preview">
            <div class="current-avatar-display">
                <?php if ($currentAvatar): ?>
                    <img src="avatar.php?id=<?= $_SESSION['user_id'] ?>" alt="Avatar actuel">
                <?php else: ?>
                    <div style="width:100%;height:100%;background:var(--gradient-accent);display:flex;align-items:center;justify-content:center;color:white;font-size:3rem;">
                        <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="current-avatar-info">
                <h2><?= $currentAvatar ? "Votre avatar actuel" : "Aucun avatar" ?></h2>
                <p>Choisissez une image depuis votre appareil pour remplacer votre avatar.</p>
            </div>
        </div>

        <!-- Upload Card -->
        <div class="upload-card">
            <form id="avatarForm" enctype="multipart/form-data" method="POST" action="private_upload_avatar.php">

                <label class="file-box">
                    <i class="fas fa-upload fa-2x"></i>
                    <p>Choisir une image (JPEG, PNG, WEBP)</p>
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" hidden>
                </label>

                <img id="previewImg" class="preview-img">

                <button type="submit" class="btn btn-primary btn-submit" disabled id="uploadBtn">
                    <i class="fas fa-save"></i> Enregistrer l’avatar
                </button>
            </form>
        </div>

    </div>
</div>

<script>
// Prévisualisation en temps réel
const input = document.getElementById('avatarInput');
const preview = document.getElementById('previewImg');
const btn = document.getElementById('uploadBtn');

input.addEventListener('change', () => {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        btn.disabled = false;
    }
});
</script>

</body>
</html>
