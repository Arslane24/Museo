<?php
$pageTitle = 'Paramètres - MUSEO';
require_once __DIR__ . '/include/auth.php';//pour protéger la page
require_once __DIR__ . '/private_nav.php';

$message = '';
$messageType = '';

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_password'])) {
        // Simulation du changement de mot de passe
        $message = "Votre mot de passe a été modifié avec succès !";
        $messageType = "success";
    } elseif (isset($_POST['update_email'])) {
        $message = "Votre email a été mis à jour !";
        $messageType = "success";
    } elseif (isset($_POST['delete_account'])) {
        $message = "Demande de suppression envoyée. Vous recevrez un email de confirmation.";
        $messageType = "warning";
    }
}
?>

<style>
.settings-section {
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: var(--transition);
}

.settings-section:hover {
    border-color: rgba(212, 175, 55, 0.3);
}

.section-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.icon-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
.icon-warning { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
.icon-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
.icon-success { background: linear-gradient(135deg, #10b981, #059669); color: white; }

.section-title {
    color: var(--secondary-color);
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.section-description {
    color: var(--gray-600);
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    color: var(--gray-700);
    font-weight: 500;
    margin-bottom: 0.5rem;
    display: block;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius);
    color: var(--gray-700);
    font-size: 1rem;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--secondary-color);
    background: rgba(255, 255, 255, 0.08);
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius-lg);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: var(--gradient-primary);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(26, 77, 122, 0.3);
}

.btn-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(239, 68, 68, 0.3);
}

.btn-outline {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: var(--gray-700);
}

.btn-outline:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: var(--secondary-color);
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius-lg);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #10b981;
}

.alert-warning {
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.3);
    color: #f59e0b;
}

.alert-danger {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

.password-requirements {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-top: 0.5rem;
}

.password-requirements ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.password-requirements li {
    color: var(--gray-600);
    font-size: 0.875rem;
    padding: 0.25rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.password-requirements li i {
    color: var(--secondary-color);
    font-size: 0.75rem;
}

.danger-zone {
    border: 2px dashed rgba(239, 68, 68, 0.3);
    background: rgba(239, 68, 68, 0.05);
}

.two-factor-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.two-factor-info h4 {
    color: var(--gray-700);
    margin-bottom: 0.5rem;
}

.two-factor-info p {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin: 0;
}

.status-badge {
    padding: 0.375rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
}

.status-inactive {
    background: rgba(100, 116, 139, 0.2);
    color: var(--gray-500);
}

.privacy-options {
    display: grid;
    gap: 1rem;
}

.privacy-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius);
}

.privacy-label {
    color: var(--gray-700);
    font-weight: 500;
}

