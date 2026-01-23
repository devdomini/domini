# Système de Gestion des Livreurs - Domini

## 🚚 Vue d'ensemble

Le système de gestion des livreurs permet d'administrer tous les livreurs de la plateforme Domini, de gérer leurs affectations aux entreprises et de suivre leurs performances.

## 📊 Fonctionnalités

### 1. Liste des Livreurs (`/admin/livreurs`)

**Statistiques affichées :**
- Total des livreurs
- Livreurs actifs
- Livreurs inactifs

**Filtres disponibles :**
- 🔍 Recherche par nom, email, téléphone
- 📊 Filtrer par statut (Actif/Inactif)
- 🏢 Filtrer par entreprise affectée

**Actions par livreur :**
- 👁️ **Voir détails** - Afficher les informations complètes et statistiques
- ✏️ **Modifier** - Éditer les informations du livreur
- 🏢 **Affecter entreprise** - Assigner le livreur à une entreprise
- 🔑 **Changer mot de passe** - Réinitialiser le mot de passe
- 🔄 **Toggle actif/inactif** - Activer ou désactiver rapidement

### 2. Ajouter un Livreur (`/admin/livreurs/create`)

**Champs requis :**
- Nom complet *
- Email * (unique)
- Téléphone *
- Mot de passe * (min 6 caractères)

**Champs optionnels :**
- Entreprise (peut être affectée plus tard)
- Statut actif (coché par défaut)

### 3. Modifier un Livreur (`/admin/livreurs/{id}/edit`)

**Champs modifiables :**
- Nom complet
- Email
- Téléphone
- Entreprise affectée
- Statut actif/inactif

**Note :** Le mot de passe se change via une page dédiée

### 4. Détails du Livreur (`/admin/livreurs/{id}`)

**Informations affichées :**
- Avatar (initiale du nom)
- Nom, email, téléphone
- Statut (Actif/Inactif)
- Date d'inscription
- Entreprise affectée (si applicable)

**Statistiques de livraison :**
- Total livraisons
- Livraisons ce mois
- Livraisons aujourd'hui
- Livraisons en cours

**Actions rapides :**
- Modifier les informations
- Changer le mot de passe
- Affecter/Retirer une entreprise

### 5. Changer Mot de Passe (`/admin/livreurs/{id}/change-password`)

**Champs :**
- Nouveau mot de passe * (min 6 caractères)
- Confirmation du mot de passe *

**Informations importantes :**
- Le mot de passe doit contenir au moins 6 caractères
- Le livreur devra utiliser ce nouveau mot de passe pour se connecter
- Assurez-vous de communiquer le nouveau mot de passe de manière sécurisée

## 🏢 Affectation d'Entreprise

### Comment affecter une entreprise

**Option 1 : Depuis la liste**
1. Cliquer sur l'icône 🏢 (verte) sur la ligne du livreur
2. Modal s'ouvre avec la liste des entreprises
3. Sélectionner une entreprise
4. Cliquer sur "Affecter"

**Option 2 : Depuis les détails**
1. Aller sur la page détails du livreur
2. Si non affecté : cliquer sur "Affecter une entreprise"
3. Si déjà affecté : cliquer sur "Retirer l'entreprise" puis "Affecter une entreprise"

**Option 3 : Depuis la modification**
1. Éditer le livreur
2. Sélectionner l'entreprise dans la liste déroulante
3. Enregistrer

### Retirer une entreprise

**Depuis les détails du livreur :**
1. Cliquer sur "Retirer l'entreprise"
2. Confirmer l'action

**Depuis la modification :**
1. Éditer le livreur
2. Sélectionner "Aucune" dans la liste des entreprises
3. Enregistrer

## 🎨 Design & Interface

### Couleurs utilisées

