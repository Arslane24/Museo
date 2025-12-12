<?php
$pageTitle = 'Paramètres - MUSEO';
require_once __DIR__ . '/include/auth.php'; // protège la page
require_once __DIR__ . '/private_nav.php';

// Connexion à la base de données
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

$message = '';
$messageType = '';

$userId = $_SESSION['user_id'] ?? null;

// Protection au cas où
if (!$userId) {
    header('Location: login.php');
    exit;
}

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1) Mise à jour nom + email
    if (isset($_POST['update_account'])) {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {
            $message = "Le nom et l'email sont obligatoires.";
            $messageType = "danger";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $userId]);

            // Mettre à jour la session
            $_SESSION['user_name']  = $name;
            $_SESSION['user_email'] = $email;

            $message = "Vos informations ont été mises à jour.";
            $messageType = "success";
        }
    }

    // 2) Changement de mot de passe
    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        // Récupérer le hash actuel
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $hash = $stmt->fetchColumn();

        if (!$hash || !password_verify($current, $hash)) {
            $message = "Mot de passe actuel incorrect.";
            $messageType = "danger";
        } elseif ($new !== $confirm) {
            $message = "Les deux nouveaux mots de passe ne correspondent pas.";
            $messageType = "danger";
        } elseif (strlen($new) < 8) {
            $message = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
            $messageType = "danger";
        } else {
            $newHash = password_hash($new, PASSWORD_BCRYPT);
            $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd->execute([$newHash, $userId]);

            $message = "Votre mot de passe a été modifié avec succès.";
            $messageType = "success";
        }
    }

    // 3) Suppression du compte
    if (isset($_POST['delete_account'])) {
        // Suppression de l'utilisateur (favorites supprimés via ON DELETE CASCADE)
        $del = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $del->execute([$userId]);

        session_destroy();
        header("Location: index.php?account_deleted=1");
        exit;
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
.form-control[disabled] {
    background: rgba(255, 255, 255, 0.15) !important;
    color: white !important;
    opacity: 1 !important;
    cursor: not-allowed;
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

@media (max-width: 768px) {
    /* mêmes règles responsives que ton ancienne page si besoin */
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
                    <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_login']) ?>" disabled>
                    <small style="color: var(--gray-600); display: block; margin-top: 0.5rem;">Le nom d'utilisateur ne peut pas être modifié</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" name="email">
                </div>

                <button type="submit" name="update_account" class="btn btn-primary">
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
</body>
</html>
