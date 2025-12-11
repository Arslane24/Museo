<?php
$pageTitle = 'Mes Réservations - MUSEO';
require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/private_nav.php';

// Connexion à la base de données
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

// Charger les managers
require_once __DIR__ . '/src/models/MuseumManager.php';
require_once __DIR__ . '/src/models/StatsManager.php';

$museumManager = new MuseumManager($pdo);
$statsManager = new StatsManager($pdo);
$userId = $_SESSION['user_id'];

// Récupérer les réservations depuis la base de données
$bookings = $museumManager->getUserBookings($userId);

// Transformer les données pour le format attendu
$reservations = [];
foreach ($bookings as $booking) {
    $location = $booking['city'] . ', ' . $booking['country'];
    
    // Déterminer le statut basé sur la date
    $status = strtotime($booking['visit_date']) >= strtotime('today') ? 'confirmed' : 'completed';
    
    $reservations[] = [
        'id' => $booking['id'],
        'museum' => $booking['museum_name'],
        'location' => $location,
        'date' => $booking['visit_date'],
        'time' => substr($booking['visit_time'], 0, 5),
        'adults' => $booking['people_count'] ?? 1,
        'children' => 0,
        'price' => ($booking['people_count'] ?? 1) * 15,
        'status' => $status,
        'booking_ref' => 'RES-' . $booking['id'],
        'image' => $booking['image_url'] ?? 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=400&h=250&fit=crop'
    ];
}

// Filtrer par statut
$filter = $_GET['filter'] ?? 'all';
$filteredReservations = $reservations;
if ($filter !== 'all') {
    $filteredReservations = array_filter($reservations, function($res) use ($filter) {
        return $res['status'] === $filter;
    });
}

// Statistiques depuis la base de données
$stats = $statsManager->getReservationStats($userId);

// Grouper par mois
$reservationsByMonth = [];
foreach ($reservations as $res) {
    $month = date('Y-m', strtotime($res['date']));
    if (!isset($reservationsByMonth[$month])) {
        $reservationsByMonth[$month] = [];
    }
    $reservationsByMonth[$month][] = $res;
}
ksort($reservationsByMonth);
?>

<main id="main-content" role="main">
<style>
.filter-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    background: rgba(255, 255, 255, 0.05);
    padding: 0.5rem;
    border-radius: var(--border-radius-lg);
}

.filter-tab {
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    background: transparent;
    border: none;
    color: var(--gray-600);
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-tab:hover {
    background: rgba(255, 255, 255, 0.05);
    color: var(--gray-700);
}

.filter-tab.active {
    background: var(--gradient-primary);
    color: white;
}

.filter-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 700;
}

.reservations-grid {
    display: grid;
    gap: 1.5rem;
}

.reservation-card {
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-xl);
    overflow: hidden;
    transition: var(--transition);
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 1.5rem;
}

.reservation-card:hover {
    border-color: var(--secondary-color);
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(212, 175, 55, 0.2);
}

