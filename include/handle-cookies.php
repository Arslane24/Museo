<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept_cookies'])) {
        // Accepté: cookie valide 1 an
        setcookie('cookie_consent', 'accepted', time() + (365 * 24 * 60 * 60), '/', '', false, true);
        // Ne pas forcer le thème, laisser l'utilisateur choisir
    } elseif (isset($_POST['decline_cookies'])) {
        // Refusé: cookie valide 30 jours (pour ne pas harceler l'utilisateur)
        setcookie('cookie_consent', 'declined', time() + (30 * 24 * 60 * 60), '/', '', false, true);
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}
