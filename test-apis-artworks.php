<?php
/**
 * TEST DES APIs POUR RÉCUPÉRER DES ŒUVRES D'ART
 */
require_once 'config/api_keys.php';

header('Content-Type: text/html; charset=UTF-8');
echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Test APIs Œuvres</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#0f172a;color:#fff;}";
echo ".api-test{background:rgba(255,255,255,0.05);padding:20px;margin:20px 0;border-radius:10px;}";
echo "h2{color:#c9a961;}.success{color:#10b981;}.error{color:#ef4444;}";
echo "pre{background:#000;padding:15px;overflow-x:auto;border-radius:5px;}</style></head><body>";

echo "<h1>🎨 Test des APIs d'œuvres d'art</h1>";

// ========================================
// 1. TEST MET MUSEUM API (déjà utilisée)
// ========================================
echo "<div class='api-test'>";
echo "<h2>1. Met Museum API</h2>";
$met_url = MET_MUSEUM_API_URL . "search?q=painting&hasImages=true";
$met_response = @file_get_contents($met_url);
if ($met_response) {
    $met_data = json_decode($met_response, true);
    echo "<p class='success'>✅ Fonctionne - " . count($met_data['objectIDs'] ?? []) . " œuvres trouvées</p>";
    echo "<pre>" . json_encode(array_slice($met_data, 0, 3), JSON_PRETTY_PRINT) . "</pre>";
} else {
    echo "<p class='error'>❌ Erreur de connexion</p>";
}
echo "</div>";

// ========================================
// 2. TEST HARVARD ART MUSEUMS API
// ========================================
echo "<div class='api-test'>";
echo "<h2>2. Harvard Art Museums API</h2>";
$harvard_url = HARVARD_ART_API_URL . "object?apikey=" . HARVARD_ART_API_KEY . "&size=10&hasimage=1";
$harvard_response = @file_get_contents($harvard_url);
if ($harvard_response) {
    $harvard_data = json_decode($harvard_response, true);
    echo "<p class='success'>✅ Fonctionne - " . ($harvard_data['info']['totalrecords'] ?? 0) . " œuvres disponibles</p>";
    if (isset($harvard_data['records'][0])) {
        $artwork = $harvard_data['records'][0];
        echo "<p><strong>Exemple:</strong> " . ($artwork['title'] ?? 'N/A') . "</p>";
        echo "<p><strong>Image:</strong> " . ($artwork['primaryimageurl'] ?? 'N/A') . "</p>";
    }
    echo "<pre>" . json_encode(array_slice($harvard_data, 0, 2), JSON_PRETTY_PRINT) . "</pre>";
} else {
    echo "<p class='error'>❌ Erreur de connexion</p>";
}
echo "</div>";

// ========================================
// 3. TEST CHICAGO ART INSTITUTE API
// ========================================
echo "<div class='api-test'>";
echo "<h2>3. Chicago Art Institute API</h2>";
$chicago_url = CHICAGO_ART_API_URL . "api/v1/artworks/search?q=painting&limit=10&fields=id,title,artist_display,image_id";
$chicago_response = @file_get_contents($chicago_url);
if ($chicago_response) {
    $chicago_data = json_decode($chicago_response, true);
    echo "<p class='success'>✅ Fonctionne - " . ($chicago_data['pagination']['total'] ?? 0) . " œuvres disponibles</p>";
    if (isset($chicago_data['data'][0])) {
        $artwork = $chicago_data['data'][0];
        echo "<p><strong>Exemple:</strong> " . ($artwork['title'] ?? 'N/A') . "</p>";
        echo "<p><strong>Image ID:</strong> " . ($artwork['image_id'] ?? 'N/A') . "</p>";
        echo "<p><em>URL image: https://www.artic.edu/iiif/2/{image_id}/full/843,/0/default.jpg</em></p>";
    }
    echo "<pre>" . json_encode(array_slice($chicago_data, 0, 2), JSON_PRETTY_PRINT) . "</pre>";
} else {
    echo "<p class='error'>❌ Erreur de connexion</p>";
}
echo "</div>";

// ========================================
// 4. TEST RIJKSMUSEUM API
// ========================================
echo "<div class='api-test'>";
echo "<h2>4. Rijksmuseum API</h2>";
$rijks_url = RIJKSMUSEUM_API_URL . "?key=0fiuZFh4&imgonly=true&ps=10";
$rijks_response = @file_get_contents($rijks_url);
if ($rijks_response) {
    $rijks_data = json_decode($rijks_response, true);
    echo "<p class='success'>✅ Fonctionne - " . ($rijks_data['count'] ?? 0) . " œuvres disponibles</p>";
    if (isset($rijks_data['artObjects'][0])) {
        $artwork = $rijks_data['artObjects'][0];
        echo "<p><strong>Exemple:</strong> " . ($artwork['title'] ?? 'N/A') . "</p>";
        echo "<p><strong>Image:</strong> " . ($artwork['webImage']['url'] ?? 'N/A') . "</p>";
    }
    echo "<pre>" . json_encode(array_slice($rijks_data, 0, 2), JSON_PRETTY_PRINT) . "</pre>";
} else {
    echo "<p class='error'>❌ Erreur de connexion</p>";
}
echo "</div>";

// ========================================
// 5. TEST SMITHSONIAN API
// ========================================
echo "<div class='api-test'>";
echo "<h2>5. Smithsonian API</h2>";
$smith_url = SMITHSONIAN_API_URL . "api/v1.0/search?q=painting&api_key=" . SMITHSONIAN_API_KEY . "&rows=10";
$smith_response = @file_get_contents($smith_url);
if ($smith_response) {
    $smith_data = json_decode($smith_response, true);
    echo "<p class='success'>✅ Fonctionne - " . ($smith_data['response']['rowCount'] ?? 0) . " œuvres trouvées</p>";
    if (isset($smith_data['response']['rows'][0])) {
        $artwork = $smith_data['response']['rows'][0];
        echo "<p><strong>Exemple:</strong> " . ($artwork['title'] ?? 'N/A') . "</p>";
    }
    echo "<pre>" . json_encode(array_slice($smith_data, 0, 2), JSON_PRETTY_PRINT) . "</pre>";
} else {
    echo "<p class='error'>❌ Erreur de connexion</p>";
}
echo "</div>";

// ========================================
// RECOMMANDATIONS
// ========================================
echo "<div class='api-test'>";
echo "<h2>📊 Recommandations</h2>";
echo "<ul>";
echo "<li><strong>Met Museum:</strong> Excellente API, pas de clé requise, images haute qualité ✅</li>";
echo "<li><strong>Harvard Art:</strong> Très bonne API avec clé, 100k requêtes/jour ✅</li>";
echo "<li><strong>Chicago Art:</strong> Bonne API, images via IIIF, pas de clé ✅</li>";
echo "<li><strong>Rijksmuseum:</strong> Nécessite clé API (à obtenir), très riche ⚠️</li>";
echo "<li><strong>Smithsonian:</strong> API complexe mais riche, avec clé ✅</li>";
echo "</ul>";
echo "<p><strong>💡 Stratégie recommandée:</strong> Utiliser Met Museum (principal) + Harvard Art + Chicago Art pour maximiser les œuvres disponibles</p>";
echo "</div>";

echo "</body></html>";
?>
