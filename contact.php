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

<link rel="stylesheet" href="css/contact.css">

<!-- Hero Section Contact -->
<section class="contact-hero">
    <div class="container">
        <div class="text-center">
            <h1>Contactez-Nous</h1>
            <p>Notre équipe est à votre écoute pour répondre à toutes vos questions</p>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="contact-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> contact-alert alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="contact-card">
                    <h2 class="text-center">Envoyez-nous un message</h2>
                        
                        <form method="POST" class="contact-form">
                            <div class="mb-4">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nom complet
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="Votre nom complet" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="votre@email.com" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="subject" class="form-label">
                                    <i class="fas fa-tag me-2"></i>Sujet
                                </label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Sélectionnez un sujet</option>
                                    <option value="reservation">Question sur une réservation</option>
                                    <option value="annulation">Annulation / Modification</option>
                                    <option value="information">Demande d'information</option>
                                    <option value="technique">Problème technique</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="form-label">
                                    <i class="fas fa-comment me-2"></i>Message
                                </label>
                                <textarea class="form-control" id="message" name="message" 
                                          rows="5" placeholder="Décrivez votre demande en détail..." required></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="contact-submit-btn">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer le message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            
            <!-- Contact Info Sidebar -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="contact-info-card">
                    <h4><i class="fas fa-info-circle me-2"></i>Informations</h4>
                        
                        <div class="mb-3">
                            <h6><i class="fas fa-clock me-2"></i>Horaires</h6>
                            <p class="mb-0">Lundi - Vendredi: 9h - 18h</p>
                            <p class="mb-0">Samedi: 10h - 16h</p>
                            <p>Dimanche: Fermé</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="fas fa-envelope me-2"></i>Email</h6>
                            <p class="mb-0">contact@museo.com</p>
                            <p>support@museo.com</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="fas fa-phone me-2"></i>Téléphone</h6>
                            <p class="mb-0">+33 1 23 45 67 89</p>
                            <p>+33 1 23 45 67 90</p>
                        </div>
                        
                        <div>
                            <h6><i class="fas fa-share-alt me-2"></i>Réseaux sociaux</h6>
                            <div class="social-buttons">
                                <a href="#" class="btn btn-outline-primary" title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="btn btn-outline-info" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-outline-danger" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="btn btn-outline-primary" title="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                
                <div class="help-card">
                    <i class="fas fa-question-circle fa-3x"></i>
                    <h5>Besoin d'aide ?</h5>
                    <p>Consultez notre FAQ pour trouver rapidement des réponses</p>
                    <a href="#" class="btn btn-light">
                        <i class="fas fa-book me-2"></i>Voir la FAQ
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'include/footer.php'; ?>
