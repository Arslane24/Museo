<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="icon" type="image/x-icon" href="public/images/logo.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/hero-animations.css" rel="stylesheet">
    <style>
    .museum-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
    }
    .museum-card:hover img {
        transform: scale(1.1);
    }
    .museum-card:hover .museum-overlay {
        opacity: 1;
    }
    .museum-card .btn:hover {
        background: linear-gradient(135deg, #d4af37, #f0c748) !important;
        color: white !important;
        transform: scale(1.05);
    }
    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border-color: #d4af37;
    }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="public/images/logo.png" alt="MUSEO" height="70" class="logo-icon">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Inscription</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reserver.php">Réserver</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="Explorer.php">Explorer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    
                   
                </ul>
            </div>
        </div>
    </nav>

    <!-- Overlay backdrop pour menu hamburger -->
    <div class="navbar-backdrop" id="navbarBackdrop"></div>

    <script>
        // ========== GESTION AMÉLIORÉE DE L'OVERLAY BACKDROP ==========
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggler = document.querySelector('.navbar-toggler');
            const navbarCollapse = document.getElementById('navbarNav');
            const backdrop = document.getElementById('navbarBackdrop');
            const navbar = document.querySelector('.navbar');
            
            if (!navbarToggler || !navbarCollapse || !backdrop) {
                console.warn('Éléments du menu hamburger non trouvés');
                return;
            }
            
            // Fonction pour afficher l'overlay
            function showBackdrop() {
                if (window.innerWidth < 992) { // Seulement en mode responsive
                    backdrop.classList.add('show');
                    document.body.style.overflow = 'hidden';
                    console.log('Overlay affiché - z-index:', window.getComputedStyle(backdrop).zIndex);
                    console.log('Menu z-index:', window.getComputedStyle(navbarCollapse).zIndex);
                }
            }
            
            // Fonction pour masquer l'overlay
            function hideBackdrop() {
                backdrop.classList.remove('show');
                document.body.style.overflow = '';
            }
            
            // Événements Bootstrap Collapse
            navbarCollapse.addEventListener('show.bs.collapse', function(e) {
                console.log('Menu en cours d\'ouverture...');
                showBackdrop();
            });
            
            navbarCollapse.addEventListener('shown.bs.collapse', function(e) {
                console.log('Menu complètement ouvert');
            });
            
            navbarCollapse.addEventListener('hide.bs.collapse', function(e) {
                console.log('Menu en cours de fermeture...');
                hideBackdrop();
            });
            
            navbarCollapse.addEventListener('hidden.bs.collapse', function(e) {
                console.log('Menu complètement fermé');
            });
            
            // Fermer le menu en cliquant sur l'overlay
            backdrop.addEventListener('click', function(e) {
                console.log('Clic sur overlay détecté');
                if (navbarCollapse.classList.contains('show')) {
                    // Utiliser Bootstrap Collapse API pour fermer proprement
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                        toggle: true
                    });
                }
            });
            
            // Fermer le menu en cliquant sur un lien de navigation
            const navLinks = navbarCollapse.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (window.innerWidth < 992 && navbarCollapse.classList.contains('show')) {
                        console.log('Lien cliqué - fermeture du menu');
                        setTimeout(() => {
                            const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                                toggle: false
                            });
                            bsCollapse.hide();
                        }, 150); // Petit délai pour une meilleure UX
                    }
                });
            });
            
            // Gestion du redimensionnement de la fenêtre
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth >= 992) {
                        // Mode desktop - masquer l'overlay
                        hideBackdrop();
                    }
                }, 250);
            });
            
            // Empêcher la propagation des clics sur le menu vers l'overlay
            navbarCollapse.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            
            console.log('✅ Script overlay backdrop initialisé avec succès');
        });
    </script>

    <!-- AOS (Animate On Scroll) Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialiser AOS avec des paramètres optimisés
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100,
            delay: 50
        });
    </script>
