# 🏛️ GUIDE COMPLET - APIs MUSEO

## 📊 **RÉSUMÉ DES 12 APIs ESSENTIELLES**

Ce guide présente toutes les APIs utilisées dans le site MUSEO pour fournir une expérience complète aux utilisateurs.

---

## 🎯 **FONCTIONNALITÉS CLÉS DU SITE**

- ✅ **Météo** de la ville du musée choisi
- ✅ **Œuvres d'art** avec informations détaillées
- ✅ **Images** de qualité des œuvres
- ✅ **Géolocalisation** sur carte interactive
- ✅ **Informations** complètes sur chaque œuvre

---

## 🔑 **RÉCAPITULATIF DES CLÉS API**

### **APIs AVEC CLÉ (4 APIs) :**
1. **Europeana** - `addevollail`
2. **OpenCage** - `56240991f2b34462b6f0caf6bdd0830e`
3. **Smithsonian** - `K4Q4bTWIyN4AUALW8vngTuTLh1JU8gk19EdbX2Q4`
4. **OpenWeatherMap** - `09bb84206c31c4428d3df828199144fb`

### **APIS GRATUITES (8 APIs) :**
- Paris Musées, Images d'Art, Metropolitan Museum, Chicago Art Institute, British Museum, Nominatim, Wikimedia Commons, POP

---

## 🟢 **APIs D'ŒUVRES FRANÇAISES (3 APIs)**

### 1. **Europeana API**
- **URL :** `https://www.europeana.eu/api/v2/search.json`
- **Clé requise :** ✅ Oui (`addevollail`)
- **Description :** Collections d'art européennes - Plus de 50 millions d'objets numériques
- **Rôle :** Fournit les œuvres d'art françaises et européennes
- **Limite :** 100 requêtes/jour
- **Exemple d'utilisation :** Recherche d'œuvres par mot-clé, récupération de détails

### 2. **Paris Musées API**
- **URL :** `https://www.parismuseescollections.paris.fr/api/`
- **Clé requise :** ❌ Gratuite
- **Description :** Collections des 14 musées municipaux de Paris - 280 000+ notices
- **Rôle :** Œuvres spécifiques des musées parisiens
- **Limite :** Illimité
- **Exemple d'utilisation :** Recherche d'œuvres par mot-clé, récupération de détails

### 3. **Images d'Art (RMN-GP) API**
- **URL :** `https://art.rmngp.fr/api/`
- **Clé requise :** ❌ Gratuite
- **Description :** Images haute résolution de la Réunion des Musées Nationaux
- **Rôle :** Images de qualité muséale des œuvres françaises
- **Limite :** Illimité
- **Exemple d'utilisation :** Recherche d'images d'œuvres, informations sur les artistes

---

## 🔵 **APIs D'ŒUVRES INTERNATIONALES (4 APIs)**

### 4. **Metropolitan Museum API**
- **URL :** `https://collectionapi.metmuseum.org/public/collection/v1/`
- **Clé requise :** ❌ Gratuite
- **Description :** Collections du Met New York - 400 000+ œuvres
- **Rôle :** Œuvres d'art internationales et américaines
- **Limite :** Illimité
- **Exemple d'utilisation :** Recherche d'objets, récupération de détails sur une œuvre

### 5. **Chicago Art Institute API**
- **URL :** `https://api.artic.edu/`
- **Clé requise :** ❌ Gratuite
- **Description :** Collections de l'Art Institute of Chicago - 100 000+ œuvres
- **Rôle :** Œuvres d'art modernes et contemporaines
- **Limite :** Illimité
- **Exemple d'utilisation :** Recherche d'œuvres, images, métadonnées

### 6. **Smithsonian API**
- **URL :** `https://api.si.edu/openaccess/`
- **Clé requise :** ✅ Oui (`K4Q4bTWIyN4AUALW8vngTuTLh1JU8gk19EdbX2Q4`)
- **Description :** Collections Smithsonian - 3 millions+ d'objets
- **Rôle :** Trésors culturels américains et internationaux
- **Limite :** 1000 requêtes/jour
- **Exemple d'utilisation :** Recherche d'objets, images, métadonnées

### 7. **British Museum API**
- **URL :** `https://www.britishmuseum.org/api/collection/v1/search`
- **Clé requise :** ❌ Gratuite
- **Description :** Collections du British Museum - 4 millions+ d'objets
- **Rôle :** Antiquités et art mondial
- **Limite :** Illimité
- **Exemple d'utilisation :** Recherche d'objets, images, métadonnées

---

## 🔵 **APIs DE LOCALISATION (2 APIs)**

