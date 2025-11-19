<?php
$pageTitle = 'Paramètres - MUSEO';
require_once __DIR__ . '/include/auth.php';//pour protéger la page
require_once __DIR__ . '/private_nav.php';
?>

<div class="private-container">
    <div class="private-card">
        <h2><i class="fas fa-cog me-2"></i>Paramètres du compte</h2>
        
        <p>Ici vous pourrez changer votre mot de passe, supprimer le compte, etc.</p>
        
        <div style="margin-top: 2rem;">
            <p style="color: var(--gray-600); font-style: italic;">
                <i class="fas fa-info-circle me-1"></i>
                Fonctionnalités à venir : modification du mot de passe, suppression du compte, paramètres de confidentialité.
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
