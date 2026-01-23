# Système de Gestion du Menu Domini

## 📊 Architecture de la Base de Données

### Tables créées :

1. **`categories`** - Catégories de plats
   - `id`, `nom`, `logo`, `est_disponible`, `timestamps`

2. **`plats`** - Plats disponibles
   - `id`, `nom`, `prix`, `image`, `est_disponible`, `detail`, `categorie_id`, `timestamps`

3. **`accompagnements`** - Accompagnements pour les plats
   - `id`, `nom`, `image`, `qte_gratuit`, `prix_unitaire`, `disponible`, `plat_id`, `timestamps`

4. **`options`** - Options supplémentaires pour les plats
   - `id`, `nom`, `image`, `qte_gratuit`, `prix_unitaire`, `disponible`, `plat_id`, `timestamps`

### Relations :

```
categories (1) ──── (N) plats
plats (1) ──── (N) accompagnements
plats (1) ──── (N) options
```

## 🎯 Fonctionnalités

### 1. Gestion des Catégories
**URL** : `/admin/menu/categories`

- **Affichage en cartes** avec image/logo
- **Modal pour créer** une nouvelle catégorie
- **Modal pour modifier** une catégorie existante
- **Switch** pour activer/désactiver
- **Bouton supprimer** avec confirmation
- **Validation** : impossible de supprimer si des plats sont liés

### 2. Gestion des Plats
**URL** : `/admin/menu/plats`

- **Affichage en cartes** avec image, prix, catégorie
- **Panneau latéral** qui s'ouvre à droite pour :
  - ✅ Créer un nouveau plat
  - ✅ Modifier un plat existant
  - ✅ Gérer les accompagnements et options
- **Switch** pour activer/désactiver rapidement
- **Bouton gérer extras** (accompagnements + options)
- **Bouton supprimer** avec confirmation

### 3. Accompagnements & Options

#### Concept :
- Chaque plat peut avoir **0 ou plusieurs** accompagnements
- Chaque plat peut avoir **0 ou plusieurs** options
- Les accompagnements/options peuvent être **gratuits** ou **payants**

#### Champs :
- **`qte_gratuit`** : Nombre d'unités gratuites incluses
- **`prix_unitaire`** : Prix par unité supplémentaire (en FCFA)

#### Exemples :

**Accompagnement** :
- Nom : "Alloco"
- Qté gratuite : 1
- Prix unitaire : 500 FCFA
- → Le client reçoit 1 alloco gratuit, chaque alloco supplémentaire coûte 500 FCFA

**Option** :
- Nom : "Fromage"
- Qté gratuite : 0
- Prix unitaire : 200 FCFA
- → Chaque portion de fromage coûte 200 FCFA

## 🚀 Utilisation

### Accéder au menu
```
http://localhost:8000/admin/menu
```

### Créer une catégorie
1. Aller sur `/admin/menu/categories`
2. Cliquer sur la card "Ajouter une catégorie"
3. Remplir le formulaire dans le modal
4. Soumettre

### Créer un plat
1. Aller sur `/admin/menu/plats`
2. Cliquer sur "Ajouter un plat"
3. Remplir le formulaire dans le panneau latéral :
   - Nom du plat
   - Catégorie (liste déroulante)
   - Prix en FCFA
   - Image (optionnel, max 5 Mo)
   - Détails/Description (optionnel)
   - Statut disponible/indisponible
4. Cliquer sur "Créer le plat"

### Modifier un plat
1. Cliquer sur l'icône **crayon** (jaune) sur la card du plat
2. Modifier les informations dans le panneau latéral
3. Cliquer sur "Enregistrer"

### Gérer les accompagnements/options
1. Cliquer sur l'icône **+** (noir) sur la card du plat
2. Le panneau latéral s'ouvre avec deux sections :
   - **Accompagnements** (fond beige)
   - **Options** (fond jaune)
3. Cliquer sur "+ Ajouter un accompagnement" ou "+ Ajouter une option"
4. Remplir le formulaire :
   - Nom
   - Quantité gratuite
   - Prix unitaire
   - Disponible (checkbox)
5. Soumettre

### Supprimer un accompagnement/option
- Cliquer sur l'icône **poubelle** (rouge) à droite de l'accompagnement/option
- Confirmer la suppression

## 📁 Structure des fichiers

