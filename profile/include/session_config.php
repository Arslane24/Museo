<?php
/**
 * CONFIGURATION DES SESSIONS PHP
 * À inclure au début de chaque page qui utilise des sessions
 */

// Configuration stricte des cookies de session
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Mettre à 1 si HTTPS
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_lifetime', 0); // Cookie de session (se supprime à la fermeture du navigateur)
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '');

// Empêcher la regénération automatique de session
ini_set('session.use_strict_mode', 1);
ini_set('session.use_trans_sid', 0);

// Garbage collection agressif
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 1440); // 24 minutes