.reservation-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.reservation-content {
    padding: 1.5rem 1.5rem 1.5rem 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.reservation-header {
    margin-bottom: 1rem;
}

.reservation-title {
    color: var(--secondary-color);
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.reservation-location {
    color: var(--gray-600);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.reservation-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.detail-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(212, 175, 55, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--secondary-color);
}

.detail-info {
    flex: 1;
}

.detail-label {
    color: var(--gray-600);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    color: var(--gray-700);
    font-weight: 600;
}

.reservation-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.booking-ref {
    color: var(--gray-600);
    font-size: 0.875rem;
    font-family: monospace;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.status-confirmed {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-pending {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.status-completed {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.status-cancelled {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.reservation-actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: transparent;
    color: var(--gray-700);
    cursor: pointer;
    transition: var(--transition);
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: var(--secondary-color);
    color: var(--secondary-color);
}

.action-btn.danger:hover {
    background: rgba(239, 68, 68, 0.1);
    border-color: #ef4444;
    color: #ef4444;
}

/* Modal styles */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-overlay.active {
    display: flex;
}

.modal-content {
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-xl);
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    padding: 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    color: var(--secondary-color);
    font-size: 1.5rem;
    margin: 0;
}

.modal-close {
    background: transparent;
    border: none;
    color: var(--gray-600);
    font-size: 1.5rem;
    cursor: pointer;
    transition: var(--transition);
}

.modal-close:hover {
    color: var(--secondary-color);
}

.modal-body {
    padding: 2rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.detail-label {
    color: var(--gray-600);
    font-weight: 500;
}

.detail-value {
    color: var(--gray-700);
    text-align: right;
}

.ticket-preview {
    background: white;
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    margin-top: 1rem;
    color: #1a1a2e;
}

.ticket-header {
    text-align: center;
    border-bottom: 2px dashed #d4af37;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
}

.ticket-header h3 {
    color: #1a1a2e;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.ticket-qr {
    text-align: center;
    padding: 1rem 0;
}

.ticket-qr-placeholder {
    width: 150px;
    height: 150px;
    margin: 0 auto;
    background: #f5f5f5;
    border: 2px solid #d4af37;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    color: #666;
}

.ticket-info {
    margin-top: 1rem;
}

.ticket-info p {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
    margin: 0;
}

.ticket-info strong {
    color: #d4af37;
}

.status-badge.cancelled,
.btn-cancel {
    border-color: #ef4444;
    color: #ef4444;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--gray-600);
}

.empty-state i {
    font-size: 5rem;
    opacity: 0.3;
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: var(--gray-700);
    margin-bottom: 1rem;
}

.price-tag {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--secondary-color);
}

/* Mini stat cards */
.stat-card {
    background: var(--gradient-card);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius-lg);
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-3px);
    border-color: rgba(212, 175, 55, 0.3);
}

.stat-card .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.stat-card .stat-info {
    flex: 1;
}

.stat-card .stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--secondary-color);
    line-height: 1;
}

.stat-card .stat-label {
    font-size: 0.75rem;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.25rem;
}

@media (max-width: 968px) {
    .reservation-card {
        grid-template-columns: 1fr;
    }
    
    .reservation-image {
        height: 200px;
    }
    
    .reservation-content {
        padding: 1.5rem;
    }
    
    .filter-tabs {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
}

@media (max-width: 576px) {
    .reservation-details {
        grid-template-columns: 1fr;
    }
    
    .reservation-title {
        font-size: 1.25rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
}
</style>

<div class="container py-4">
    <!-- Statistiques en haut -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['total_tickets'] ?></div>
                    <div class="stat-label">Billets totaux</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-euro-sign"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= number_format($stats['total_spent'], 0) ?>€</div>
                    <div class="stat-label">Dépenses totales</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= number_format($stats['avg_price'], 0) ?>€</div>
                    <div class="stat-label">Prix moyen</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['confirmed'] ?></div>
                    <div class="stat-label">Confirmées</div>
                </div>
            </div>
        </div>
    </div>

    <div class="private-card">
        <div class="section-header">
            <h1 class="section-title">
                <i class="fas fa-list"></i>
                Toutes mes réservations
            </h1>
            <a href="reserver.php" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nouvelle réservation
            </a>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="?filter=all" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
                <span>Toutes</span>
                <span class="filter-count"><?= $stats['all'] ?></span>
            </a>
            <a href="?filter=confirmed" class="filter-tab <?= $filter === 'confirmed' ? 'active' : '' ?>">
                <span>Confirmées</span>
                <span class="filter-count"><?= $stats['confirmed'] ?></span>
            </a>
            <a href="?filter=pending" class="filter-tab <?= $filter === 'pending' ? 'active' : '' ?>">
                <span>En attente</span>
                <span class="filter-count"><?= $stats['pending'] ?></span>
            </a>
            <a href="?filter=completed" class="filter-tab <?= $filter === 'completed' ? 'active' : '' ?>">
                <span>Complétées</span>
                <span class="filter-count"><?= $stats['completed'] ?></span>
            </a>
            <a href="?filter=cancelled" class="filter-tab <?= $filter === 'cancelled' ? 'active' : '' ?>">
                <span>Annulées</span>
                <span class="filter-count"><?= $stats['cancelled'] ?></span>
            </a>
        </div>

        <!-- Reservations Grid -->
        <?php if (empty($filteredReservations)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>Aucune réservation trouvée</h3>
                <p>Vous n'avez pas encore de réservation <?= $filter !== 'all' ? 'dans cette catégorie' : '' ?>.</p>
                <a href="reserver.php" class="btn btn-primary" style="margin-top: 1.5rem;">
                    <i class="fas fa-plus"></i>
                    Réserver une visite
                </a>
            </div>
        <?php else: ?>
            <div class="reservations-grid">
                <?php foreach ($filteredReservations as $res): ?>
                    <div class="reservation-card">
                        <img src="<?= htmlspecialchars($res['image']) ?>" alt="<?= htmlspecialchars($res['museum']) ?>" class="reservation-image">
                        
                        <div class="reservation-content">
                            <div>
                                <div class="reservation-header">
                                    <h3 class="reservation-title"><?= htmlspecialchars($res['museum']) ?></h3>
                                    <p class="reservation-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= htmlspecialchars($res['location']) ?>
                                    </p>
                                </div>

                                <div class="reservation-details">
                                    <div class="detail-item">
                                        <div class="detail-icon">
                                            <i class="fas fa-calendar"></i>
                                        </div>
                                        <div class="detail-info">
                                            <div class="detail-label">Date</div>
                                            <div class="detail-value"><?= date('d/m/Y', strtotime($res['date'])) ?></div>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-icon">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="detail-info">
                                            <div class="detail-label">Heure</div>
                                            <div class="detail-value"><?= $res['time'] ?></div>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="detail-info">
                                            <div class="detail-label">Visiteurs</div>
                                            <div class="detail-value"><?= $res['adults'] ?> adulte(s), <?= $res['children'] ?> enfant(s)</div>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <div class="detail-icon">
                                            <i class="fas fa-euro-sign"></i>
                                        </div>
                                        <div class="detail-info">
                                            <div class="detail-label">Prix</div>
                                            <div class="detail-value price-tag"><?= number_format($res['price'], 2) ?>€</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="reservation-footer">
                                <div>
                                    <div class="booking-ref">Réf: <?= htmlspecialchars($res['booking_ref']) ?></div>
                                    <div class="status-badge status-<?= $res['status'] ?>" style="margin-top: 0.5rem;">
                                        <i class="fas fa-<?= $res['status'] === 'confirmed' ? 'check-circle' : ($res['status'] === 'pending' ? 'clock' : ($res['status'] === 'completed' ? 'check-double' : 'times-circle')) ?>"></i>
                                        <?= $res['status'] === 'confirmed' ? 'Confirmée' : ($res['status'] === 'pending' ? 'En attente' : ($res['status'] === 'completed' ? 'Complétée' : 'Annulée')) ?>
                                    </div>
                                </div>

                                <div class="reservation-actions">
                                    <?php if ($res['status'] === 'confirmed' || $res['status'] === 'pending'): ?>
                                        <button class="action-btn" onclick="showDetails(<?= htmlspecialchars(json_encode($res), ENT_QUOTES) ?>)">
                                            <i class="fas fa-eye"></i>
                                            Détails
                                        </button>
                                        <button class="action-btn" onclick="downloadTicket(<?= htmlspecialchars(json_encode($res), ENT_QUOTES) ?>)">
                                            <i class="fas fa-download"></i>
                                            Billet
                                        </button>
                                        <button class="action-btn danger" onclick="cancelReservation(<?= $res['id'] ?>, <?= htmlspecialchars(json_encode($res['museum']), ENT_QUOTES) ?>)">
                                            <i class="fas fa-times"></i>
                                            Annuler
                                        </button>
                                    <?php elseif ($res['status'] === 'completed'): ?>
                                        <button class="action-btn" onclick="showDetails(<?= htmlspecialchars(json_encode($res), ENT_QUOTES) ?>)">
                                            <i class="fas fa-eye"></i>
                                            Détails
                                        </button>
                                        <button class="action-btn">
                                            <i class="fas fa-star"></i>
                                            Laisser un avis
                                        </button>
                                        <button class="action-btn">
                                            <i class="fas fa-redo"></i>
                                            Réserver à nouveau
                                        </button>
                                    <?php else: ?>
                                        <button class="action-btn">
                                            <i class="fas fa-info-circle"></i>
                                            Détails
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal pour les détails de réservation -->
<div class="modal-overlay" id="detailsModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-info-circle me-2"></i>Détails de la réservation</h2>
            <button class="modal-close" onclick="closeModal('detailsModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="ticket-preview">
                <div class="ticket-header">
                    <h3 id="detailMuseum">Musée</h3>
                    <p id="detailLocation" style="color: #666; margin: 0;"></p>
                </div>
                <div class="ticket-info">
                    <p><strong>Date de visite :</strong> <span id="detailDate"></span></p>
                    <p><strong>Heure :</strong> <span id="detailTime"></span></p>
                    <p><strong>Nombre de personnes :</strong> <span id="detailPeople"></span></p>
                    <p><strong>Prix total :</strong> <span id="detailPrice"></span></p>
                    <p><strong>Référence :</strong> <span id="detailRef"></span></p>
                    <p><strong>Statut :</strong> <span id="detailStatus"></span></p>
                </div>
            </div>
            <div class="reservation-actions" style="margin-top: 1.5rem; justify-content: center;">
                <button class="action-btn" onclick="printTicket()">
                    <i class="fas fa-print"></i>
                    Imprimer
                </button>
                <button class="action-btn" onclick="closeModal('detailsModal')">
                    <i class="fas fa-times"></i>
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
// Fonction pour afficher les détails
function showDetails(reservation) {
    document.getElementById('detailMuseum').textContent = reservation.museum;
    document.getElementById('detailLocation').textContent = reservation.location;
    document.getElementById('detailDate').textContent = new Date(reservation.date).toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    document.getElementById('detailTime').textContent = reservation.time;
    document.getElementById('detailPeople').textContent = reservation.adults + ' personne(s)';
    document.getElementById('detailPrice').textContent = reservation.price.toFixed(2) + ' €';
    document.getElementById('detailRef').textContent = reservation.booking_ref;
    
    const statusText = reservation.status === 'confirmed' ? 'Confirmée' : 
                      reservation.status === 'pending' ? 'En attente' : 
                      reservation.status === 'completed' ? 'Complétée' : 'Annulée';
    document.getElementById('detailStatus').textContent = statusText;
    
    document.getElementById('detailsModal').classList.add('active');
}

// Fonction pour fermer le modal
function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Fermer le modal en cliquant sur l'overlay
document.getElementById('detailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal('detailsModal');
    }
});

// Fonction pour imprimer le ticket
function printTicket() {
    window.print();
}

// Fonction pour télécharger le billet en PDF
function downloadTicket(reservation) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // Couleurs
    const goldColor = [212, 175, 55];
    const darkColor = [26, 26, 46];
    
    // En-tête
    doc.setFillColor(...goldColor);
    doc.rect(0, 0, 210, 40, 'F');
    
    doc.setTextColor(...darkColor);
    doc.setFontSize(24);
    doc.setFont(undefined, 'bold');
    doc.text('MUSEOLINK', 105, 20, { align: 'center' });
    
    doc.setFontSize(14);
    doc.setFont(undefined, 'normal');
    doc.text('Billet de réservation', 105, 30, { align: 'center' });
    
    // Informations du musée
    doc.setTextColor(0, 0, 0);
    doc.setFontSize(18);
    doc.setFont(undefined, 'bold');
    doc.text(reservation.museum, 20, 60);
    
    doc.setFontSize(12);
    doc.setFont(undefined, 'normal');
    doc.setTextColor(100, 100, 100);
    doc.text(reservation.location, 20, 70);
    
    // Ligne de séparation
    doc.setDrawColor(...goldColor);
    doc.setLineWidth(0.5);
    doc.line(20, 80, 190, 80);
    
    // Détails de la réservation
    doc.setTextColor(0, 0, 0);
    doc.setFontSize(12);
    
    let yPos = 95;
    const lineHeight = 10;
    
    const details = [
        ['Date de visite:', new Date(reservation.date).toLocaleDateString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })],
        ['Heure:', reservation.time],
        ['Nombre de personnes:', reservation.adults + ' personne(s)'],
        ['Prix total:', reservation.price.toFixed(2) + ' €'],
        ['Référence:', reservation.booking_ref],
        ['Statut:', 'Confirmée']
    ];
    
    details.forEach(([label, value]) => {
        doc.setFont(undefined, 'bold');
        doc.text(label, 20, yPos);
        doc.setFont(undefined, 'normal');
        doc.text(value, 80, yPos);
        yPos += lineHeight;
    });
    
    // QR Code placeholder (texte)
    yPos += 10;
    doc.setFillColor(240, 240, 240);
    doc.rect(70, yPos, 70, 70, 'F');
    doc.setTextColor(100, 100, 100);
    doc.setFontSize(10);
    doc.text('QR Code', 105, yPos + 35, { align: 'center' });
    doc.text(reservation.booking_ref, 105, yPos + 42, { align: 'center' });
    
    // Instructions
    yPos += 85;
    doc.setFontSize(10);
    doc.setTextColor(0, 0, 0);
    doc.setFont(undefined, 'bold');
    doc.text('Instructions importantes:', 20, yPos);
    
    yPos += 8;
    doc.setFont(undefined, 'normal');
    doc.setFontSize(9);
    const instructions = [
        '• Présentez ce billet à l\'entrée du musée',
        '• Arrivez 15 minutes avant l\'heure prévue',
        '• Le billet est valable uniquement pour la date et l\'heure indiquées',
        '• En cas d\'annulation, contactez-nous au moins 24h à l\'avance'
    ];
    
    instructions.forEach(instruction => {
        doc.text(instruction, 20, yPos);
        yPos += 6;
    });
    
    // Footer
    doc.setDrawColor(...goldColor);
    doc.line(20, 270, 190, 270);
    doc.setFontSize(8);
    doc.setTextColor(100, 100, 100);
    doc.text('MuseoLink - Votre passerelle vers l\'art et la culture', 105, 280, { align: 'center' });
    doc.text('https://museo.alwaysdata.net', 105, 285, { align: 'center' });
    
    // Télécharger le PDF
    const fileName = `Billet_${reservation.museum.replace(/[^a-z0-9]/gi, '_')}_${reservation.date}.pdf`;
    doc.save(fileName);
}

// Fonction d'annulation de réservation
async function cancelReservation(reservationId, museumName) {
    if (!confirm(`Êtes-vous sûr de vouloir annuler votre réservation pour "${museumName}" ?\n\nCette action est irréversible.`)) {
        return;
    }
    
    try {
        const response = await fetch('api/reservations.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                reservation_id: reservationId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Réservation annulée avec succès !');
            // Recharger la page pour actualiser la liste
            window.location.reload();
        } else {
            alert('❌ Erreur : ' + (data.error || 'Impossible d\'annuler la réservation'));
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('❌ Erreur de connexion. Veuillez réessayer.');
    }
}
</script>
</main>
<!-- Script de thème intégré dans private_nav.php -->
</body>
</html>
