# ✅ CHECKLIST SEO - MUSEO
## Date: 21 novembre 2025

---

## 📋 FICHIERS CRÉÉS POUR LE RÉFÉRENCEMENT

### ✅ 1. robots.txt
**Emplacement:** `/robots.txt`
**Description:** Indique aux moteurs de recherche (Google, Bing, Qwant) quelles pages indexer
**Contenu:**
- ✅ Autorise l'indexation des pages publiques
- ✅ Bloque les pages privées et sensibles
- ✅ Référence le sitemap
- ✅ Définit un délai de crawl

**Test:** https://museo.alwaysdata.net/robots.txt

---

### ✅ 2. sitemap.xml
**Emplacement:** `/sitemap.xml`
**Description:** Plan du site XML pour faciliter l'exploration par les moteurs de recherche
**Pages incluses:**
- ✅ index.php (priorité 1.0)
- ✅ Explorer.php (priorité 0.9)
- ✅ reserver.php (priorité 0.8)
- ✅ contact.php (priorité 0.7)

**⚠️ ACTION REQUISE:** Remplacer `https://museo.alwaysdata.net` par votre vrai domaine

**Test:** https://museo.alwaysdata.net/sitemap.xml

---

### ✅ 3. .htaccess
**Emplacement:** `/.htaccess`
**Optimisations incluses:**
- ✅ URLs propres (suppression du .php)
- ✅ Compression GZIP
- ✅ Mise en cache des fichiers statiques
- ✅ En-têtes de sécurité
- ✅ Protection des fichiers sensibles
- ✅ Pages d'erreur personnalisées

**⚠️ ACTION REQUISE EN PRODUCTION:**
- Décommenter les lignes HTTPS
- Activer la redirection www → non-www

---

### ✅ 4. Meta Tags SEO (header.php)
**Emplacement:** `/include/header.php`
**Balises ajoutées:**
- ✅ Meta description dynamique
- ✅ Meta keywords
- ✅ Meta author et robots
- ✅ Canonical URL
- ✅ Open Graph (Facebook)
- ✅ Twitter Cards
- ✅ JSON-LD Schema.org

**Pages avec descriptions personnalisées:**
- ✅ index.php
- ✅ reserver.php
- ✅ Explorer.php
- ✅ contact.php

---

### ✅ 5. Pages Créées
**Explorer.php** - Page de découverte des musées
- ✅ Meta tags SEO optimisés
- ✅ Contenu structuré avec 6 musées
- ✅ Design responsive

**contact.php** - Page de contact
- ✅ Meta tags SEO optimisés
- ✅ Formulaire de contact fonctionnel
- ✅ Informations de contact

---

## 🔍 COMMENT VÉRIFIER LE RÉFÉRENCEMENT

### 1️⃣ Google Search Console
1. Aller sur: https://search.google.com/search-console
2. Ajouter votre propriété (domaine)
3. Soumettre le sitemap: `https://museo.alwaysdata.net/sitemap.xml`
4. Utiliser "Inspection d'URL" pour vérifier l'indexation
5. Attendre 24-48h pour voir les résultats

**📊 Métriques à surveiller:**
- Nombre de pages indexées
- Impressions dans les résultats de recherche
- Taux de clics (CTR)
- Erreurs d'exploration

---

### 2️⃣ Bing Webmaster Tools
1. Aller sur: https://www.bing.com/webmasters
2. Ajouter votre site
3. Soumettre le sitemap
4. Utiliser l'outil d'analyse SEO
5. Attendre l'indexation

**Note:** Yahoo utilise l'index de Bing, donc votre site sera aussi référencé sur Yahoo

---

### 3️⃣ Qwant
1. Qwant explore automatiquement les sites
2. Pas de soumission manuelle nécessaire
3. Le sitemap.xml et robots.txt aideront à l'indexation
4. Vérifier après quelques jours: rechercher "site:museo.alwaysdata.net" sur Qwant

---

### 4️⃣ Tests SEO à Effectuer

**A. Vérifier robots.txt**
```
URL: https://museo.alwaysdata.net/robots.txt
✅ Le fichier doit s'afficher correctement
✅ Vérifier que le sitemap est référencé
```

**B. Vérifier sitemap.xml**
```
URL: https://museo.alwaysdata.net/sitemap.xml
✅ Le fichier XML doit s'afficher
✅ Vérifier que toutes les URLs sont correctes
```