```
domini/
├── app/
│   ├── Models/
│   │   ├── Categorie.php
│   │   ├── Plat.php
│   │   ├── Accompagnement.php
│   │   └── Option.php
│   └── Http/Controllers/
│       ├── MenuController.php       (stats du menu)
│       ├── CategorieController.php  (CRUD catégories)
│       └── PlatController.php       (CRUD plats + extras)
├── database/migrations/
│   ├── 2026_01_20_000002_create_categories_table.php
│   ├── 2026_01_20_000003_create_plats_table.php
│   ├── 2026_01_20_000004_create_accompagnements_table.php
│   └── 2026_01_20_000005_create_options_table.php
├── resources/views/admin/menu/
│   ├── index.blade.php              (page principale menu)
│   ├── categories/
│   │   └── index.blade.php          (gestion catégories)
│   └── plats/
│       └── index.blade.php          (gestion plats avec panneau latéral)
└── storage/app/public/
    ├── categories/                   (images catégories)
    ├── plats/                        (images plats)
    ├── accompagnements/              (images accompagnements)
    └── options/                      (images options)
```

## 🎨 Interface Utilisateur

### Panneau Latéral (Sidebar)
- **Largeur** : 600px (max 90vw sur mobile)
- **Position** : Fixe à droite
- **Animation** : Slide-in de droite vers gauche
- **Overlay** : Fond noir semi-transparent
- **Fermeture** : 
  - Cliquer sur l'overlay
  - Cliquer sur le bouton ×
  - Appuyer sur Échap

### Design
- **Couleurs principales** :
  - Primary : #D9542A (orange Domini)
  - Secondary : #F7B801 (jaune Domini)
  - Dark : #3A3A3A
- **Cards** : Effet hover avec élévation
- **Switch** : Toggle iOS-like
- **Badges** : Pour statuts et catégories

## ⚙️ Validations

### Plats
- Nom : obligatoire, max 255 caractères
- Prix : obligatoire, numérique, >= 0
- Image : optionnel, formats jpeg/png/jpg/gif/webp, max 5 Mo
- Catégorie : obligatoire, doit exister dans la table categories
- Détails : optionnel, texte

### Accompagnements/Options
- Nom : obligatoire, max 255 caractères
- Qté gratuite : obligatoire, entier >= 0
- Prix unitaire : obligatoire, numérique >= 0
- Image : optionnel, max 5 Mo

## 🔒 Sécurité

- **CSRF Protection** : Tous les formulaires incluent `@csrf`
- **Method Spoofing** : `@method('PUT')`, `@method('DELETE')`
- **Validation serveur** : Messages en français
- **Validation client** : Vérification de la taille des images avant upload
- **Suppression en cascade** : Supprimer un plat supprime ses accompagnements/options
- **Vérification des relations** : Impossible de supprimer une catégorie avec des plats

## 🐛 Débogage

### Problème : Les stats ne se mettent pas à jour
**Solution** : Le `MenuController` récupère maintenant les stats dynamiquement depuis la base de données.

### Problème : Upload d'image échoue
**Solutions** :
1. Vérifier que `storage:link` a été exécuté
2. Vérifier les permissions des dossiers `storage/app/public/*`
3. Vérifier la taille de l'image (max 5 Mo)
4. Vérifier le format (jpeg, png, jpg, gif, webp)

### Problème : Le panneau latéral ne s'ouvre pas
**Solution** : Vérifier que JavaScript est activé et qu'il n'y a pas d'erreurs dans la console du navigateur.

## 📊 Statistiques affichées

Sur la page `/admin/menu` :
- **Total Catégories** : Nombre de catégories créées
- **Total Plats** : Nombre de plats créés
- **Plats Actifs** : Nombre de plats disponibles (`est_disponible = true`)

## 💡 Conseils d'utilisation

1. **Créer d'abord les catégories** avant d'ajouter des plats
2. **Optimiser les images** avant upload (max 800x800px recommandé)
3. **Utiliser des descriptions claires** pour les plats
4. **Indiquer les quantités gratuites** pour les accompagnements
5. **Désactiver plutôt que supprimer** les plats saisonniers
6. **Organiser les accompagnements** par type (féculents, légumes, sauces)
7. **Organiser les options** par type (fromage, viande supplémentaire, sauces)

## 🔄 Workflow recommandé

1. **Créer les catégories** (Entrées, Plats principaux, Desserts, Boissons)
2. **Ajouter les plats** dans chaque catégorie
3. **Configurer les accompagnements** pour chaque plat
4. **Configurer les options** pour personnalisation
5. **Tester** en activant/désactivant
6. **Ajuster les prix** selon la stratégie (1500-3000 FCFA)
