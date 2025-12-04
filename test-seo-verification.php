<?php
/**
 * TEST SEO VERIFICATION - MuseoLink
 * Ce script vérifie que tous les éléments SEO sont en place
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test SEO - MuseoLink</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #0f172a;
            color: #e2e8f0;
        }
        h1 {
            color: #c9a961;
            text-align: center;
            margin-bottom: 30px;
        }
        h2 {
            color: #dfc480;
            border-bottom: 2px solid #c9a961;
            padding-bottom: 10px;
            margin-top: 30px;
        }
        .test-item {
            background: #1e293b;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #64748b;
        }
        .success {
            border-left-color: #10b981;
        }
        .warning {
            border-left-color: #f59e0b;
        }
        .error {
            border-left-color: #ef4444;
        }
        .status {
            font-weight: bold;
            margin-right: 10px;
        }
        .success .status { color: #10b981; }
        .warning .status { color: #f59e0b; }
        .error .status { color: #ef4444; }
        code {
            background: #0f172a;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.9em;
            color: #c9a961;
        }
        .url {
            color: #60a5fa;
            text-decoration: none;
        }
        .url:hover {
            text-decoration: underline;
        }
        .next-steps {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border: 2px solid #c9a961;
            padding: 20px;
            border-radius: 12px;
            margin-top: 30px;
        }
        .next-steps h3 {
            color: #c9a961;
            margin-top: 0;
        }
        .next-steps ol {
            line-height: 2;
        }
        .next-steps a {
            color: #60a5fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>🔍 Vérification SEO - MuseoLink</h1>
    <p style="text-align: center; color: #94a3b8;">Vérification des éléments essentiels pour le référencement</p>

    <h2>📄 Fichiers Essentiels</h2>

    <?php
    $baseUrl = 'https://museo.alwaysdata.net';
    $files = [
        'robots.txt' => [
            'path' => 'robots.txt',
            'description' => 'Fichier d\'instruction pour les robots des moteurs de recherche'
        ],
        'sitemap.xml' => [
            'path' => 'sitemap.xml',
            'description' => 'Plan du site XML pour faciliter l\'indexation'
        ],
        'googleb5ff906f7ef35242.html' => [
            'path' => 'googleb5ff906f7ef35242.html',
            'description' => 'Fichier de vérification Google Search Console'
        ]
    ];

    foreach ($files as $name => $info) {
        $exists = file_exists(__DIR__ . '/' . $info['path']);
        $class = $exists ? 'success' : 'error';
        $status = $exists ? '✅ EXISTE' : '❌ MANQUANT';
        
        echo "<div class='test-item {$class}'>";
        echo "<span class='status'>{$status}</span>";
        echo "<strong>{$name}</strong><br>";
        echo "<small>{$info['description']}</small><br>";
        if ($exists) {
            echo "📍 <a href='/{$info['path']}' class='url' target='_blank'>{$baseUrl}/{$info['path']}</a>";
        }
        echo "</div>";
    }
    ?>

    <h2>🎯 Meta Tags SEO dans header.php</h2>

    <?php
    $headerFile = __DIR__ . '/include/header.php';
    $headerContent = file_get_contents($headerFile);
    
    $metaTags = [
        'description' => ['name="description"', 'Balise meta description pour les résultats de recherche'],
        'keywords' => ['name="keywords"', 'Mots-clés pour le référencement'],
        'robots' => ['name="robots"', 'Instructions pour les robots (index, follow)'],
        'canonical' => ['rel="canonical"', 'URL canonique pour éviter le contenu dupliqué'],
        'og:title' => ['property="og:title"', 'Open Graph pour Facebook/LinkedIn'],
        'og:description' => ['property="og:description"', 'Description Open Graph'],
        'twitter:card' => ['name="twitter:card"', 'Twitter Card pour les partages'],
        'JSON-LD' => ['type="application/ld+json"', 'Données structurées Schema.org']
    ];

    foreach ($metaTags as $name => $info) {
        $exists = strpos($headerContent, $info[0]) !== false;
        $class = $exists ? 'success' : 'warning';
        $status = $exists ? '✅ PRÉSENT' : '⚠️ ABSENT';
        
        echo "<div class='test-item {$class}'>";
        echo "<span class='status'>{$status}</span>";
        echo "<strong>{$name}</strong><br>";
        echo "<small>{$info[1]}</small>";
        echo "</div>";
    }
    ?>

    <h2>📱 Pages avec SEO Optimisé</h2>

    <?php
    $pages = [
        'index.php' => 'Page d\'accueil',
        'Explorer.php' => 'Page de découverte des musées',
        'reserver.php' => 'Page de réservation',
        'contact.php' => 'Page de contact'
    ];

    foreach ($pages as $file => $description) {
        $path = __DIR__ . '/' . $file;
        $exists = file_exists($path);
        
        if ($exists) {
            $content = file_get_contents($path);
            $hasDescription = strpos($content, '$page_description') !== false;
            $hasKeywords = strpos($content, '$page_keywords') !== false;
            $hasTitle = strpos($content, '$page_title') !== false;
            
            $score = 0;
            if ($hasTitle) $score++;
            if ($hasDescription) $score++;
            if ($hasKeywords) $score++;
            
            if ($score === 3) {
                $class = 'success';
                $status = '✅ COMPLET';
            } elseif ($score > 0) {
                $class = 'warning';
                $status = '⚠️ PARTIEL';
            } else {
                $class = 'error';
                $status = '❌ MANQUANT';
            }
            
            echo "<div class='test-item {$class}'>";
            echo "<span class='status'>{$status}</span>";
            echo "<strong>{$file}</strong> - {$description}<br>";
            echo "<small>";
            echo ($hasTitle ? '✓ Title ' : '✗ Title ');
            echo ($hasDescription ? '✓ Description ' : '✗ Description ');
            echo ($hasKeywords ? '✓ Keywords' : '✗ Keywords');
            echo "</small>";
            echo "</div>";
        }
    }
    ?>

    <h2>🌐 URLs à Tester</h2>

    <div class="test-item">
        <strong>Vérification robots.txt</strong><br>
        <a href="<?php echo $baseUrl; ?>/robots.txt" class="url" target="_blank">
            <?php echo $baseUrl; ?>/robots.txt
        </a>
    </div>

    <div class="test-item">
        <strong>Vérification sitemap.xml</strong><br>
        <a href="<?php echo $baseUrl; ?>/sitemap.xml" class="url" target="_blank">
            <?php echo $baseUrl; ?>/sitemap.xml
        </a>
    </div>

    <div class="test-item">
        <strong>Vérification Google</strong><br>
        <a href="<?php echo $baseUrl; ?>/googleb5ff906f7ef35242.html" class="url" target="_blank">
            <?php echo $baseUrl; ?>/googleb5ff906f7ef35242.html
        </a>
    </div>

    <h2>🧪 Outils de Test Externes</h2>

    <div class="test-item">
        <strong>Google Rich Results Test</strong><br>
        <a href="https://search.google.com/test/rich-results?url=<?php echo urlencode($baseUrl); ?>" class="url" target="_blank">
            Tester les données structurées
        </a>
    </div>

    <div class="test-item">
        <strong>PageSpeed Insights</strong><br>
        <a href="https://pagespeed.web.dev/analysis?url=<?php echo urlencode($baseUrl); ?>" class="url" target="_blank">
            Tester la vitesse et performance
        </a>
    </div>

    <div class="test-item">
        <strong>Google Mobile-Friendly Test</strong><br>
        <a href="https://search.google.com/test/mobile-friendly?url=<?php echo urlencode($baseUrl); ?>" class="url" target="_blank">
            Tester la compatibilité mobile
        </a>
    </div>

    <div class="next-steps">
        <h3>🚀 Prochaines Étapes pour le Référencement</h3>
        <ol>
            <li>
                <strong>Google Search Console</strong><br>
                Allez sur <a href="https://search.google.com/search-console" target="_blank">search.google.com/search-console</a><br>
                → Ajoutez votre site : <code><?php echo $baseUrl; ?></code><br>
                → Vérifiez avec le fichier HTML (déjà en place)<br>
                → Soumettez le sitemap : <code>sitemap.xml</code>
            </li>
            <li>
                <strong>Bing Webmaster Tools</strong><br>
                Allez sur <a href="https://www.bing.com/webmasters" target="_blank">bing.com/webmasters</a><br>
                → Importez depuis Google Search Console (méthode rapide)<br>
                → OU ajoutez manuellement votre site
            </li>
            <li>
                <strong>Attendez 48-72h</strong><br>
                Le temps que les moteurs de recherche indexent votre site
            </li>
            <li>
                <strong>Vérifiez l'indexation</strong><br>
                Tapez dans Google : <code>site:museo.alwaysdata.net</code><br>
                Vos pages doivent apparaître dans les résultats
            </li>
        </ol>
    </div>

    <div style="text-align: center; margin-top: 40px; padding: 20px; color: #94a3b8;">
        <p>📖 Consultez le guide complet : <code>GUIDE-REFERENCEMENT-MOTEURS-RECHERCHE.md</code></p>
        <p><small>Script de vérification SEO - MuseoLink © <?php echo date('Y'); ?></small></p>
    </div>

</body>
</html>
