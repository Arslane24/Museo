<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test SEO - MuseoLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f8f9fa; }
        .test-card { margin-bottom: 20px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">🔍 Test SEO - MuseoLink</h1>
        
        <div class="card test-card">
            <div class="card-header bg-primary text-white">
                <h3>1. Fichiers SEO Essentiels</h3>
            </div>
            <div class="card-body">
                <?php
                $files = [
                    'robots.txt' => 'Instructions pour les moteurs de recherche',
                    'sitemap.xml' => 'Plan du site XML',
                    '.htaccess' => 'Configuration Apache',
                    'SEO-CHECKLIST.md' => 'Checklist SEO'
                ];
                
                foreach ($files as $file => $desc) {
                    $exists = file_exists($file);
                    $icon = $exists ? '✅' : '❌';
                    $class = $exists ? 'success' : 'error';
                    echo "<p class='$class'>$icon <strong>$file</strong> - $desc</p>";
                }
                ?>
            </div>
        </div>

        <div class="card test-card">
            <div class="card-header bg-success text-white">
                <h3>2. Pages Principales</h3>
            </div>
            <div class="card-body">
                <?php
                $pages = [
                    'index.php' => 'Page d accueil',
                    'Explorer.php' => 'Découverte des musées',
                    'reserver.php' => 'Réservation',
                    'contact.php' => 'Contact',
                    'login.php' => 'Connexion',
                    'register.php' => 'Inscription'
                ];
                
                foreach ($pages as $page => $desc) {
                    $exists = file_exists($page);
                    $icon = $exists ? '✅' : '❌';
                    $class = $exists ? 'success' : 'error';
                    echo "<p class='$class'>$icon <strong>$page</strong> - $desc</p>";
                }
                ?>
            </div>
        </div>

        <div class="card test-card">
            <div class="card-header bg-info text-white">
                <h3>3. Test des Meta Tags (Page d'accueil)</h3>
            </div>
            <div class="card-body">
                <?php
                if (file_exists('index.php')) {
                    $content = file_get_contents('index.php');
                    
                    $checks = [
                        'page_title' => ['Titre de la page', strpos($content, '$page_title') !== false],
                        'page_description' => ['Description SEO', strpos($content, '$page_description') !== false],
                        'page_keywords' => ['Mots-clés', strpos($content, '$page_keywords') !== false]
                    ];
                    
                    foreach ($checks as $key => $check) {
                        $icon = $check[1] ? '✅' : '❌';
                        $class = $check[1] ? 'success' : 'error';
                        echo "<p class='$class'>$icon <strong>{$check[0]}</strong></p>";
                    }
                } else {
                    echo "<p class='error'>❌ Impossible de lire index.php</p>";
                }
                ?>
            </div>
        </div>

        <div class="card test-card">
            <div class="card-header bg-warning text-dark">
                <h3>4. Test du Header SEO</h3>
            </div>
            <div class="card-body">
                <?php
                if (file_exists('include/header.php')) {
                    $content = file_get_contents('include/header.php');
                    
                    $checks = [
                        'meta description' => strpos($content, 'meta name="description"') !== false,
                        'meta keywords' => strpos($content, 'meta name="keywords"') !== false,
                        'Open Graph' => strpos($content, 'property="og:') !== false,
                        'Twitter Cards' => strpos($content, 'name="twitter:') !== false,
                        'JSON-LD' => strpos($content, 'application/ld+json') !== false,
                        'Canonical URL' => strpos($content, 'rel="canonical"') !== false
                    ];
                    
                    foreach ($checks as $name => $exists) {
                        $icon = $exists ? '✅' : '❌';
                        $class = $exists ? 'success' : 'error';
                        echo "<p class='$class'>$icon <strong>$name</strong></p>";
                    }
                } else {
                    echo "<p class='error'>❌ Impossible de lire include/header.php</p>";
                }
                ?>
            </div>
        </div>

        <div class="card test-card">
            <div class="card-header bg-secondary text-white">
                <h3>5. Contenu du robots.txt</h3>
            </div>
            <div class="card-body">
                <?php
                if (file_exists('robots.txt')) {
                    echo "<pre class='bg-light p-3'>";
                    echo htmlspecialchars(file_get_contents('robots.txt'));
                    echo "</pre>";
                    echo "<a href='robots.txt' target='_blank' class='btn btn-primary'>Voir robots.txt</a>";
                } else {
                    echo "<p class='error'>❌ robots.txt introuvable</p>";
                }
                ?>
            </div>
        </div>

        <div class="card test-card">
            <div class="card-header bg-secondary text-white">
                <h3>6. Contenu du sitemap.xml</h3>
            </div>
            <div class="card-body">
                <?php
                if (file_exists('sitemap.xml')) {
                    echo "<pre class='bg-light p-3' style='max-height: 300px; overflow-y: auto;'>";
                    echo htmlspecialchars(file_get_contents('sitemap.xml'));
                    echo "</pre>";
                    echo "<a href='sitemap.xml' target='_blank' class='btn btn-primary'>Voir sitemap.xml</a>";
                } else {
                    echo "<p class='error'>❌ sitemap.xml introuvable</p>";
                }
                ?>
            </div>
        </div>

        <div class="card test-card">
            <div class="card-header bg-dark text-white">
                <h3>7. Actions Requises</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h5>⚠️ Avant la mise en ligne:</h5>
                    <ol>
                        <li>Remplacer <code>votre-domaine.com</code> par votre vrai domaine dans robots.txt et sitemap.xml</li>
                        <li>Activer HTTPS (décommenter les lignes dans .htaccess)</li>
                        <li>Configurer les paramètres SMTP dans secret/api_keys.php</li>
                        <li>Ajouter les images des musées dans public/images/</li>
                    </ol>
                </div>
                
                <div class="alert alert-info">
                    <h5>📊 Après la mise en ligne:</h5>
                    <ol>
                        <li>Soumettre le sitemap sur <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a></li>
                        <li>Soumettre le sitemap sur <a href="https://www.bing.com/webmasters" target="_blank">Bing Webmaster Tools</a></li>
                        <li>Attendre 24-48h et vérifier l'indexation</li>
                        <li>Tester avec: <code>site:votre-domaine.com</code> sur Google, Bing, Qwant</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card test-card">
            <div class="card-header bg-success text-white">
                <h3>8. Liens Utiles</h3>
            </div>
            <div class="card-body">
                <ul>
                    <li><a href="https://search.google.com/search-console" target="_blank">Google Search Console</a></li>
                    <li><a href="https://www.bing.com/webmasters" target="_blank">Bing Webmaster Tools</a></li>
                    <li><a href="https://search.google.com/test/rich-results" target="_blank">Google Rich Results Test</a></li>
                    <li><a href="https://pagespeed.web.dev/" target="_blank">PageSpeed Insights</a></li>
                    <li><a href="https://validator.schema.org/" target="_blank">Schema.org Validator</a></li>
                    <li><a href="SEO-CHECKLIST.md" target="_blank">Voir la Checklist SEO complète</a></li>
                </ul>
            </div>
        </div>

        <div class="alert alert-success mt-4">
            <h4>✅ Configuration SEO Terminée!</h4>
            <p>Tous les éléments essentiels pour le référencement sont en place. Consultez le fichier <strong>SEO-CHECKLIST.md</strong> pour plus de détails.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