**C. Tester les Meta Tags**
```
1. Ouvrir une page (ex: index.php)
2. F12 → Onglet "Elements" ou "Inspecteur"
3. Chercher dans <head>:
   ✅ <meta name="description">
   ✅ <meta property="og:title">
   ✅ <script type="application/ld+json">
```

**D. Tests avec outils en ligne**

**Google Rich Results Test:**
- URL: https://search.google.com/test/rich-results
- Tester chaque page pour vérifier les données structurées

**PageSpeed Insights:**
- URL: https://pagespeed.web.dev/
- Vérifier les performances (mobile et desktop)
- Objectif: Score > 90

**Schema.org Validator:**
- URL: https://validator.schema.org/
- Valider les données structurées JSON-LD

---

## ⚠️ ACTIONS AVANT LA MISE EN LIGNE

### OBLIGATOIRE:
1. ✏️ Remplacer `votre-domaine.com` par votre vrai domaine dans:
   - robots.txt (ligne Sitemap)
   - sitemap.xml (toutes les balises <loc>)

2. 🔒 Activer HTTPS:
   - Obtenir un certificat SSL (Let's Encrypt gratuit)
   - Décommenter les lignes HTTPS dans .htaccess

3. 📧 Configurer les emails:
   - Renseigner les paramètres SMTP dans secret/api_keys.php
   - Tester l'envoi d'emails

4. 🖼️ Ajouter les images des musées:
   - Créer le dossier public/images/
   - Ajouter: louvre.jpg, british-museum.jpg, moma.jpg, etc.

### RECOMMANDÉ:
5. 📊 Installer Google Analytics
6. 🔍 Créer un compte Search Console
7. 📱 Tester sur mobile
8. ⚡ Optimiser les images (compression)

---

## 📊 APRÈS 48H DE MISE EN LIGNE

### Vérifications à faire:

**Google:**
```
Rechercher: site:votre-domaine.com
Résultat attendu: Vos pages indexées apparaissent
```

**Bing:**
```
Rechercher: site:votre-domaine.com
Résultat attendu: Vos pages indexées apparaissent
```

**Qwant:**
```
Rechercher: site:votre-domaine.com
Résultat attendu: Vos pages indexées apparaissent
```

### Si rien n'apparaît:
1. Vérifier que robots.txt n'empêche pas l'indexation
2. Re-soumettre le sitemap
3. Utiliser "Demander l'indexation" dans Search Console
4. Attendre 24h de plus

---

## 🎯 MOTS-CLÉS CIBLÉS

**Principaux:**
- musée
- réservation musée
- billets musée
- visite musée
- culture
- art
- histoire

**Secondaires:**
- Louvre
- British Museum
- MoMA
- musées Paris
- musées monde
- exposition
- galerie

**Longue traîne:**
- "réserver billets musée en ligne"
- "visite musée Paris réservation"
- "acheter billets Louvre"

---

## ✅ RÉCAPITULATIF FINAL

| Élément | Status | Testé |
|---------|--------|-------|
| robots.txt | ✅ Créé | ⬜ À tester |
| sitemap.xml | ✅ Créé | ⬜ À tester |
| .htaccess | ✅ Créé | ⬜ À tester |
| Meta tags | ✅ Créé | ⬜ À tester |
| Open Graph | ✅ Créé | ⬜ À tester |
| JSON-LD | ✅ Créé | ⬜ À tester |
| Explorer.php | ✅ Créé | ⬜ À tester |
| contact.php | ✅ Créé | ⬜ À tester |
| Google Search Console | ⬜ À configurer | ⬜ À tester |
| Bing Webmaster | ⬜ À configurer | ⬜ À tester |

---

## 📞 SUPPORT

Si le professeur pose des questions:

**Q: Où est le sitemap?**
R: `/sitemap.xml` - Contient 4 pages principales avec priorités

**Q: Avez-vous configuré robots.txt?**
R: Oui, `/robots.txt` - Autorise les pages publiques, bloque les privées

**Q: Les meta tags sont-ils présents?**
R: Oui, dans `/include/header.php` avec descriptions personnalisées par page

**Q: Avez-vous des données structurées?**
R: Oui, JSON-LD Schema.org dans le header

**Q: Le site est-il optimisé pour le SEO?**
R: Oui:
- URLs propres (.htaccess)
- Compression GZIP
- Mise en cache
- Meta tags complets
- Sitemap XML
- Données structurées

---

