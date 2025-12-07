<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : 'MuseoLink - Réservez vos billets de musées en ligne. Découvrez les plus grands musées du monde : Louvre, MoMA, British Museum et plus encore.'; ?>">
    <meta name="keywords" content="<?php echo isset($page_keywords) ? htmlspecialchars($page_keywords) : 'musée, réservation musée, billets musée, visite culturelle, art, histoire, exposition, Louvre, MoMA, British Museum'; ?>">
    <meta name="author" content="MuseoLink">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo 'https://museo.alwaysdata.net/' . basename($_SERVER['PHP_SELF']); ?>">
    
    <!-- Open Graph (Facebook, LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title . ' - MuseoLink' : 'MuseoLink - Réservation de musées en ligne'; ?>">
    <meta property="og:description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : 'Réservez vos billets de musées en ligne. Découvrez les plus grands musées du monde.'; ?>">
    <meta property="og:url" content="<?php echo 'https://museo.alwaysdata.net/' . basename($_SERVER['PHP_SELF']); ?>">
    <meta property="og:image" content="https://museo.alwaysdata.net/public/images/logo.png">
    <meta property="og:site_name" content="MuseoLink">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo isset($page_title) ? $page_title . ' - MuseoLink' : 'MuseoLink'; ?>">
    <meta name="twitter:description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : 'Réservez vos billets de musées en ligne'; ?>">
    <meta name="twitter:image" content="https://museo.alwaysdata.net/public/images/logo.png">
    
    <!-- Schema.org JSON-LD - WebSite -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "MuseoLink",
      "alternateName": "Museo Link",
      "description": "Plateforme de réservation de billets de musées en ligne",
      "url": "https://museo.alwaysdata.net",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "https://museo.alwaysdata.net/Explorer.php?search={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
    
    <!-- Schema.org JSON-LD - Organization -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "MuseoLink",
      "alternateName": "Museo Link",
      "url": "https://museo.alwaysdata.net",
      "logo": "https://museo.alwaysdata.net/public/images/logo.png",
      "description": "MuseoLink est la plateforme leader de réservation de billets de musées en ligne. Réservez simplement vos visites culturelles dans les plus grands musées du monde.",
      "foundingDate": "2025",
      "contactPoint": {
        "@type": "ContactPoint",
        "contactType": "Customer Service",
        "availableLanguage": ["French", "English"],
        "url": "https://museo.alwaysdata.net/contact.php"
      },
      "sameAs": [
        "https://www.facebook.com/MuseoLink",
        "https://www.instagram.com/museolink",
        "https://twitter.com/museolink",
        "https://www.linkedin.com/company/museolink"
      ]
    }
    </script>
    
    <link rel="icon" type="image/x-icon" href="public/images/logo.ico">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="css/styles_advanced.css" rel="stylesheet">
    <script src="js/styles_advanced.js" defer></script>
    <link href="css/hero-animations.css" rel="stylesheet">
    <style>
    /* Mobile menu fixes - ONLY FOR MOBILE */
    @media (max-width: 991px) {
        .navbar-toggler-advanced {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            filter: none !important;
            z-index: 10001 !important;
        }
        
        .navbar-nav-advanced {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            background: #ffffff !important;
            z-index: 10000 !important;
        }
        
        [data-theme="dark"] .navbar-nav-advanced {
            background: #1e293b !important;
        }
        
        /* Rendre les liens visibles */
        .navbar-nav-advanced .nav-link-advanced {
            color: #0f172a !important;
            background: rgba(241, 245, 249, 0.5);
        }
        
        .navbar-nav-advanced .nav-link-advanced:hover {
            background: rgba(201, 169, 97, 0.15) !important;
            color: #c9a961 !important;
        }
        
        .navbar-nav-advanced .nav-link-advanced i {
            color: #475569 !important;
            opacity: 1 !important;
        }
        
        [data-theme="dark"] .navbar-nav-advanced .nav-link-advanced {
            color: #f1f5f9 !important;
            background: rgba(51, 65, 85, 0.5);
        }
        
        [data-theme="dark"] .navbar-nav-advanced .nav-link-advanced:hover {
            background: rgba(201, 169, 97, 0.25) !important;
            color: #dfc480 !important;
        }
        
        [data-theme="dark"] .navbar-nav-advanced .nav-link-advanced i {
            color: #cbd5e1 !important;
        }
        
        .navbar-overlay {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            z-index: 9999 !important;
        }
    }
    
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
        background: linear-gradient(135deg, #c9a961, #dfc480) !important;
        color: white !important;
        transform: scale(1.05);
    }
    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border-color: #c9a961;
    }
    </style>
</head>
<body<?php echo isset($body_class) ? ' class="' . htmlspecialchars($body_class) . '"' : ''; ?>>
    <!-- Navigation -->
    <nav class="navbar-advanced">
        <div class="container">
            <a class="navbar-brand-advanced" href="index.php">
                <img src="public/images/logo.png" alt="MuseoLink" class="logo-icon">
            </a>
            
            <button class="navbar-toggler-advanced" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <?php
            $current = basename($_SERVER['PHP_SELF']);
            function nav_active($file) {
                global $current;
                return $current === $file ? 'active' : '';
            }
            ?>
            
            <ul class="navbar-nav-advanced">
                <li>
                    <a class="nav-link-advanced <?php echo nav_active('index.php'); ?>" href="index.php">
                        <i class="fas fa-home me-2"></i>Accueil
                    </a>
                </li>
                <li>
                    <a class="nav-link-advanced <?php echo nav_active('Explorer.php'); ?>" href="Explorer.php">
                        <i class="fas fa-compass me-2"></i>Explorer
                    </a>
                </li>
                <li>
                    <a class="nav-link-advanced <?php echo nav_active('reserver.php'); ?>" href="reserver.php">
                        <i class="fas fa-ticket-alt me-2"></i>Réserver
                    </a>
                </li>
                <li>
                    <a class="nav-link-advanced <?php echo nav_active('contact.php'); ?>" href="contact.php">
                        <i class="fas fa-envelope me-2"></i>Contact
                    </a>
                </li>
                <li>
                    <a class="nav-link-advanced <?php echo nav_active('login.php'); ?>" href="login.php">
                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                    </a>
                </li>
                <li>
                    <a class="nav-link-advanced <?php echo nav_active('register.php'); ?>" href="register.php">
                        <i class="fas fa-user-plus me-2"></i>Inscription
                    </a>
                </li>
                <li>
                    <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Mobile Menu Overlay -->
    <div class="navbar-overlay"></div>

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
