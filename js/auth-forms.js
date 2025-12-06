/**
 * AUTH FORMS - JavaScript pour les pages de connexion, inscription et réinitialisation
 */

// Initialiser AOS animations
document.addEventListener('DOMContentLoaded', function() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true
        });
    }
});

/**
 * Toggle password visibility
 */
function initPasswordToggle() {
    const toggleButtons = document.querySelectorAll('.password-toggle');
    
    toggleButtons.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const inputGroup = this.parentElement;
            const passwordInput = inputGroup.querySelector('.form-control');
            
            if (passwordInput) {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            }
        });
    });
}

/**
 * Validation email en temps réel
 */
function initEmailValidation() {
    const emailInputs = document.querySelectorAll('input[type="email"]');
    
    emailInputs.forEach(input => {
        input.addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (this.value) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    });
}

/**
 * Password strength checker
 */
function initPasswordStrength() {
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    if (!passwordInput || !strengthBar || !strengthText) return;
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        let text = '';
        let color = '';
        
        // Critères de force
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        // Définir le texte et la couleur selon la force
        switch(strength) {
            case 0:
            case 1:
                text = 'Faible';
                color = '#ef4444';
                break;
            case 2:
                text = 'Moyen';
                color = '#f59e0b';
                break;
            case 3:
                text = 'Bon';
                color = '#10b981';
                break;
            case 4:
                text = 'Excellent';
                color = '#059669';
                break;
        }
        
        // Mettre à jour la barre de progression
        const width = (strength / 4) * 100;
        strengthBar.style.width = width + '%';
        strengthBar.style.backgroundColor = color;
        strengthText.textContent = password.length > 0 ? 'Force : ' + text : '';
        strengthText.style.color = color;
    });
}

/**
 * Validation de confirmation de mot de passe
 */
function initPasswordConfirmation() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    
    if (!passwordInput || !confirmInput) return;
    
    confirmInput.addEventListener('input', function() {
        if (this.value && this.value !== passwordInput.value) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else if (this.value) {
            this.classList.add('is-valid');
            this.classList.remove('is-invalid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });
    
    // Vérifier aussi quand le mot de passe principal change
    passwordInput.addEventListener('input', function() {
        if (confirmInput.value && confirmInput.value !== this.value) {
            confirmInput.classList.add('is-invalid');
            confirmInput.classList.remove('is-valid');
        } else if (confirmInput.value) {
            confirmInput.classList.add('is-valid');
            confirmInput.classList.remove('is-invalid');
        }
    });
}

/**
 * Animation des inputs au focus
 */
function initInputAnimations() {
    const inputs = document.querySelectorAll('.form-control');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            const inputGroup = this.closest('.input-group') || this;
            inputGroup.style.transform = 'scale(1.02)';
            inputGroup.style.transition = 'transform 0.2s ease';
        });
        
        input.addEventListener('blur', function() {
            const inputGroup = this.closest('.input-group') || this;
            inputGroup.style.transform = 'scale(1)';
        });
    });
}

/**
 * Animation de soumission du formulaire
 */
function initFormSubmitAnimation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Vérifier la validité du formulaire avant de désactiver le bouton
            if (!form.checkValidity()) {
                return; // Laisser la validation HTML5 faire son travail
            }
            
            const submitBtn = form.querySelector('.btn-auth');
            
            if (submitBtn && !submitBtn.disabled) {
                const originalText = submitBtn.innerHTML;
                const loadingText = submitBtn.getAttribute('data-loading') || '<i class="fas fa-spinner fa-spin me-2"></i>Chargement...';
                
                submitBtn.innerHTML = loadingText;
                submitBtn.disabled = true;
                
                // Stocker le texte original pour pouvoir le restaurer
                submitBtn.dataset.originalText = originalText;
            }
        });
    });
}

/**
 * Auto-focus sur le premier champ
 */
function initAutoFocus() {
    const firstInput = document.querySelector('.form-control');
    
    if (firstInput && !firstInput.value) {
        setTimeout(() => {
            firstInput.focus();
        }, 500);
    }
}

/**
 * Animation des alertes
 */
function initAlertAnimations() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        // Auto-hide après 5 secondes pour les messages de succès
        if (alert.classList.contains('alert-success')) {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        }
    });
}

/**
 * Validation en temps réel des champs requis
 */
function initRequiredFieldsValidation() {
    const requiredInputs = document.querySelectorAll('.form-control[required]');
    
    requiredInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim() !== '') {
                this.classList.remove('is-invalid');
            }
        });
    });
}

/**
 * Initialisation de tous les modules
 */
function initAuthForms() {
    initPasswordToggle();
    initEmailValidation();
    initPasswordStrength();
    initPasswordConfirmation();
    initInputAnimations();
    initFormSubmitAnimation();
    initAutoFocus();
    initAlertAnimations();
    initRequiredFieldsValidation();
}

// Lancer l'initialisation au chargement du DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAuthForms);
} else {
    initAuthForms();
}
