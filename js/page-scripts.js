// Animation des statistiques au scroll
const observerOptions = {
    threshold: 0.5,
    rootMargin: '0px'
};

const animateCounter = (element) => {
    const target = parseInt(element.getAttribute('data-count'));
    const duration = 2000;
    const increment = target / (duration / 16);
    let current = 0;

    const updateCounter = () => {
        current += increment;
        if (current < target) {
            element.textContent = Math.floor(current).toLocaleString();
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target.toLocaleString();
        }
    };

    updateCounter();
};

const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counters = entry.target.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                if (counter.textContent === '0') {
                    animateCounter(counter);
                }
            });
        }
    });
}, observerOptions);

const statsSection = document.querySelector('.stats-section');
if (statsSection) {
    statsObserver.observe(statsSection);
}

// Smooth scroll pour le bouton scroll indicator
document.querySelector('.scroll-indicator')?.addEventListener('click', () => {
    document.querySelector('#search').scrollIntoView({ behavior: 'smooth' });
});

// Gestion du formulaire de recherche
const searchForm = document.getElementById('searchForm');
console.log('🔍 Formulaire de recherche trouvé:', !!searchForm);

if (searchForm) {
    // Soumission du formulaire - Rediriger vers Explorer.php avec les paramètres
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        console.log('📤 Formulaire soumis');
        
        // Récupérer les valeurs du formulaire
        const museumName = document.getElementById('museumName').value.trim();
        const category = document.getElementById('category').value;
        const country = document.getElementById('country').value.trim();
        
        console.log('📋 Valeurs:', { museumName, category, country });
        
        // Construire l'URL de redirection vers Explorer.php
        const params = new URLSearchParams();
        if (museumName) params.append('search', museumName);
        if (category && category !== 'all') params.append('category', category);
        if (country) params.append('country', country);
        
        // Rediriger vers Explorer.php avec les paramètres de recherche
        window.location.href = `Explorer.php?${params.toString()}`;
    });
}

// Animation des cards au scroll
const cardObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.fade-in-up').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = 'all 0.6s ease';
    cardObserver.observe(card);
});
