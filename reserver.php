<?php
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

$pageTitle = 'Réserver - MuseoLink';

// Récupérer les musées depuis la base de données
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

$museums = [];
$stmt = $pdo->query("SELECT id, name, city, country FROM museums WHERE is_active = 1 ORDER BY name LIMIT 20");
$museums = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le musée présélectionné depuis l'URL
$selectedMuseumId = isset($_GET['museum_id']) ? (int)$_GET['museum_id'] : null;

// If form is submitted - NOT USED anymore, using API instead
$message = "";
$messageType = "";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Réservez vos billets de musée en ligne avec Museo. Sélectionnez votre musée préféré, choisissez la date et l'heure de votre visite et confirmez votre réservation en quelques clics.">
    <meta name="keywords" content="réserver musée, billets musée en ligne, réservation visite musée, acheter billets musée, booking musée">
    <meta name="author" content="Museo">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:title" content="Réserver - MuseoLink">
    <meta property="og:description" content="Réservez vos billets de musée en ligne avec MuseoLink">
    <meta property="og:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST']; ?>/public/images/logo.png">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Réserver - MuseoLink">
    <meta name="twitter:description" content="Réservez vos billets de musée en ligne avec MuseoLink">
    
    <link rel="icon" type="image/x-icon" href="public/images/logo.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/auth-forms.css" rel="stylesheet">
    <style>
        .reservation-container {
            min-height: 100vh;
            padding: 6rem 0 3rem 0;
            background: var(--dark-color);
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(201, 169, 97, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(26, 77, 122, 0.15) 0%, transparent 50%);
        }
        
        .reservation-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-xl);
            padding: 3rem;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .reservation-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .reservation-header h1 {
            color: var(--secondary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .reservation-header p {
            color: var(--gray-600);
            font-size: 1.1rem;
        }
        
        .museum-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .museum-option {
            position: relative;
            cursor: pointer;
        }
        
        .museum-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        
        .museum-option label {
            display: block;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-lg);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            color: var(--gray-700);
        }
        
        .museum-option input[type="radio"]:checked + label {
            background: rgba(201, 169, 97, 0.15);
            border-color: var(--secondary-color);
            color: var(--secondary-color);
        }
        
        .museum-option label:hover {
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .museum-option i {
            font-size: 2rem;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .date-time-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .visitor-count {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .form-control select option,
        select.form-control option {
            background: var(--dark-color);
            color: var(--gray-700);
            padding: 0.5rem;
        }
        
        select.form-control,
        input[type="date"].form-control {
            background: rgba(255, 255, 255, 0.08);
            color: var(--gray-800);
            cursor: pointer;
        }
        
        select.form-control:focus,
        input[type="date"].form-control:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(201, 169, 97, 0.25);
            color: var(--white);
        }
        
        select.form-control option:hover {
            background: rgba(201, 169, 97, 0.2);
        }
        
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
            opacity: 0.8;
        }
        
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }
        
        input[type="date"] {
            color-scheme: dark;
        }
        
        /* Navigation bar for logged in users */
        .reservation-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0;
            z-index: 1000;
        }
        
        .reservation-nav .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .nav-user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--gray-700);
        }
        
        .nav-user-info i {
            color: var(--secondary-color);
        }
        
        .nav-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .nav-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-lg);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        
        .nav-btn-primary {
            background: rgba(201, 169, 97, 0.15);
            color: var(--secondary-color);
            border: 1px solid rgba(201, 169, 97, 0.3);
        }
        
        .nav-btn-primary:hover {
            background: rgba(201, 169, 97, 0.25);
            color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .nav-btn-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .nav-btn-danger:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #ef4444;
            transform: translateY(-2px);
        }
        
        .reservation-container.with-nav {
            padding-top: 8rem;
        }
        
        @media (max-width: 768px) {
            .reservation-nav .container {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .nav-actions {
                width: 100%;
                justify-content: space-between;
            }
        }
        
        .pricing-info {
            background: rgba(201, 169, 97, 0.1);
            border-left: 4px solid var(--secondary-color);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            margin: 2rem 0;
            color: var(--gray-700);
        }
        
        .pricing-info h4 {
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }
        
        .pricing-info ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .pricing-info li {
            padding: 0.5rem 0;
            display: flex;
            justify-content: space-between;
        }
        
        .login-prompt {
            text-align: center;
            padding: 3rem;
        }
        
        .login-prompt i {
            font-size: 4rem;
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
        }
        
        .login-prompt h2 {
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }
        
        .login-prompt p {
            color: var(--gray-600);
            margin-bottom: 2rem;
        }
        
        @media (max-width: 768px) {
            .reservation-card {
                padding: 2rem;
                margin: 0 1rem;
            }
            
            .date-time-grid,
            .visitor-count {
                grid-template-columns: 1fr;
            }
            
            .museum-selector {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php if ($isLoggedIn): ?>
        <!-- Navigation for logged in users -->
        <nav class="reservation-nav">
            <div class="container">
                <div class="nav-user-info">
                    <i class="fas fa-user-circle fa-lg"></i>
                    <span>Bienvenue, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></span>
                </div>
                <div class="nav-actions">
                    <a href="private_dash.php" class="nav-btn nav-btn-primary">
                        <i class="fas fa-th-large"></i>
                        Dashboard
                    </a>
                    <a href="logout.php" class="nav-btn nav-btn-danger">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </div>
            </div>
        </nav>
    <?php else: ?>
        <a href="index.php" class="back-home">
            <i class="fas fa-arrow-left"></i>
            <span>Retour à l'accueil</span>
        </a>
    <?php endif; ?>

    <div class="reservation-container <?= $isLoggedIn ? 'with-nav' : '' ?>">
        <div class="container">
            <div class="reservation-card" data-aos="zoom-in">
                
                <?php if ($isLoggedIn): ?>
                    <div class="reservation-header">
                        <h1><i class="fas fa-ticket-alt me-2"></i>Réserver une visite</h1>
                        <p>Planifiez votre prochaine découverte culturelle</p>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?= $messageType ?> text-center">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="reservationForm">
                        
                        <!-- Museum Selection -->
                        <div class="form-group">
                            <label class="form-label mb-3">
                                <i class="fas fa-landmark me-2"></i>Choisissez votre musée
                            </label>
                            <div class="input-group">
                                <i class="fas fa-landmark"></i>
                                <select class="form-control" name="museum" id="museumSelect" required>
                                    <option value="">-- Sélectionner un musée --</option>
                                    <?php foreach ($museums as $museum): ?>
                                        <option value="<?= $museum['id'] ?>" <?= ($selectedMuseumId == $museum['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($museum['name']) ?> - <?= htmlspecialchars($museum['city']) ?>, <?= htmlspecialchars($museum['country']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Date and Time -->
                        <div class="date-time-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-2"></i>Date de visite
                                </label>
                                <div class="input-group">
                                    <i class="fas fa-calendar"></i>
                                    <input type="date" class="form-control" name="date" required 
                                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-clock me-2"></i>Heure
                                </label>
                                <div class="input-group">
                                    <i class="fas fa-clock"></i>
                                    <select class="form-control" name="time" required>
                                        <option value="">Choisir...</option>
                                        <option value="09:00">09:00</option>
                                        <option value="10:00">10:00</option>
                                        <option value="11:00">11:00</option>
                                        <option value="14:00">14:00</option>
                                        <option value="15:00">15:00</option>
                                        <option value="16:00">16:00</option>
                                        <option value="17:00">17:00</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Visitor Count -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-users me-2"></i>Nombre de personnes
                            </label>
                            <div class="input-group">
                                <i class="fas fa-users"></i>
                                <input type="number" class="form-control" name="number_of_people" id="numberOfPeople"
                                       value="1" min="1" max="20" required>
                            </div>
                            <small class="text-muted">Enfants (-18 ans) : gratuit</small>
                        </div>

                        <!-- Pricing Info -->
                        <div class="pricing-info">
                            <h4><i class="fas fa-info-circle me-2"></i>Tarifs</h4>
                            <ul>
                                <li>
                                    <span>Adulte</span>
                                    <span><strong>15€</strong></span>
                                </li>
                                <li>
                                    <span>Enfant (-18 ans)</span>
                                    <span><strong>Gratuit</strong></span>
                                </li>
                                <li>
                                    <span>Étudiant (sur justificatif)</span>
                                    <span><strong>10€</strong></span>
                                </li>
                            </ul>
                        </div>

                        <!-- Additional Notes -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-comment me-2"></i>Commentaires (optionnel)
                            </label>
                            <div class="input-group">
                                <textarea class="form-control" name="comments" rows="3" 
                                          placeholder="Demandes spéciales, accessibilité..."></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-auth">
                            <i class="fas fa-check me-2"></i>Confirmer la réservation
                        </button>
                    </form>

                <?php else: ?>
                    <!-- Login Prompt -->
                    <div class="login-prompt">
                        <i class="fas fa-lock"></i>
                        <h2>Connexion requise</h2>
                        <p>Vous devez être connecté pour effectuer une réservation.</p>
                        <a href="login.php?redirect=reserver.php" class="btn btn-auth" style="display: inline-block; max-width: 300px;">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </a>
                        <div style="margin-top: 1.5rem;">
                            <p style="color: var(--gray-600);">
                                Pas encore de compte ?
                                <a href="register.php" class="auth-link">Créer un compte</a>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                once: true
            });
        }

        // Form validation and UX enhancements
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('reservationForm');
            
            if (form) {
                // Prevent past dates
                const dateInput = document.querySelector('input[type="date"]');
                if (dateInput) {
                    const today = new Date();
                    today.setDate(today.getDate() + 1);
                    const minDate = today.toISOString().split('T')[0];
                    dateInput.setAttribute('min', minDate);
                }
                
                // Form submission - use API
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const museumId = document.getElementById('museumSelect').value;
                    const visitDate = document.querySelector('input[name="date"]').value;
                    const visitTime = document.querySelector('select[name="time"]').value;
                    const numberOfPeople = document.getElementById('numberOfPeople').value;
                    const comments = document.querySelector('textarea[name="comments"]').value || '';
                    
                    // Validation
                    if (!museumId || !visitDate || !visitTime) {
                        alert('Veuillez remplir tous les champs obligatoires');
                        return;
                    }
                    
                    // Show loading
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement...';
                    submitBtn.disabled = true;
                    
                    try {
                        const response = await fetch('api/reservations.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                museum_id: parseInt(museumId),
                                visit_date: visitDate,
                                visit_time: visitTime,
                                number_of_people: parseInt(numberOfPeople),
                                message: comments
                            })
                        });
                        
                        console.log('Response status:', response.status);
                        
                        // Récupérer le texte brut d'abord
                        const responseText = await response.text();
                        console.log('Response text:', responseText);
                        
                        // Essayer de parser en JSON
                        let data;
                        try {
                            data = JSON.parse(responseText);
                        } catch (parseError) {
                            console.error('Erreur de parsing JSON:', parseError);
                            console.log('Contenu reçu (premiers 500 caractères):', responseText.substring(0, 500));
                            alert('❌ Erreur serveur: La réponse n\'est pas au format JSON. Vérifiez la console.');
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                            return;
                        }
                        
                        console.log('Response data:', data);
                        
                        if (data.success) {
                            // Success - show message and redirect
                            alert('Réservation confirmée !\n\nCode de réservation : ' + data.reservation.reservation_code + '\n\nUn email de confirmation vous a été envoyé.');
                            window.location.href = 'private_reservations.php';
                        } else {
                            // Error
                            alert('❌ Erreur : ' + (data.error || 'Une erreur est survenue'));
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    } catch (error) {
                        console.error('Erreur complète:', error);
                        alert('❌ Erreur de connexion. Veuillez vérifier la console pour plus de détails.');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                });
            }
        });
    </script>
</body>
</html>
