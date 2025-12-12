# MuseoLink - Plateforme de Réservation de Musées

Projet réalisé dans le cadre du cours de développement web.

## Description

MuseoLink est une plateforme web permettant aux utilisateurs de découvrir et réserver des billets pour différents musées à travers le monde. Le système offre une interface intuitive pour explorer les collections, planifier des visites et gérer ses réservations.

## Technologies Utilisées

### Backend
- PHP 8.0+
- MySQL / MariaDB
- PDO pour les requêtes sécurisées
- PHPMailer pour l'envoi d'emails

### Frontend
- HTML5 / CSS3
- Bootstrap 5.1.3
- JavaScript (Vanilla)
- Font Awesome 6.0

### APIs Externes
- Met Museum API
- Harvard Art Museums API
- Europeana API
- Paris Musées API
- Chicago Art Institute API

### Cartographie
- Leaflet.js
- OpenStreetMap

## Fonctionnalités Principales

### Espace Public
- Recherche avancée de musées (nom, catégorie, pays)
- Exploration interactive avec carte géographique
- Consultation des détails et œuvres d'art
- Système de réservation en ligne
- Formulaire de contact

### Espace Utilisateur
- Authentification sécurisée (inscription, connexion, réinitialisation mot de passe)
- Dashboard personnalisé avec statistiques
- Gestion des réservations (création, consultation, annulation)
- Système de favoris
- Téléchargement de billets en PDF
- Profil utilisateur modifiable

### Administration
- Gestion des musées dans la base de données
- Suivi des réservations
- Statistiques d'utilisation

## Structure du Projet

```
MuseoV3/
├── api/                    # Endpoints API REST
│   ├── favorites-toggle.php
│   ├── museums-search.php
│   └── reservations.php
├── css/                    # Feuilles de style
│   ├── style.css
│   ├── auth-forms.css
│   └── ...
├── js/                     # Scripts JavaScript
│   ├── page-scripts.js
│   ├── explorer.js
│   └── ...
├── src/                    # Classes PHP
│   ├── models/            # Modèles de données
│   ├── services/          # Services (Mail, APIs)
│   └── utils/             # Utilitaires
├── include/               # Composants réutilisables
│   ├── header.php
│   ├── footer.php
│   └── auth.php
├── public/                # Ressources publiques
│   └── images/
├── secret/                # Configuration (non versionnée)
│   ├── .conf
│   ├── database.php
│   └── api_keys.php
└── vendor/                # Dépendances Composer

```

## Installation

### Prérequis
- PHP 8.0 ou supérieur
- MySQL 5.7+ ou MariaDB 10.3+
- Serveur web (Apache ou Nginx)
- Composer

### Configuration

1. Cloner le dépôt
```bash
git clone https://github.com/votre-username/MuseoV3.git
cd MuseoV3
```

2. Installer les dépendances
```bash
composer install
```

3. Configurer la base de données
   - Créer une base de données MySQL
   - Importer le schéma SQL fourni
   - Configurer les paramètres dans `secret/.conf`

4. Configuration du fichier `.conf`
```ini
DB_HOST=localhost
DB_NAME=votre_database
DB_USER=votre_user
DB_PASS=votre_password

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=votre_email@gmail.com
SMTP_PASS=votre_password_app
```

5. Configurer le serveur web
   - Pointer vers le dossier racine du projet
   - Activer mod_rewrite (Apache)
   - Configurer les permissions d'écriture si nécessaire

## Base de Données

### Tables Principales
- `users` - Comptes utilisateurs
- `museums` - Catalogue de musées
- `reservations` - Réservations des utilisateurs
- `favorites` - Musées favoris
- `contacts` - Messages de contact

## Sécurité

- Mots de passe hashés avec password_hash()
- Protection CSRF sur les formulaires
- Requêtes préparées PDO contre les injections SQL
- Validation et échappement des données utilisateur
- Authentification par session sécurisée
- HTTPS recommandé en production

## APIs REST

### Endpoints Disponibles

**Recherche de musées**
```
GET /api/museums-search.php?search=louvre&category=art&country=france
```

**Gestion des favoris**
```
POST /api/favorites-toggle.php
Body: {"museum_id": 1}
```

**Réservations**
```
GET /api/reservations.php           # Liste des réservations
POST /api/reservations.php          # Créer une réservation
DELETE /api/reservations.php        # Annuler une réservation
```

## Développement

### Standards de Code
- PSR-12 pour PHP
- Commentaires en français
- Nommage des variables en camelCase
- Classes en PascalCase

### Structure MVC
Le projet suit une architecture MVC simplifiée :
- Modèles dans `/src/models`
- Vues dans les fichiers `.php` racine
- Contrôleurs intégrés dans les modèles

## Auteurs
- **Yanis SAMAH** - Responsable de projet & support technique
   - Coordination générale du groupe
   - Gestion du planning
   - Organisation des réunions et communication interne
   - Participation aux choix techniques et à la validation des livrables
   - Support transversal auprès des développeurs
   - Développement complet du système d'envoi d'emails (PHPMailer)
   - Gestion de l'inscription et création de comptes utilisateurs
   - Optimisation SEO du site
   - Mise en place du site WordPress vitrine avec Lisa OUYAHIA

- **Arslane HAMLAT** - Développeur API & intégration des données
   - Développement et gestion des API externes (Harvard, OpenWeather, Géocodage)
   - Création des scripts internes : recherche, suggestions, données musées
   - Intégration du back-end avec le front-end
   - Optimisation des performances et traitement des données
   - Tests techniques et débogage

- **Lisa OUYAHIA** - Développeuse front-end & gestion WordPress
   - Conception et développement des pages privées (profils, favoris, réservations…) avec Amel BENAISSA
   - Intégration front-end des fonctionnalités utilisateur
   - Gestion complète du site WordPress vitrine avec Yanis SAMAH
   - Rédaction du contenu documentaire (fonctionnalités, modèle, APIs…)
   - Gestion de l'hébergement AlwaysData et mise en ligne du projet

- **Amel BENAISSA** - Développeuse front-end & testeuse
   - Conception et développement des pages privées avec Lisa OUYAHIA
   - Tests fonctionnels et validation des interfaces
   - Contribution au développement front-end


## Licence

Projet académique - Tous droits réservés
