<?php
/**
 * SCRIPT D'ENRICHISSEMENT BDD - MUSÉES
 * Ajoute automatiquement des musées de France 🇫🇷, UK 🇬🇧, USA 🇺🇸, Japon 🇯🇵
 */

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/secret/database.php';
$data = require __DIR__ . '/secret/database.php';
$pdo = $data['pdo'];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enrichissement BDD - Musées</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #0f172a; color: white; }
        .container { max-width: 1200px; margin: 0 auto; }
        .success { background: #28a745; padding: 15px; margin: 10px 0; border-radius: 8px; }
        .error { background: #dc3545; padding: 15px; margin: 10px 0; border-radius: 8px; }
        .museum-card { background: #1e293b; padding: 20px; margin: 15px 0; border-radius: 12px; border-left: 5px solid #c9a961; }
        .museum-card h3 { color: #c9a961; margin: 0 0 10px 0; }
        .stats { background: #c9a961; color: #0f172a; padding: 20px; border-radius: 12px; margin: 20px 0; text-align: center; }
        .stats h2 { margin: 0; font-size: 2.5rem; }
        pre { background: #0f172a; padding: 10px; border-radius: 5px; overflow-x: auto; }
        button { background: #c9a961; color: #0f172a; border: none; padding: 15px 30px; border-radius: 8px; font-size: 1.1rem; font-weight: bold; cursor: pointer; margin: 10px 5px; }
        button:hover { background: #dfc480; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏛️ Enrichissement Base de Données - Musées Mondiaux</h1>
        <p>Ajout automatique de musées réels avec coordonnées GPS</p>

<?php

// Liste des musées à ajouter avec leurs vraies données
$museums = [
    // 🇫🇷 FRANCE
    [
        'name' => 'Musée du Louvre',
        'description' => 'Le plus grand musée d\'art du monde avec la Joconde et la Vénus de Milo. Plus de 380 000 œuvres d\'art.',
        'address' => 'Rue de Rivoli, 75001 Paris',
        'city' => 'Paris',
        'country' => 'France',
        'latitude' => 48.8606,
        'longitude' => 2.3376,
        'price' => 17.00,
        'category' => 'art',
        'image_url' => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=800'
    ],
    [
        'name' => 'Musée d\'Orsay',
        'description' => 'Collections impressionnistes et post-impressionnistes : Monet, Renoir, Van Gogh, Degas.',
        'address' => '1 Rue de la Légion d\'Honneur, 75007 Paris',
        'city' => 'Paris',
        'country' => 'France',
        'latitude' => 48.8600,
        'longitude' => 2.3266,
        'price' => 16.00,
        'category' => 'art',
        'image_url' => 'https://images.unsplash.com/photo-1536924430914-91f9e2041b83?w=800'
    ],
    [
        'name' => 'Centre Pompidou',
        'description' => 'Art moderne et contemporain. Architecture révolutionnaire de Renzo Piano et Richard Rogers.',
        'address' => 'Place Georges-Pompidou, 75004 Paris',
        'city' => 'Paris',
        'country' => 'France',
        'latitude' => 48.8606,
        'longitude' => 2.3522,
        'price' => 15.00,
        'category' => 'modern',
        'image_url' => 'https://images.unsplash.com/photo-1582555172866-f73bb12a2ab3?w=800'
    ],
    [
        'name' => 'Musée Rodin',
        'description' => 'Sculptures d\'Auguste Rodin dont Le Penseur et Le Baiser dans un magnifique hôtel particulier.',
        'address' => '77 Rue de Varenne, 75007 Paris',
        'city' => 'Paris',
        'country' => 'France',
        'latitude' => 48.8553,
        'longitude' => 2.3159,
        'price' => 13.00,
        'category' => 'art',
        'image_url' => 'https://images.unsplash.com/photo-1566305977571-5666677c6e98?w=800'
    ],
    
    // 🇬🇧 ROYAUME-UNI
    [
        'name' => 'British Museum',
        'description' => 'L\'un des plus grands musées au monde. Pierre de Rosette, momies égyptiennes, sculptures grecques.',
        'address' => 'Great Russell St, London WC1B 3DG',
        'city' => 'London',
        'country' => 'United Kingdom',
        'latitude' => 51.5194,
        'longitude' => -0.1270,
        'price' => 0.00,
        'category' => 'history',
        'image_url' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800'
    ],
    [
        'name' => 'National Gallery',
        'description' => 'Peintures européennes du 13e au 19e siècle. Van Gogh, Turner, Botticelli, Vélasquez.',
        'address' => 'Trafalgar Square, London WC2N 5DN',
        'city' => 'London',
        'country' => 'United Kingdom',
        'latitude' => 51.5089,
        'longitude' => -0.1283,
        'price' => 0.00,
        'category' => 'art',
        'image_url' => 'https://images.unsplash.com/photo-1567767292278-a4f21aa2d36e?w=800'
    ],
    [
        'name' => 'Tate Modern',
        'description' => 'Art moderne et contemporain international dans une ancienne centrale électrique.',
        'address' => 'Bankside, London SE1 9TG',
        'city' => 'London',
        'country' => 'United Kingdom',
        'latitude' => 51.5076,
        'longitude' => -0.0994,
        'price' => 0.00,
        'category' => 'modern',
        'image_url' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800'
    ],
    [
        'name' => 'Victoria and Albert Museum',
        'description' => 'Arts décoratifs et design. Plus grande collection d\'arts décoratifs au monde.',
        'address' => 'Cromwell Rd, London SW7 2RL',
        'city' => 'London',
        'country' => 'United Kingdom',
        'latitude' => 51.4966,
        'longitude' => -0.1722,
        'price' => 0.00,
        'category' => 'art',
        'image_url' => 'https://images.unsplash.com/photo-1563299796-17596ed6b017?w=800'
    ],
    
    // 🇺🇸 USA
    [
        'name' => 'Metropolitan Museum of Art',
        'description' => 'Le Met : 5000 ans d\'art de toutes les cultures. Plus de 2 millions d\'œuvres.',
        'address' => '1000 5th Ave, New York, NY 10028',
        'city' => 'New York',
        'country' => 'United States',
        'latitude' => 40.7794,
        'longitude' => -73.9632,
        'price' => 30.00,
        'category' => 'art',
        'image_url' => 'https://images.unsplash.com/photo-1545158535-c3f7168c28b6?w=800'
    ],
    [
        'name' => 'Museum of Modern Art (MoMA)',
        'description' => 'Art moderne et contemporain. Van Gogh, Picasso, Warhol, Pollock.',
        'address' => '11 W 53rd St, New York, NY 10019',
        'city' => 'New York',
        'country' => 'United States',
        'latitude' => 40.7614,
        'longitude' => -73.9776,
        'price' => 25.00,
        'category' => 'modern',
        'image_url' => 'https://images.unsplash.com/photo-1564399579883-451a5d44ec08?w=800'
    ],
    [
        'name' => 'Art Institute of Chicago',
        'description' => 'Collections impressionnistes, American Gothic de Grant Wood, Un dimanche à la Grande Jatte de Seurat.',
        'address' => '111 S Michigan Ave, Chicago, IL 60603',
        'city' => 'Chicago',
        'country' => 'United States',
        'latitude' => 41.8796,
        'longitude' => -87.6237,
        'price' => 32.00,
        'category' => 'art',
        'image_url' => 'https://images.unsplash.com/photo-1582555172866-f73bb12a2ab3?w=800'
    ],
    [
        'name' => 'Smithsonian National Museum',
        'description' => 'Le plus grand complexe de musées au monde avec 19 musées et galeries.',
        'address' => '1000 Jefferson Dr SW, Washington, DC 20560',
        'city' => 'Washington',
        'country' => 'United States',
        'latitude' => 38.8913,
        'longitude' => -77.0261,
        'price' => 0.00,
        'category' => 'history',
        'image_url' => 'https://images.unsplash.com/photo-1560594281-2e33b1e83c96?w=800'
    ],
    
    // 🇯🇵 JAPON
    [
        'name' => 'Tokyo National Museum',
        'description' => 'Le plus ancien et le plus grand musée du Japon. Art japonais, asiatique, archéologie.',
        'address' => '13-9 Uenokoen, Taito City, Tokyo 110-8712',
        'city' => 'Tokyo',
        'country' => 'Japan',
        'latitude' => 35.7188,
        'longitude' => 139.7764,
        'price' => 1000.00,
        'category' => 'art',
        'image_url' => 'https://images.unsplash.com/photo-1542640244-7e672d6cef4e?w=800'
    ],
    [
        'name' => 'Mori Art Museum',
        'description' => 'Art contemporain au 52e étage de la Mori Tower avec vue panoramique sur Tokyo.',
        'address' => '6 Chome-10-1 Roppongi, Minato City, Tokyo 106-6150',
        'city' => 'Tokyo',
        'country' => 'Japan',
        'latitude' => 35.6605,
        'longitude' => 139.7292,
        'price' => 1800.00,
        'category' => 'contemporary',
        'image_url' => 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=800'
    ],
    [
        'name' => 'Kyoto National Museum',
        'description' => 'Collections d\'art japonais pré-moderne. Patrimoine culturel de Kyoto.',
        'address' => '527 Chayacho, Higashiyama Ward, Kyoto, 605-0931',
        'city' => 'Kyoto',
        'country' => 'Japan',
        'latitude' => 34.9880,
        'longitude' => 135.7730,
        'price' => 700.00,
        'category' => 'art',
        'image_url' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=800'
    ],
    [
        'name' => 'TeamLab Borderless',
        'description' => 'Musée d\'art numérique immersif. Installations interactives et projections digitales.',
        'address' => '1 Chome-3-8 Aomi, Koto City, Tokyo 135-0064',
        'city' => 'Tokyo',
        'country' => 'Japan',
        'latitude' => 35.6251,
        'longitude' => 139.7753,
        'price' => 3200.00,
        'category' => 'contemporary',
        'image_url' => 'https://images.unsplash.com/photo-1480796927426-f609979314bd?w=800'
    ],
];

echo "<div class='stats'>";
echo "<h2>" . count($museums) . " musées prêts à être importés</h2>";
echo "<p>🇫🇷 France: 4 | 🇬🇧 UK: 4 | 🇺🇸 USA: 4 | 🇯🇵 Japon: 4</p>";
echo "</div>";

// Vérifier si on doit importer
if (isset($_GET['import']) && $_GET['import'] === 'confirm') {
    echo "<h2>Import en cours...</h2>";
    
    $imported = 0;
    $errors = 0;
    
    foreach ($museums as $museum) {
        try {
            // Vérifier si le musée existe déjà
            $stmt = $pdo->prepare("SELECT id FROM museums WHERE name = ?");
            $stmt->execute([$museum['name']]);
            
            if ($stmt->fetch()) {
                echo "<div class='museum-card'>";
                echo "<h3>{$museum['name']}</h3>";
                echo "<p>Déjà dans la base de données</p>";
                echo "</div>";
                continue;
            }
            
            // Insérer le musée
            $stmt = $pdo->prepare("
                INSERT INTO museums (name, description, address, city, country, latitude, longitude, price, category, image_url, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $museum['name'],
                $museum['description'],
                $museum['address'],
                $museum['city'],
                $museum['country'],
                $museum['latitude'],
                $museum['longitude'],
                $museum['price'],
                $museum['category'],
                $museum['image_url']
            ]);
            
            $imported++;
            
            echo "<div class='museum-card'>";
            echo "<h3>{$museum['name']}</h3>";
            echo "<p><strong>{$museum['city']}, {$museum['country']}</strong></p>";
            echo "<p>{$museum['description']}</p>";
            echo "<p>📍 GPS: {$museum['latitude']}, {$museum['longitude']}</p>";
            echo "<p>💰 Prix: " . number_format($museum['price'], 2) . " €</p>";
            echo "</div>";
            
        } catch (Exception $e) {
            $errors++;
            echo "<div class='error'>";
            echo "<h3>❌ Erreur: {$museum['name']}</h3>";
            echo "<p>{$e->getMessage()}</p>";
            echo "</div>";
        }
    }
    
    echo "<div class='stats'>";
    echo "<h2>Import terminé !</h2>";
    echo "<p>{$imported} musées ajoutés | {$errors} erreurs</p>";
    echo "<a href='explorer.php'><button>Voir les musées</button></a>";
    echo "</div>";
    
} else {
    // Afficher la prévisualisation
    echo "<h2>📋 Prévisualisation des musées à importer</h2>";
    
    foreach ($museums as $museum) {
        echo "<div class='museum-card'>";
        echo "<h3>{$museum['name']}</h3>";
        echo "<p><strong>📍 {$museum['city']}, {$museum['country']}</strong></p>";
        echo "<p>{$museum['description']}</p>";
        echo "<p>🎫 Prix: " . number_format($museum['price'], 2) . " €</p>";
        echo "<p>🗺️ Coordonnées: {$museum['latitude']}, {$museum['longitude']}</p>";
        echo "</div>";
    }
    
    echo "<div class='stats'>";
    echo "<a href='?import=confirm'><button>LANCER L'IMPORT</button></a>";
    echo "<p style='margin-top: 10px; font-size: 0.9rem;'>Cliquez pour ajouter tous ces musées dans votre base de données</p>";
    echo "</div>";
}

?>

    </div>
</body>
</html>
