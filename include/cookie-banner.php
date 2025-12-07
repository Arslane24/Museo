<?php
// Vérifier si l'utilisateur a fait un choix
$showBanner = true;

// Ne pas afficher uniquement si l'utilisateur a ACCEPTÉ
if (!empty($_COOKIE['cookie_consent']) && $_COOKIE['cookie_consent'] === 'accepted') {
    $showBanner = false;
}

// Si le cookie est "declined", on affiche quand même la bannière à la prochaine visite
// Car le cookie de session "declined" aura expiré

// Afficher la bannière si nécessaire
if ($showBanner): ?>
<!-- Cookie Consent Banner -->
<div id="cookieConsent" style="position: fixed; bottom: 0; left: 0; right: 0; background: linear-gradient(135deg, #1a4d7a 0%, #0f172a 100%); padding: 1.5rem 0; box-shadow: 0 -10px 40px rgba(0,0,0,0.3); z-index: 9999; border-top: 3px solid #c9a961;">
    <div class="container">
        <form method="POST" action="">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #c9a961, #dfc480); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-cookie-bite" style="color: #0f172a; font-size: 1.3rem;"></i>
                        </div>
                        <div>
                            <h5 style="color: white; margin: 0 0 0.5rem 0; font-weight: 700; font-size: 1.1rem;">🍪 Nous utilisons des cookies</h5>
                            <p style="color: #cbd5e1; margin: 0; font-size: 0.95rem; line-height: 1.6;">
                                Nous utilisons des cookies pour améliorer votre expérience de navigation, analyser le trafic du site et personnaliser le contenu. En cliquant sur "Accepter", vous consentez à notre utilisation des cookies.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div style="display: flex; gap: 0.75rem; justify-content: lg-end; flex-wrap: wrap;">
                        <button type="submit" name="accept_cookies" style="background: linear-gradient(135deg, #c9a961, #dfc480); color: #0f172a; border: none; padding: 0.75rem 1.8rem; border-radius: 50px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(201, 169, 97, 0.3); font-size: 0.95rem;">
                            <i class="fas fa-check me-2"></i>Accepter
                        </button>
                        <button type="submit" name="decline_cookies" style="background: transparent; color: white; border: 2px solid white; padding: 0.75rem 1.8rem; border-radius: 50px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; font-size: 0.95rem;">
                            <i class="fas fa-times me-2"></i>Refuser
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Animation d'apparition et gestion du masquage pour la session en cours
document.addEventListener('DOMContentLoaded', function() {
    const banner = document.getElementById('cookieConsent');
    if (banner) {
        // Vérifier si l'utilisateur a cliqué sur refuser dans cette session
        const declined = sessionStorage.getItem('cookie_declined');
        
        if (!declined) {
            // Afficher la bannière avec animation
            setTimeout(function() {
                banner.style.transform = 'translateY(0)';
                banner.style.transition = 'transform 0.5s ease';
            }, 500);
        } else {
            // Masquer la bannière si l'utilisateur a déjà refusé dans cette session
            banner.style.display = 'none';
        }
    }
});

// Fonction pour masquer la bannière après refus
function hideBannerAfterDecline() {
    const banner = document.getElementById('cookieConsent');
    if (banner) {
        sessionStorage.setItem('cookie_declined', 'true');
        banner.style.transform = 'translateY(100%)';
        setTimeout(function() {
            banner.style.display = 'none';
        }, 500);
    }
}

// Écouter la soumission du formulaire pour gérer le refus
const form = document.querySelector('#cookieConsent form');
if (form) {
    form.addEventListener('submit', function(e) {
        if (e.submitter && e.submitter.name === 'decline_cookies') {
            hideBannerAfterDecline();
        }
    });
}
</script>

<style>
#cookieConsent {
    transform: translateY(100%);
}
</style>
<?php endif; ?>
