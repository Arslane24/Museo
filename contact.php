<?php
require_once 'secret/api_keys.php';
$page_title = "Contact";
$page_description = "Contactez l'équipe MuseoLink. Besoin d'aide pour votre réservation ? Une question sur nos services ? Notre équipe est à votre disposition pour vous assister.";
$page_keywords = "contact museolink, aide réservation, support client, service client musée, questions réservation";

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $messageContent = trim($_POST['message'] ?? '');
    
    if ($name && $email && $subject && $messageContent) {
        // Here you would normally send email or save to database
        $message = "Merci pour votre message ! Nous vous répondrons dans les plus brefs délais.";
        $messageType = "success";
    } else {
        $message = "Veuillez remplir tous les champs du formulaire.";
        $messageType = "danger";
    }
}

include 'include/header.php';
?>

<!-- Hero Section Contact -->
<section class="hero-section" style="background: linear-gradient(135deg, #1a4d7a 0%, #2c5f8d 100%); min-height: 40vh; padding-top: 120px;">
    <div class="hero-content">
        <div class="hero-text-wrapper text-center">
            <h1 class="hero-title animate-up" style="color: white; font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem;">Contactez-Nous</h1>
            <p class="hero-subtitle animate-up-delay-1" style="color: rgba(255,255,255,0.9); font-size: 1.2rem;">
                Notre équipe est à votre écoute
            </p>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-lg border-0" style="border-radius: 20px;">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4" style="color: #1a4d7a;">Envoyez-nous un message</h2>
                        
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="fas fa-user me-2" style="color: #c9a961;"></i>Nom complet
                                </label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name" 
                                       placeholder="Votre nom" required style="border-radius: 12px;">
                            </div>
                            
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-2" style="color: #c9a961;"></i>Email
                                </label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                       placeholder="votre@email.com" required style="border-radius: 12px;">
                            </div>
                            
                            <div class="mb-4">
                                <label for="subject" class="form-label fw-semibold">
                                    <i class="fas fa-tag me-2" style="color: #c9a961;"></i>Sujet
                                </label>
                                <select class="form-select form-select-lg" id="subject" name="subject" required style="border-radius: 12px;">
                                    <option value="">Sélectionnez un sujet</option>
                                    <option value="reservation">Question sur une réservation</option>
                                    <option value="annulation">Annulation / Modification</option>
                                    <option value="information">Demande d'information</option>
                                    <option value="technique">Problème technique</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="form-label fw-semibold">
                                    <i class="fas fa-comment me-2" style="color: #c9a961;"></i>Message
                                </label>
                                <textarea class="form-control form-control-lg" id="message" name="message" 
                                          rows="5" placeholder="Votre message..." required style="border-radius: 12px;"></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-lg py-3" 
                                        style="background: linear-gradient(135deg, #c9a961 0%, #dfc480 100%); 
                                               color: #0f172a; border: none; border-radius: 50px; 
                                               font-weight: 700; font-size: 1.1rem;">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer le message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact Info Sidebar -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card shadow border-0 mb-4" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <h4 class="mb-4" style="color: #1a4d7a;">
                            <i class="fas fa-info-circle me-2"></i>Informations
                        </h4>
                        
                        <div class="mb-3">
                            <h6 class="fw-bold" style="color: #c9a961;">
                                <i class="fas fa-clock me-2"></i>Horaires
                            </h6>
                            <p class="mb-0 text-muted">Lundi - Vendredi: 9h - 18h</p>
                            <p class="mb-0 text-muted">Samedi: 10h - 16h</p>
                            <p class="text-muted">Dimanche: Fermé</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="fw-bold" style="color: #c9a961;">
                                <i class="fas fa-envelope me-2"></i>Email
                            </h6>
                            <p class="mb-0 text-muted">contact@museo.com</p>
                            <p class="text-muted">support@museo.com</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="fw-bold" style="color: #c9a961;">
                                <i class="fas fa-phone me-2"></i>Téléphone
                            </h6>
                            <p class="mb-0 text-muted">+33 1 23 45 67 89</p>
                            <p class="text-muted">+33 1 23 45 67 90</p>
                        </div>
                        
                        <div>
                            <h6 class="fw-bold" style="color: #c9a961;">
                                <i class="fas fa-share-alt me-2"></i>Réseaux sociaux
                            </h6>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="btn btn-outline-info btn-sm">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-outline-danger btn-sm">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow border-0" style="border-radius: 20px; background: linear-gradient(135deg, #1a4d7a 0%, #2c5f8d 100%);">
                    <div class="card-body p-4 text-white text-center">
                        <i class="fas fa-question-circle fa-3x mb-3" style="color: #c9a961;"></i>
                        <h5>Besoin d'aide ?</h5>
                        <p class="mb-3">Consultez notre FAQ pour trouver rapidement des réponses</p>
                        <a href="#" class="btn btn-light btn-sm">
                            <i class="fas fa-book me-2"></i>Voir la FAQ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'include/footer.php'; ?>
