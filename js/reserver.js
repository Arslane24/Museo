/**
 * RESERVER PAGE - JavaScript pour la page de réservation
 */

// Initialize AOS animations
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
        
        // Form submission handling
        form.addEventListener('submit', function(e) {
            // You can add custom validation or confirmation here
            const museum = document.querySelector('input[name="museum"]:checked');
            if (!museum) {
                e.preventDefault();
                alert('Veuillez sélectionner un musée');
                return false;
            }
        });
    }
});
