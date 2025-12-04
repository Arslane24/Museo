# Museo
Projet de réservation pour des visites dans des musées

## 🎯 Description
Museo est une plateforme web moderne permettant aux utilisateurs de découvrir et réserver des billets pour les plus grands musées du monde. Le site offre une expérience utilisateur optimale avec un système d'authentification complet et une interface responsive.

## ✨ Fonctionnalités
- 🔐 Système d'authentification (inscription, connexion, réinitialisation de mot de passe)
- 🎫 Réservation de billets en ligne
- 🏛️ Exploration des musées du monde entier
- 📱 Design responsive et moderne
- 💌 Envoi d'emails automatiques (activation de compte, réinitialisation)
- 🔒 Sécurité renforcée (tokens, sessions, validation)

## 🚀 Technologies Utilisées
- **Backend**: PHP 8+
- **Base de données**: MySQL
- **Frontend**: Bootstrap 5, HTML5, CSS3, JavaScript
- **Email**: PHPMailer
- **Animations**: AOS (Animate On Scroll)
- **Cartes**: Leaflet.js

## 📋 SEO - Référencement

### Fichiers SEO Créés
✅ **robots.txt** - Indique aux moteurs de recherche quelles pages indexer
✅ **sitemap.xml** - Plan du site pour faciliter l'exploration
✅ **.htaccess** - Optimisation performances et sécurité
✅ **Meta tags** - Descriptions, mots-clés, Open Graph, Twitter Cards
✅ **Données structurées** - JSON-LD Schema.org

### Comment Vérifier le Référencement

#### 1. Google Search Console
1. Aller sur [Google Search Console](https://search.google.com/search-console)
2. Ajouter votre propriété (domaine)
3. Soumettre le sitemap: `https://museo.alwaysdata.net/sitemap.xml`
4. Vérifier l'indexation: Inspection d'URL

#### 2. Bing Webmaster Tools
1. Aller sur [Bing Webmaster Tools](https://www.bing.com/webmasters)
2. Ajouter votre site
3. Soumettre le sitemap
4. Utiliser l'outil de test SEO

#### 3. Qwant
1. Qwant utilise son propre index
2. Soumettre l'URL: [Qwant](https://www.qwant.com)
3. Le site sera crawlé automatiquement

#### 4. Tests SEO Locaux

**Vérifier robots.txt:**
```
https://museo.alwaysdata.net/robots.txt
```

**Vérifier sitemap.xml:**
```
https://museo.alwaysdata.net/sitemap.xml
```

**Tester les balises meta** (F12 dans le navigateur):
- Vérifier les balises `<meta name="description">`
- Vérifier les balises Open Graph (`og:`)
- Vérifier les données structurées JSON-LD

**Outils de test recommandés:**
- [Google Rich Results Test](https://search.google.com/test/rich-results)
- [PageSpeed Insights](https://pagespeed.web.dev/)
- [Schema.org Validator](https://validator.schema.org/)

### Pages Indexables
- ✅ `index.php` - Page d'accueil (priorité: 1.0)
- ✅ `Explorer.php` - Découverte des musées (priorité: 0.9)
- ✅ `reserver.php` - Réservation (priorité: 0.8)
- ✅ `contact.php` - Contact (priorité: 0.7)

### Mots-clés Principaux
- musée, réservation musée, billets musée
- visite musée, culture, art, histoire
- Louvre, British Museum, MoMA
- musées Paris, musées monde

## 📁 Structure du Projet
```
museo/
├── index.php                 # Page d'accueil
├── Explorer.php             # Page de découverte des musées
├── reserver.php             # Page de réservation
├── contact.php              # Page de contact
├── login.php                # Connexion
├── register.php             # Inscription
├── logout.php               # Déconnexion
├── activate.php             # Activation du compte
├── reset_password.php       # Réinitialisation mot de passe
├── reset_request.php        # Demande de réinitialisation
├── robots.txt               # SEO - Instructions robots
├── sitemap.xml              # SEO - Plan du site
├── .htaccess               # Configuration Apache
├── css/                     # Feuilles de style
├── js/                      # Scripts JavaScript
├── include/                 # Fichiers inclus (header, footer)
├── secret/                  # Configuration sensible
│   ├── database.php        # Connexion BDD
│   └── api_keys.php        # Clés API
├── src/                     # Classes PHP
│   ├── models/             # Modèles
│   ├── services/           # Services (email, etc.)
│   └── utils/              # Utilitaires
└── public/images/          # Images et médias
```

## 🔧 Installation

1. **Cloner le projet**
```bash
git clone https://github.com/Arslane24/Museo.git
cd museo
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer la base de données**
- Créer une base de données MySQL
- Importer le schéma SQL
- Configurer `secret/database.php`

4. **Configurer les clés API**
- Modifier `secret/api_keys.php`
- Ajouter vos clés SMTP pour l'envoi d'emails

5. **Configurer le .htaccess**
- Activer HTTPS en production
- Ajuster les redirections selon vos besoins

6. **Tester le site**
```bash
php -S localhost:8000
```

## 📧 Configuration Email (PHPMailer)
Modifier dans `secret/api_keys.php`:
```php
define('SMTP_HOST', 'smtp.votre-serveur.com');
define('SMTP_USERNAME', 'votre-email@domaine.com');
define('SMTP_PASSWORD', 'votre-mot-de-passe');
define('SMTP_PORT', 587);
```

## 🔐 Sécurité
- ✅ Tokens de sécurité pour activation/réinitialisation
- ✅ Protection CSRF
- ✅ Validation des entrées utilisateur
- ✅ Mots de passe hashés (password_hash)
- ✅ Sessions sécurisées
- ✅ Protection des fichiers sensibles (.htaccess)

## 🌐 Référencement - Points Importants

### Avant la Mise en Ligne
1. ⚠️ Remplacer `votre-domaine.com` par votre vrai domaine dans:
   - `robots.txt` (ligne Sitemap)
   - `sitemap.xml` (toutes les URLs)
   
2. ⚠️ Activer HTTPS:
   - Décommenter les lignes HTTPS dans `.htaccess`
   
3. ⚠️ Ajouter des images réelles pour les musées dans `Explorer.php`

### Après la Mise en Ligne
1. ✅ Soumettre le sitemap aux moteurs de recherche
2. ✅ Créer un compte Google Search Console
3. ✅ Créer un compte Bing Webmaster Tools
4. ✅ Vérifier l'indexation après 48-72h
5. ✅ Surveiller les performances avec Google Analytics

## 👨‍💻 Auteur
**Arslane24** - [GitHub](https://github.com/Arslane24)

## 📄 Licence
Ce projet est sous licence MIT.

## 🎓 Contexte Académique
Projet réalisé dans le cadre d'un cours de développement web.
