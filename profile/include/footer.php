    <!-- Ultra Modern Footer -->
    <footer class="modern-footer">
        <div class="footer-content">
            <div class="container">
                <div class="footer-grid">
                    <!-- Brand Section -->
                    <div class="footer-col footer-brand-col">
                        <div class="brand-wrapper">
                            <img src="/public/images/logo.png" alt="MuseoLink" class="footer-logo">
                            <h3 class="brand-name">MuseoLink</h3>
                        </div>
                        <p class="brand-description">Découvrez et réservez vos billets pour les plus grands musées du monde. Une expérience culturelle inoubliable vous attend.</p>
                        <div class="footer-stats">
                            <div class="stat-item">
                                <i class="fas fa-museum"></i>
                                <div>
                                    <strong>500+</strong>
                                    <span>Musées</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <div>
                                    <strong>50K+</strong>
                                    <span>Visiteurs</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="footer-col">
                        <h4 class="footer-heading">Navigation</h4>
                        <ul class="footer-links">
                            <li><a href="index.php"><i class="fas fa-angle-right"></i> Accueil</a></li>
                            <li><a href="Explorer.php"><i class="fas fa-angle-right"></i> Explorer les musées</a></li>
                            <li><a href="about.php"><i class="fas fa-angle-right"></i> À propos</a></li>
                            <li><a href="contact.php"><i class="fas fa-angle-right"></i> Contact</a></li>
                        </ul>
                    </div>

                    <!-- Popular Museums -->
                    <div class="footer-col">
                        <h4 class="footer-heading">Musées Populaires</h4>
                        <ul class="footer-links">
                            <li><a href="#"><i class="fas fa-angle-right"></i> Le Louvre - Paris</a></li>
                            <li><a href="#"><i class="fas fa-angle-right"></i> MoMA - New York</a></li>
                            <li><a href="#"><i class="fas fa-angle-right"></i> British Museum - Londres</a></li>
                            <li><a href="#"><i class="fas fa-angle-right"></i> Musée d'Orsay - Paris</a></li>
                        </ul>
                    </div>

                    <!-- Newsletter & Social -->
                    <div class="footer-col">
                        <h4 class="footer-heading">Restez Connecté</h4>
                        <p class="newsletter-text">Recevez nos actualités et offres exclusives</p>
                        <form class="newsletter-form" onsubmit="return false;">
                            <input type="email" placeholder="Votre email" class="newsletter-input" required>
                            <button type="submit" class="newsletter-btn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                        <div class="footer-social">
                            <a href="#" class="social-btn facebook" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-btn instagram" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-btn twitter" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-btn linkedin" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Footer Bottom -->
                <div class="footer-bottom">
                    <div class="footer-bottom-left">
                        <p>&copy; 2025 <strong>MuseoLink</strong>. Tous droits réservés.</p>
                    </div>
                    <div class="footer-bottom-right">
                        <a href="#">Politique de confidentialité</a>
                        <span class="separator">•</span>
                        <a href="#">Conditions d'utilisation</a>
                        <span class="separator">•</span>
                        <a href="#">Cookies</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Top Button -->
        <button id="backToTop" class="back-to-top" aria-label="Retour en haut">
            <i class="fas fa-chevron-up"></i>
        </button>
    </footer>

    <!-- Footer JavaScript -->
    <script>
    (function() {
        'use strict';
        
        // Back to Top functionality
        const backToTopBtn = document.getElementById('backToTop');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });
        
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Newsletter animation
        const newsletterForm = document.querySelector('.newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('.newsletter-btn');
                btn.innerHTML = '<i class="fas fa-check"></i>';
                btn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    btn.style.background = '';
                    newsletterForm.reset();
                }, 2000);
            });
        }

        // Social icons hover effect
        const socialBtns = document.querySelectorAll('.social-btn');
        socialBtns.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) rotate(360deg)';
            });
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) rotate(0deg)';
            });
        });
    })();
    </script>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>
