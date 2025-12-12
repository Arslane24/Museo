<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept_cookies'])) {
        setcookie('cookie_consent', 'accepted', time() + (365 * 24 * 60 * 60), '/', '', false, true);
        setcookie('museo_theme', 'light', time() + (365 * 24 * 60 * 60), '/', '', false, false);
    } elseif (isset($_POST['decline_cookies'])) {
        setcookie('cookie_consent', 'declined', 0, '/', '', false, true);
    }
}