### 8. **Nominatim (OpenStreetMap) API**
- **URL :** `https://nominatim.openstreetmap.org/search`
- **Clé requise :** ❌ Gratuite
- **Description :** Service de géocodage OpenStreetMap
- **Rôle :** Coordonnées précises des musées
- **Limite :** 1 requête/seconde
- **Exemple d'utilisation :** Obtenir les coordonnées d'un musée

### 9. **OpenCage API**
- **URL :** `https://api.opencagedata.com/geocode/v1/json`
- **Clé requise :** ✅ Oui (`56240991f2b34462b6f0caf6bdd0830e`)
- **Description :** Service de géocodage global avancé
- **Rôle :** Géolocalisation de secours et enrichie
- **Limite :** 2500 requêtes/jour
- **Exemple d'utilisation :** Géocodage précis des adresses de musées

---

## ⚫ **API DE MÉTÉO (1 API)**

### 10. **OpenWeatherMap API**
- **URL :** `https://api.openweathermap.org/data/2.5/`
- **Clé requise :** ✅ Oui (`09bb84206c31c4428d3df828199144fb`)
- **Description :** Données météorologiques en temps réel
- **Rôle :** Météo pour planifier les visites
- **Limite :** 1000 requêtes/jour
- **Exemple d'utilisation :** Afficher la météo pour la ville du musée

---

## ⚪ **APIs D'IMAGES (2 APIs)**

### 11. **Wikimedia Commons API**
- **URL :** `https://commons.wikimedia.org/w/api.php`
- **Clé requise :** ❌ Gratuite
- **Description :** Images libres de droits Wikimedia
- **Rôle :** Images d'œuvres sous licence libre
- **Limite :** Illimité
- **Exemple d'utilisation :** Recherche d'images d'œuvres d'art

### 12. **POP (Patrimoine) API**
- **URL :** `https://data.culture.gouv.fr/api/records/1.0/search/`
- **Clé requise :** ❌ Gratuite
- **Description :** Plateforme Ouverte du Patrimoine français
- **Rôle :** Patrimoine culturel français
- **Limite :** Illimité
- **Exemple d'utilisation :** Recherche d'objets du patrimoine français

---

## 🚀 **UTILISATION DANS LE SITE**

### **Page d'accueil (`index.php`) :**
- Affichage des musées partenaires
- Widget météo
- Statistiques

### **Page de test (`test-apis-simple.php`) :**
- Tableau récapitulatif des APIs
- Sélection de musée
- Affichage des œuvres d'art
- Carte interactive
- Informations météo et géolocalisation

### **Fonctionnalités principales :**
1. **Sélection d'un musée** → Chargement automatique des données
2. **Affichage des œuvres** → Images, descriptions, métadonnées
3. **Carte interactive** → Localisation précise du musée
4. **Météo en temps réel** → Conditions actuelles de la ville
5. **Informations détaillées** → Clic sur une œuvre pour plus de détails

---

## 📁 **STRUCTURE DU PROJET**

```
MUSEO/
├── index.php                 # Page d'accueil
├── index-test.php            # Page de test
├── test-apis-simple.php      # Page de test des APIs
├── booking.php               # Page de réservation
├── config/
│   └── api_keys.php          # Configuration des clés API
├── css/
│   └── style.css             # Styles personnalisés
├── js/
│   └── api-tester.js         # Logique des APIs
├── includes/
│   ├── header.php            # En-tête commun
│   └── footer.php            # Pied de page commun
└── assets/
    └── images/               # Images du site
```

---

## ✅ **TOTAL : 12 APIs ESSENTIELLES**

**Toutes les APIs sont opérationnelles et fournissent des données réelles pour une expérience utilisateur complète !** 🎨

---

## 🔧 **CONFIGURATION DES CLÉS API**

### **Comment obtenir les clés API :**

1. **Europeana API :**
   - Visitez : https://pro.europeana.eu/get-api
   - Créez un compte gratuit
   - Générez votre clé API

2. **OpenCage API :**
   - Visitez : https://opencagedata.com/api
   - Créez un compte gratuit
   - Générez votre clé API (2500 requêtes/jour)

3. **Smithsonian API :**
   - Visitez : https://api.si.edu/openaccess/
   - Créez un compte gratuit
   - Générez votre clé API (1000 requêtes/jour)

4. **OpenWeatherMap API :**
   - Visitez : https://openweathermap.org/api
   - Créez un compte gratuit
   - Générez votre clé API (1000 requêtes/jour)

### **Configuration dans le projet :**
Toutes les clés sont déjà configurées dans le fichier `config/api_keys.php` et prêtes à l'emploi !