.privacy-description {
    color: var(--gray-600);
    font-size: 0.875rem;
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

@media (max-width: 768px) {
    .two-factor-card {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .privacy-item {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}
</style>

<div class="container py-4">
    <div style="max-width: 900px; margin: 0 auto;">
    <!-- Alert Messages -->
    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?>">
            <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : ($messageType === 'warning' ? 'exclamation-triangle' : 'info-circle') ?>"></i>
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Account Settings -->
    <div class="settings-section">
        <div class="section-icon icon-primary">
            <i class="fas fa-user-cog"></i>
        </div>
        <h2 class="section-title">Informations du compte</h2>
        <p class="section-description">Modifiez vos informations personnelles</p>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Nom complet</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" name="name">
            </div>

            <div class="form-group">
                <label class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_login']) ?>" name="login" disabled>
                <small style="color: var(--gray-600); display: block; margin-top: 0.5rem;">Le nom d'utilisateur ne peut pas être modifié</small>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" name="email">
            </div>

            <button type="submit" name="update_email" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Enregistrer les modifications
            </button>
        </form>
    </div>

    <!-- Security Settings -->
    <div class="settings-section">
        <div class="section-icon icon-warning">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h2 class="section-title">Sécurité</h2>
        <p class="section-description">Gérez la sécurité de votre compte</p>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Mot de passe actuel</label>
                <input type="password" class="form-control" name="current_password" placeholder="••••••••">
            </div>

            <div class="form-group">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" class="form-control" name="new_password" placeholder="••••••••">
                <div class="password-requirements">
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Minimum 8 caractères</li>
                        <li><i class="fas fa-check-circle"></i> Au moins une lettre majuscule</li>
                        <li><i class="fas fa-check-circle"></i> Au moins un chiffre</li>
                        <li><i class="fas fa-check-circle"></i> Au moins un caractère spécial</li>
                    </ul>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Confirmer le nouveau mot de passe</label>
                <input type="password" class="form-control" name="confirm_password" placeholder="••••••••">
            </div>

            <button type="submit" name="change_password" class="btn btn-warning">
                <i class="fas fa-key"></i>
                Changer le mot de passe
            </button>
        </form>

        <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 2rem 0;">

        <!-- Two-Factor Authentication -->
        <h3 style="color: var(--gray-700); margin-bottom: 1rem;">Authentification à deux facteurs</h3>
        <div class="two-factor-card">
            <div class="two-factor-info">
                <h4>Double authentification (2FA)</h4>
                <p>Ajoutez une couche de sécurité supplémentaire à votre compte</p>
            </div>
            <div>
                <span class="status-badge status-inactive">Inactif</span>
                <button type="button" class="btn btn-outline" style="margin-left: 1rem;">
                    <i class="fas fa-lock"></i>
                    Activer
                </button>
            </div>
        </div>
    </div>

    <!-- Privacy Settings -->
    <div class="settings-section">
        <div class="section-icon icon-success">
            <i class="fas fa-user-shield"></i>
        </div>
        <h2 class="section-title">Confidentialité</h2>
        <p class="section-description">Contrôlez la visibilité de vos informations</p>

        <div class="privacy-options">
            <div class="privacy-item">
                <div>
                    <div class="privacy-label">Profil public</div>
                    <div class="privacy-description">Autoriser les autres utilisateurs à voir votre profil</div>
                </div>
                <div class="toggle-switch" onclick="toggleSwitch(this)"></div>
            </div>

            <div class="privacy-item">
                <div>
                    <div class="privacy-label">Afficher mes réservations</div>
                    <div class="privacy-description">Rendre vos réservations visibles publiquement</div>
                </div>
                <div class="toggle-switch" onclick="toggleSwitch(this)"></div>
            </div>

            <div class="privacy-item">
                <div>
                    <div class="privacy-label">Afficher mes favoris</div>
                    <div class="privacy-description">Partager votre liste de musées favoris</div>
                </div>
                <div class="toggle-switch active" onclick="toggleSwitch(this)"></div>
            </div>
        </div>
    </div>

    <!-- Notification Settings -->
    <div class="settings-section">
        <div class="section-icon icon-primary">
            <i class="fas fa-bell"></i>
        </div>
        <h2 class="section-title">Notifications</h2>
        <p class="section-description">Gérez vos préférences de notifications</p>

        <div class="privacy-options">
            <div class="privacy-item">
                <div>
                    <div class="privacy-label">Notifications par email</div>
                    <div class="privacy-description">Recevoir des emails pour les mises à jour importantes</div>
                </div>
                <div class="toggle-switch active" onclick="toggleSwitch(this)"></div>
            </div>

            <div class="privacy-item">
                <div>
                    <div class="privacy-label">Newsletter</div>
                    <div class="privacy-description">Recevoir la newsletter mensuelle MUSEO</div>
                </div>
                <div class="toggle-switch active" onclick="toggleSwitch(this)"></div>
            </div>

            <div class="privacy-item">
                <div>
                    <div class="privacy-label">Rappels de visite</div>
                    <div class="privacy-description">Recevoir des rappels 24h avant vos visites</div>
                </div>
                <div class="toggle-switch active" onclick="toggleSwitch(this)"></div>
            </div>

            <div class="privacy-item">
                <div>
                    <div class="privacy-label">Promotions et offres</div>
                    <div class="privacy-description">Recevoir des offres spéciales et promotions</div>
                </div>
                <div class="toggle-switch" onclick="toggleSwitch(this)"></div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="settings-section danger-zone">
        <div class="section-icon icon-danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h2 class="section-title" style="color: #ef4444;">Zone dangereuse</h2>
        <p class="section-description">Actions irréversibles concernant votre compte</p>

        <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between; margin-top: 1.5rem;">
            <div>
                <h4 style="color: var(--gray-700); margin-bottom: 0.5rem;">Supprimer mon compte</h4>
                <p style="color: var(--gray-600); font-size: 0.875rem; margin: 0;">
                    Cette action est irréversible. Toutes vos données seront définitivement supprimées.
                </p>
            </div>
            <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
                <button type="submit" name="delete_account" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                    Supprimer le compte
                </button>
            </form>
        </div>
    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSwitch(element) {
    element.classList.toggle('active');
    // Ici vous pouvez ajouter un appel AJAX pour sauvegarder le paramètre
    console.log('Setting toggled:', element.classList.contains('active'));
}
</script>
</body>
</html>