- **Gradient livreur** : Orange (#D9542A) → Jaune (#F7B801)
- **Statut actif** : Vert (#10B981)
- **Statut inactif** : Rouge (#EF4444)
- **Entreprise affectée** : Vert clair (#F0FDF4)
- **Non affecté** : Jaune clair (#FEF3C7)

### Icônes des actions

- 👁️ Œil (noir) : Voir détails
- ✏️ Crayon (jaune) : Modifier
- 🏢 Bâtiment (vert) : Affecter entreprise
- 🔑 Clé (violet) : Changer mot de passe
- 🔄 Switch : Activer/Désactiver

## 📁 Structure des fichiers

```
domini/
├── app/Http/Controllers/
│   └── LivreurController.php           (CRUD + affectation)
├── resources/views/admin/livreurs/
│   ├── index.blade.php                 (Liste avec filtres)
│   ├── create.blade.php                (Formulaire création)
│   ├── edit.blade.php                  (Formulaire édition)
│   ├── show.blade.php                  (Détails + stats)
│   └── change-password.blade.php       (Réinitialiser mot de passe)
└── routes/web.php                      (11 routes livreurs)
```

## 🔗 Routes disponibles

| Méthode | URL | Nom | Action |
|---------|-----|-----|--------|
| GET | `/admin/livreurs` | `admin.livreurs.index` | Liste des livreurs |
| GET | `/admin/livreurs/create` | `admin.livreurs.create` | Formulaire création |
| POST | `/admin/livreurs` | `admin.livreurs.store` | Enregistrer nouveau livreur |
| GET | `/admin/livreurs/{id}` | `admin.livreurs.show` | Détails du livreur |
| GET | `/admin/livreurs/{id}/edit` | `admin.livreurs.edit` | Formulaire édition |
| PUT | `/admin/livreurs/{id}` | `admin.livreurs.update` | Mettre à jour livreur |
| PATCH | `/admin/livreurs/{id}/toggle-status` | `admin.livreurs.toggle-status` | Activer/désactiver |
| PATCH | `/admin/livreurs/{id}/affect-entreprise` | `admin.livreurs.affect-entreprise` | Affecter entreprise |
| PATCH | `/admin/livreurs/{id}/remove-entreprise` | `admin.livreurs.remove-entreprise` | Retirer entreprise |
| GET | `/admin/livreurs/{id}/change-password` | `admin.livreurs.change-password` | Formulaire mot de passe |
| PATCH | `/admin/livreurs/{id}/password` | `admin.livreurs.update-password` | Changer mot de passe |

## 🗄️ Base de données

### Table utilisée : `users`

Les livreurs sont des utilisateurs avec `role = 'livreur'`.

**Champs spécifiques aux livreurs :**
- `id_entreprise` (nullable) : Entreprise affectée
- `is_active` (boolean) : Statut actif/inactif
- `telephone` : Numéro de téléphone

**Relation :**
```php
User belongsTo Entreprise (via id_entreprise)
```

## 🚀 Utilisation

### 1. Créer un livreur

```
1. Aller sur /admin/livreurs
2. Cliquer sur "+ Ajouter un livreur"
3. Remplir le formulaire
4. Cliquer sur "Créer le livreur"
```

### 2. Affecter à une entreprise

```
1. Depuis la liste, cliquer sur l'icône 🏢 verte
2. Sélectionner une entreprise
3. Cliquer sur "Affecter"
```

### 3. Activer/Désactiver rapidement

```
1. Dans la liste, utiliser le switch (toggle)
2. Le statut change automatiquement
```

### 4. Voir les détails

```
1. Cliquer sur l'icône 👁️ (œil noir)
2. Voir toutes les informations et statistiques
```

### 5. Modifier les informations

```
1. Cliquer sur l'icône ✏️ (crayon jaune)
2. Modifier les champs
3. Cliquer sur "Enregistrer les modifications"
```

### 6. Changer le mot de passe

```
Option 1 : Depuis la liste
1. Cliquer sur l'icône 🔑 (clé violette)
2. Entrer le nouveau mot de passe
3. Confirmer le mot de passe
4. Cliquer sur "Changer le mot de passe"

Option 2 : Depuis l'édition
1. En bas du formulaire, cliquer sur "🔑 Changer le mot de passe"
2. Suivre les mêmes étapes
```

## 📊 Filtres et Recherche

### Recherche globale
- Recherche dans : nom, email, téléphone
- Mise à jour en temps réel

### Filtrer par statut
- **Tous les statuts** : Affiche tous les livreurs
- **Actifs** : Uniquement les livreurs actifs
- **Inactifs** : Uniquement les livreurs inactifs

### Filtrer par entreprise
- **Toutes entreprises** : Affiche tous les livreurs
- Sélectionner une entreprise spécifique pour voir ses livreurs affectés

## 🔐 Sécurité

### Validation des données

**Création :**
- Email unique (vérifie qu'il n'existe pas déjà)
- Mot de passe minimum 6 caractères
- Téléphone obligatoire

**Modification :**
- Email unique (sauf pour le livreur actuel)
- Validation de l'existence de l'entreprise

**Changement de mot de passe :**
- Minimum 6 caractères
- Confirmation obligatoire

### Contraintes

- Un livreur ne peut être affecté qu'à une seule entreprise à la fois
- L'entreprise doit être active (statut = true)
- Le mot de passe est toujours hashé (Hash::make)

## 📈 Statistiques (À venir)

Les statistiques de livraison seront disponibles après l'implémentation du système de commandes :

- **Total livraisons** : Nombre total de livraisons effectuées
- **Livraisons ce mois** : Livraisons du mois en cours
- **Livraisons aujourd'hui** : Livraisons du jour
- **En cours** : Livraisons actuellement en cours

Pour le moment, toutes les statistiques affichent `0`.

## 💡 Conseils d'utilisation

1. **Créer d'abord les entreprises** avant d'affecter des livreurs
2. **Désactiver plutôt que supprimer** pour garder l'historique
3. **Affecter les livreurs par zone géographique** pour optimiser les livraisons
4. **Communiquer les mots de passe** de manière sécurisée aux livreurs
5. **Vérifier régulièrement** le statut des livreurs actifs/inactifs

## 🎯 Workflow recommandé

### Onboarding d'un nouveau livreur

1. ✅ Créer le compte livreur avec ses informations
2. ✅ Générer un mot de passe temporaire
3. ✅ Communiquer les identifiants de connexion au livreur
4. ✅ Affecter à une entreprise (si déjà connue)
5. ✅ Activer le compte
6. 📱 Le livreur change son mot de passe à la première connexion (à implémenter)

### Changement d'entreprise

1. ✅ Aller sur les détails du livreur
2. ✅ Retirer l'entreprise actuelle
3. ✅ Affecter la nouvelle entreprise
4. ✅ Vérifier la mise à jour

### Désactivation d'un livreur

1. ✅ Depuis la liste, désactiver via le switch
2. ✅ Ou depuis l'édition, décocher "Livreur actif"
3. ✅ Le livreur ne peut plus se connecter
4. ✅ Ses livraisons en cours sont à réaffecter (manuel)

## 🔄 Intégration future

Le système de livreurs est prêt pour l'intégration avec :

- **Système de commandes** : Attribution automatique des livraisons
- **Géolocalisation** : Suivi en temps réel des livreurs
- **Notifications** : Alertes pour nouvelles livraisons
- **Application mobile** : App dédiée aux livreurs
- **Tableau de bord livreur** : Interface pour consulter ses livraisons

## 🎉 Résumé

Le système de gestion des livreurs Domini est maintenant **100% fonctionnel** avec :

- ✅ 11 routes complètes
- ✅ 5 vues professionnelles
- ✅ Filtres et recherche
- ✅ Affectation d'entreprise
- ✅ Gestion des mots de passe
- ✅ Statistiques (placeholder)
- ✅ Design moderne et responsive
- ✅ Intégration dans le layout admin

Tout est prêt pour gérer efficacement vos livreurs ! 🚀
