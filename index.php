<?php
require_once 'config/api_keys.php';
$page_title = "Accueil";
include 'includes/header.php';
?>

<!-- Hero Section - Image plein écran avec texte animé -->
<section class="hero-section">
    <div class="hero-content">
        <div class="hero-text-wrapper">
            <h1 class="hero-title animate-up">Découvrez le Monde des Musées</h1>
            <p class="hero-subtitle animate-up-delay-1">Réservez vos billets en quelques clics</p>
            <p class="hero-description animate-up-delay-2">
                Explorez les plus grands musées du monde, planifiez votre visite et vivez une expérience culturelle inoubliable
            </p>
            <div class="hero-buttons animate-up-delay-3">
                <a href="#search" class="btn btn-hero-primary">
                    <i class="fas fa-search me-2"></i>Rechercher un musée
                </a>
                <a href="Explorer.php" class="btn btn-hero-secondary">
                    <i class="fas fa-compass me-2"></i>Explorer
                </a>
            </div>
        </div>
    </div>
    <div class="scroll-indicator">
        <i class="fas fa-chevron-down"></i>
    </div>
</section>

<!-- Search Section Modernisée -->
<section id="search" class="py-5" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <span class="badge" style="background: linear-gradient(135deg, #1a4d7a 0%, #2c5f8d 100%); color: white; padding: 0.5rem 1.5rem; border-radius: 30px; font-size: 0.85rem; letter-spacing: 1px;">
                                <i class="fas fa-compass me-2"></i>PLANIFIEZ VOTRE VISITE
                            </span>
                            <h2 class="display-6 fw-bold mt-3" style="color: #1a4d7a;">Trouvez Votre Musée Idéal</h2>
                            <p class="text-muted">Recherchez parmi les plus prestigieux musées du monde</p>
                        </div>
                        <form id="searchForm" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="city" class="form-label fw-semibold" style="color: #1a4d7a;">
                                        <i class="fas fa-map-marker-alt me-2"></i>Destination
                                    </label>
                                    <input type="text" class="form-control form-control-lg" id="city" placeholder="Paris, Londres, New York..." style="border: 2px solid #e2e8f0; border-radius: 12px;" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="date" class="form-label fw-semibold" style="color: #1a4d7a;">
                                        <i class="fas fa-calendar-alt me-2"></i>Date de visite
                                    </label>
                                    <input type="date" class="form-control form-control-lg" id="date" style="border: 2px solid #e2e8f0; border-radius: 12px;" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="visitors" class="form-label fw-semibold" style="color: #1a4d7a;">
                                        <i class="fas fa-users me-2"></i>Visiteurs
                                    </label>
                                    <select class="form-select form-select-lg" id="visitors" style="border: 2px solid #e2e8f0; border-radius: 12px;">
                                        <option value="1">1 personne</option>
                                        <option value="2">2 personnes</option>
                                        <option value="3">3 personnes</option>
                                        <option value="4">4 personnes</option>
                                        <option value="5+">5+ personnes</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="type" class="form-label fw-semibold" style="color: #1a4d7a;">
                                        <i class="fas fa-palette me-2"></i>Catégorie
                                    </label>
                                    <select class="form-select form-select-lg" id="type" style="border: 2px solid #e2e8f0; border-radius: 12px;">
                                        <option value="">Tous les types</option>
                                        <option value="art">Art & Peinture</option>
                                        <option value="history">Histoire & Archéologie</option>
                                        <option value="science">Sciences & Technologie</option>
                                        <option value="natural">Histoire Naturelle</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-lg px-5 py-3" style="background: linear-gradient(135deg, #d4af37 0%, #f0c748 100%); color: #0f172a; border: none; border-radius: 50px; font-weight: 800; font-size: 1.1rem; transition: all 0.3s ease;">
                                    <i class="fas fa-search me-2"></i>Rechercher Maintenant
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Avantages -->
<section class="py-5" style="background: #0f172a;">
    <div class="container py-4">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="display-5 fw-bold text-white mb-3">Pourquoi Choisir MUSEO ?</h2>
                <p class="lead" style="color: #94a3b8;">L'excellence culturelle à portée de main</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="card h-100 border-0" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border-radius: 20px; transition: all 0.3s ease;">
                    <div class="card-body text-center p-4">
                        <div class="mb-3" style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, #d4af37 0%, #f0c748 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-bolt fa-2x" style="color: #0f172a;"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-3">Réservation Instantanée</h4>
                        <p style="color: #94a3b8;">Billets électroniques envoyés immédiatement par email. Aucune file d'attente.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border-radius: 20px; transition: all 0.3s ease;">
                    <div class="card-body text-center p-4">
                        <div class="mb-3" style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, #1a4d7a 0%, #2c5f8d 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-shield-alt fa-2x text-white"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-3">Paiement Sécurisé</h4>
                        <p style="color: #94a3b8;">Transactions cryptées et protection bancaire garantie à 100%.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border-radius: 20px; transition: all 0.3s ease;">
                    <div class="card-body text-center p-4">
                        <div class="mb-3" style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, #d4af37 0%, #f0c748 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-headset fa-2x" style="color: #0f172a;"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-3">Support 24/7</h4>
                        <p style="color: #94a3b8;">Assistance multilingue disponible à tout moment pour vous aider.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Museums Redesign -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container py-4">
        <div class="text-center mb-5" data-aos="fade-up">
            <div style="display: inline-block; background: linear-gradient(135deg, #1a4d7a, #2c5f8d); color: white; padding: 0.5rem 1.5rem; border-radius: 50px; font-weight: 600; font-size: 0.9rem; letter-spacing: 1px; margin-bottom: 1rem; box-shadow: 0 4px 15px rgba(26, 77, 122, 0.3);">
                <i class="fas fa-landmark me-2"></i>DESTINATIONS POPULAIRES
            </div>
            <h2 style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, #1a4d7a, #d4af37); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 1rem;">
                Musées Incontournables
            </h2>
            <p style="color: #64748b; font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
                Explorez les plus grands musées du monde et réservez votre visite en quelques clics
            </p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="museum-card" style="border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; background: white; border: none;">
                    <div style="position: relative; overflow: hidden; height: 280px;">
                        <img src="assets/images/louvre.jpg" alt="Musée du Louvre" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                        <div style="position: absolute; top: 15px; right: 15px; background: linear-gradient(135deg, #d4af37, #f0c748); color: white; padding: 0.4rem 0.9rem; border-radius: 50px; font-weight: 700; font-size: 0.85rem; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                            <i class="fas fa-star me-1"></i>4.9
                        </div>
                        <div class="museum-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to top, rgba(26, 77, 122, 0.95), transparent); display: flex; align-items: flex-end; padding: 1.5rem; opacity: 0; transition: opacity 0.3s ease;">
                            <a href="#" class="btn" style="background: white; color: #1a4d7a; border: none; padding: 0.7rem 1.8rem; border-radius: 50px; font-weight: 700; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                <i class="fas fa-ticket-alt me-2"></i>Réserver Maintenant
                            </a>
                        </div>
                    </div>
                    <div style="padding: 1.8rem;">
                        <h3 style="font-size: 1.4rem; font-weight: 700; color: #1e293b; margin-bottom: 0.8rem;">Musée du Louvre</h3>
                        <p style="color: #64748b; margin-bottom: 1rem; display: flex; align-items: center; font-size: 1rem;">
                            <i class="fas fa-map-marker-alt" style="color: #d4af37; margin-right: 0.5rem;"></i>
                            Paris, France
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 2px solid #f1f5f9;">
                            <div style="color: #fbbf24; font-size: 0.9rem;">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span style="color: #94a3b8; font-size: 0.9rem; font-weight: 600;">(4,521 avis)</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="museum-card" style="border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; background: white; border: none;">
                    <div style="position: relative; overflow: hidden; height: 280px;">
                        <img src="assets/images/british-museum.jpg" alt="British Museum" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                        <div style="position: absolute; top: 15px; right: 15px; background: linear-gradient(135deg, #d4af37, #f0c748); color: white; padding: 0.4rem 0.9rem; border-radius: 50px; font-weight: 700; font-size: 0.85rem; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                            <i class="fas fa-star me-1"></i>4.8
                        </div>
                        <div class="museum-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to top, rgba(26, 77, 122, 0.95), transparent); display: flex; align-items: flex-end; padding: 1.5rem; opacity: 0; transition: opacity 0.3s ease;">
                            <a href="#" class="btn" style="background: white; color: #1a4d7a; border: none; padding: 0.7rem 1.8rem; border-radius: 50px; font-weight: 700; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                <i class="fas fa-ticket-alt me-2"></i>Réserver Maintenant
                            </a>
                        </div>
                    </div>
                    <div style="padding: 1.8rem;">
                        <h3 style="font-size: 1.4rem; font-weight: 700; color: #1e293b; margin-bottom: 0.8rem;">British Museum</h3>
                        <p style="color: #64748b; margin-bottom: 1rem; display: flex; align-items: center; font-size: 1rem;">
                            <i class="fas fa-map-marker-alt" style="color: #d4af37; margin-right: 0.5rem;"></i>
                            Londres, Royaume-Uni
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 2px solid #f1f5f9;">
                            <div style="color: #fbbf24; font-size: 0.9rem;">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span style="color: #94a3b8; font-size: 0.9rem; font-weight: 600;">(3,842 avis)</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="museum-card" style="border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; background: white; border: none;">
                    <div style="position: relative; overflow: hidden; height: 280px;">
                        <img src="assets/images/Met-Museum-1.jpg" alt="MET Museum" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                        <div style="position: absolute; top: 15px; right: 15px; background: linear-gradient(135deg, #d4af37, #f0c748); color: white; padding: 0.4rem 0.9rem; border-radius: 50px; font-weight: 700; font-size: 0.85rem; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                            <i class="fas fa-star me-1"></i>4.9
                        </div>
                        <div class="museum-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to top, rgba(26, 77, 122, 0.95), transparent); display: flex; align-items: flex-end; padding: 1.5rem; opacity: 0; transition: opacity 0.3s ease;">
                            <a href="#" class="btn" style="background: white; color: #1a4d7a; border: none; padding: 0.7rem 1.8rem; border-radius: 50px; font-weight: 700; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                <i class="fas fa-ticket-alt me-2"></i>Réserver Maintenant
                            </a>
                        </div>
                    </div>
                    <div style="padding: 1.8rem;">
                        <h3 style="font-size: 1.4rem; font-weight: 700; color: #1e293b; margin-bottom: 0.8rem;">MET Museum</h3>
                        <p style="color: #64748b; margin-bottom: 1rem; display: flex; align-items: center; font-size: 1rem;">
                            <i class="fas fa-map-marker-alt" style="color: #d4af37; margin-right: 0.5rem;"></i>
                            New York, États-Unis
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 2px solid #f1f5f9;">
                            <div style="color: #fbbf24; font-size: 0.9rem;">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span style="color: #94a3b8; font-size: 0.9rem; font-weight: 600;">(5,289 avis)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Section Comment ça marche -->
<section class="py-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%220.5%22 fill=%22white%22 opacity=%220.05%22/></svg>') repeat; opacity: 0.5;"></div>
    <div class="container py-5" style="position: relative; z-index: 1;">
        <div class="text-center mb-5" data-aos="fade-up">
            <div style="display: inline-block; background: linear-gradient(135deg, #d4af37, #f0c748); color: #0f172a; padding: 0.5rem 1.5rem; border-radius: 50px; font-weight: 700; font-size: 0.9rem; letter-spacing: 1px; margin-bottom: 1rem; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);">
                <i class="fas fa-route me-2"></i>PROCESSUS SIMPLE
            </div>
            <h2 style="font-size: 2.5rem; font-weight: 800; color: white; margin-bottom: 1rem;">
                Comment Ça Marche ?
            </h2>
            <p style="color: #94a3b8; font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
                Réservez votre visite en seulement 3 étapes simples et rapides
            </p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="text-center" style="position: relative; padding: 2rem 1.5rem;">
                    <div style="width: 90px; height: 90px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #1a4d7a, #2c5f8d); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(26, 77, 122, 0.4); position: relative;">
                        <span style="font-size: 2.5rem; font-weight: 800; color: #d4af37;">1</span>
                        <div style="position: absolute; top: -5px; right: -5px; width: 35px; height: 35px; background: linear-gradient(135deg, #d4af37, #f0c748); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-search" style="color: #0f172a; font-size: 0.9rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3" style="color: white; font-size: 1.3rem;">Recherchez</h4>
                    <p style="color: #94a3b8; line-height: 1.8;">
                        Explorez notre catalogue de musées par destination, type d'exposition ou date de visite.
                    </p>
                </div>
                <div style="position: absolute; top: 50%; right: -20px; width: 40px; height: 2px; background: linear-gradient(to right, #d4af37, transparent); display: none;" class="d-md-block"></div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center" style="position: relative; padding: 2rem 1.5rem;">
                    <div style="width: 90px; height: 90px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #1a4d7a, #2c5f8d); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(26, 77, 122, 0.4); position: relative;">
                        <span style="font-size: 2.5rem; font-weight: 800; color: #d4af37;">2</span>
                        <div style="position: absolute; top: -5px; right: -5px; width: 35px; height: 35px; background: linear-gradient(135deg, #d4af37, #f0c748); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-check" style="color: #0f172a; font-size: 0.9rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3" style="color: white; font-size: 1.3rem;">Réservez</h4>
                    <p style="color: #94a3b8; line-height: 1.8;">
                        Sélectionnez votre créneau horaire et le nombre de billets. Confirmation instantanée garantie.
                    </p>
                </div>
                <div style="position: absolute; top: 50%; right: -20px; width: 40px; height: 2px; background: linear-gradient(to right, #d4af37, transparent); display: none;" class="d-md-block"></div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center" style="position: relative; padding: 2rem 1.5rem;">
                    <div style="width: 90px; height: 90px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #1a4d7a, #2c5f8d); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(26, 77, 122, 0.4); position: relative;">
                        <span style="font-size: 2.5rem; font-weight: 800; color: #d4af37;">3</span>
                        <div style="position: absolute; top: -5px; right: -5px; width: 35px; height: 35px; background: linear-gradient(135deg, #d4af37, #f0c748); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-ticket-alt" style="color: #0f172a; font-size: 0.9rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3" style="color: white; font-size: 1.3rem;">Profitez</h4>
                    <p style="color: #94a3b8; line-height: 1.8;">
                        Recevez vos billets par email et présentez-les à l'entrée. Pas de file d'attente, visitez directement !
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Témoignages -->
<section class="py-5" style="background: white;">
    <div class="container py-4">
        <div class="text-center mb-5" data-aos="fade-up">
            <div style="display: inline-block; background: linear-gradient(135deg, #1a4d7a, #2c5f8d); color: white; padding: 0.5rem 1.5rem; border-radius: 50px; font-weight: 600; font-size: 0.9rem; letter-spacing: 1px; margin-bottom: 1rem; box-shadow: 0 4px 15px rgba(26, 77, 122, 0.3);">
                <i class="fas fa-comments me-2"></i>TÉMOIGNAGES
            </div>
            <h2 style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, #1a4d7a, #d4af37); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 1rem;">
                Ce Que Disent Nos Clients
            </h2>
            <p style="color: #64748b; font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
                Des milliers de visiteurs satisfaits partagent leur expérience
            </p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div style="background: #f8fafc; border-radius: 20px; padding: 2rem; position: relative; border: 2px solid #e2e8f0; transition: all 0.3s ease; height: 100%;" class="testimonial-card">
                    <div style="position: absolute; top: -20px; left: 30px; width: 40px; height: 40px; background: linear-gradient(135deg, #d4af37, #f0c748); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);">
                        <i class="fas fa-quote-left" style="color: white; font-size: 1rem;"></i>
                    </div>
                    <div style="margin-top: 1.5rem;">
                        <div style="color: #fbbf24; margin-bottom: 1rem; font-size: 1.1rem;">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p style="color: #475569; line-height: 1.8; margin-bottom: 1.5rem; font-size: 1rem;">
                            "Une expérience exceptionnelle ! La réservation était simple et rapide. J'ai pu visiter le Louvre sans faire la queue. Service impeccable !"
                        </p>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #1a4d7a, #2c5f8d); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem;">
                                MA
                            </div>
                            <div>
                                <h5 style="margin: 0; font-weight: 700; color: #1e293b; font-size: 1rem;">Marie Dubois</h5>
                                <p style="margin: 0; color: #94a3b8; font-size: 0.9rem;">Paris, France</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div style="background: #f8fafc; border-radius: 20px; padding: 2rem; position: relative; border: 2px solid #e2e8f0; transition: all 0.3s ease; height: 100%;" class="testimonial-card">
                    <div style="position: absolute; top: -20px; left: 30px; width: 40px; height: 40px; background: linear-gradient(135deg, #d4af37, #f0c748); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);">
                        <i class="fas fa-quote-left" style="color: white; font-size: 1rem;"></i>
                    </div>
                    <div style="margin-top: 1.5rem;">
                        <div style="color: #fbbf24; margin-bottom: 1rem; font-size: 1.1rem;">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p style="color: #475569; line-height: 1.8; margin-bottom: 1.5rem; font-size: 1rem;">
                            "Parfait pour organiser mon voyage à Londres. J'ai réservé plusieurs musées en une fois. Interface intuitive et service client très réactif."
                        </p>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #1a4d7a, #2c5f8d); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem;">
                                JM
                            </div>
                            <div>
                                <h5 style="margin: 0; font-weight: 700; color: #1e293b; font-size: 1rem;">Jean Martin</h5>
                                <p style="margin: 0; color: #94a3b8; font-size: 0.9rem;">Lyon, France</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div style="background: #f8fafc; border-radius: 20px; padding: 2rem; position: relative; border: 2px solid #e2e8f0; transition: all 0.3s ease; height: 100%;" class="testimonial-card">
                    <div style="position: absolute; top: -20px; left: 30px; width: 40px; height: 40px; background: linear-gradient(135deg, #d4af37, #f0c748); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);">
                        <i class="fas fa-quote-left" style="color: white; font-size: 1rem;"></i>
                    </div>
                    <div style="margin-top: 1.5rem;">
                        <div style="color: #fbbf24; margin-bottom: 1rem; font-size: 1.1rem;">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p style="color: #475569; line-height: 1.8; margin-bottom: 1.5rem; font-size: 1rem;">
                            "Application géniale ! J'ai découvert des musées incroyables que je n'aurais jamais trouvés autrement. Les prix sont très compétitifs."
                        </p>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #1a4d7a, #2c5f8d); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem;">
                                SL
                            </div>
                            <div>
                                <h5 style="margin: 0; font-weight: 700; color: #1e293b; font-size: 1rem;">Sophie Laurent</h5>
                                <p style="margin: 0; color: #94a3b8; font-size: 0.9rem;">Marseille, France</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Styles déplacés dans le head -->

<!-- CTA Final Section -->
<section class="py-5" style="background: linear-gradient(135deg, #1a4d7a 0%, #2c5f8d 100%); position: relative; overflow: hidden;">
    <!-- Bande intermédiaire supprimée -->
    <div class="container py-5 text-center" style="position: relative; z-index: 1;" data-aos="fade-up">
        <div style="max-width: 700px; margin: 0 auto;">
            <h2 style="font-size: 2.5rem; font-weight: 800; color: white; margin-bottom: 1.5rem;">
                Prêt à Explorer le Monde des Musées ?
            </h2>
            <p style="color: #cbd5e1; font-size: 1.2rem; margin-bottom: 2.5rem; line-height: 1.8;">
                Rejoignez des milliers de visiteurs qui font confiance à notre plateforme pour découvrir les plus beaux musées du monde.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="#search" class="btn" style="background: linear-gradient(135deg, #d4af37, #f0c748); color: #0f172a; border: none; padding: 1rem 2.5rem; border-radius: 50px; font-weight: 700; font-size: 1.1rem; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3); display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-search"></i>
                    Rechercher un Musée
                </a>
                <a href="#" class="btn" style="background: transparent; color: white; border: 2px solid white; padding: 1rem 2.5rem; border-radius: 50px; font-weight: 700; font-size: 1.1rem; text-decoration: none; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-info-circle"></i>
                    En Savoir Plus
                </a>
            </div>
        </div>
    </div>
</section>


<script src="js/page-scripts.js"></script>

<?php include 'includes/footer.php'; ?>