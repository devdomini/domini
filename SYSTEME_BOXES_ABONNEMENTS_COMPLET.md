# 📦 Système Complet de Boxes, Casiers et Abonnements - Domini

## Vue d'ensemble

Ce système gère les boxes de livraison intelligentes, leurs casiers, et les abonnements des entreprises partenaires. Il permet une gestion complète des points de livraison physiques et des contrats d'abonnement.

---

## 🏗️ Architecture des Boxes & Casiers

### Modèle de Données

#### Table `boxes`
```sql
- id (PK)
- ref (unique) : Référence automatique "BOX-YYYYMMDD-XXXXXX"
- nom : Nom de la box
- id_entreprise (FK) : Entreprise propriétaire
- lat : Latitude GPS
- long : Longitude GPS
- adresse : Adresse physique
- capacite : Nombre total de casiers
- est_actif : Statut actif/inactif
- timestamps
```

#### Table `casiers`
```sql
- id (PK)
- ref (unique) : Référence "BOX-REF-C001"
- qr_code (unique) : Code QR unique "QR-XXXXXXXXXXXX"
- id_box (FK) : Box parente
- id_employe (FK, nullable) : Employé assigné
- statut : libre|occupe|reserve|hors_service
- numero_casier : Numéro du casier dans la box (1, 2, 3...)
- timestamps
```

### Fonctionnalités Boxes

#### Création de Box
1. **Route** : `GET /admin/boxes/create`
2. **Champs requis** :
   - Nom de la box
   - Entreprise
   - Capacité (1-200 casiers)
   - Adresse (optionnel)
   - Coordonnées GPS (optionnel)

3. **Génération automatique** :
   - Référence unique
   - Création de tous les casiers
   - QR codes uniques pour chaque casier

#### Liste des Boxes
- **Route** : `GET /admin/boxes`
- **Affichage** :
  - Informations de la box
  - Statistiques en temps réel
  - Total, libres, occupés, taux d'occupation
  - Actions : Voir, Modifier, Activer/Désactiver, Supprimer, Ajouter casiers

#### Détails d'une Box
- **Route** : `GET /admin/boxes/{box}`
- **Affichage** :
  - Informations complètes
  - Statistiques détaillées
  - Liste complète des casiers avec filtres
  - Recherche par numéro, référence ou QR code
  - Visualisation des QR codes

#### Ajout de Casiers Supplémentaires
- **Route** : `POST /admin/boxes/{box}/casiers`
- **Paramètres** :
  - nombre : 1-50 casiers
- **Comportement** :
  - Génération automatique des nouveaux casiers
  - Incrémentation de la capacité
  - Numéros séquentiels automatiques

---

## 📋 Architecture des Abonnements

### Modèle de Données

#### Table `abonnements`
```sql
- id (PK)
- id_entreprise (FK) : Entreprise abonnée
- representant : Nom du représentant
- numero : Téléphone du représentant
- fonction : Fonction dans l'entreprise
- status : actif|suspendu|resilie|expire
- statut_subvention_commande : totale|partielle|aucune
- pourcentage : Pourcentage de subvention (0-100%)
- nbre_employe : Nombre d'employés couverts
- date_debut : Date de début
- date_fin : Date de fin
- date_resiliation : Date de résiliation (nullable)
- raison_resiliation : Raison de la résiliation (nullable)
- timestamps
```

### Fonctionnalités Abonnements

#### Création d'Abonnement
- **Route** : `GET /admin/abonnements/create`
- **Champs requis** :
  - Entreprise
  - Représentant (nom, téléphone, fonction)
  - Type de subvention (totale, partielle, aucune)
  - Pourcentage si partielle
  - Nombre d'employés
  - Durée en mois

- **Calcul automatique** :
  - Date de début : Date actuelle
  - Date de fin : Date début + durée

#### Liste des Abonnements
- **Route** : `GET /admin/abonnements`
- **Statistiques** :
  - Total abonnements
  - Actifs
  - Suspendus
  - Résiliés
  
- **Filtres** :
  - Par statut
  - Par entreprise

- **Affichage tableau** :
  - Entreprise et localisation
  - Représentant et contact
  - Nombre d'employés
  - Subvention (pourcentage)
  - Période (dates + jours restants)
  - Statut coloré
  - Actions contextuelles

#### Actions sur Abonnement

##### 1. Suspendre
- **Route** : `PATCH /admin/abonnements/{abonnement}/suspendre`
- **Disponible pour** : Abonnements actifs
- **Effet** : Change le statut à "suspendu"

##### 2. Réactiver
- **Route** : `PATCH /admin/abonnements/{abonnement}/reactiver`
- **Disponible pour** : Abonnements suspendus
- **Condition** : Ne doit pas être expiré
- **Effet** : Change le statut à "actif"

##### 3. Résilier
- **Route** : `PATCH /admin/abonnements/{abonnement}/resilier`
- **Disponible pour** : Abonnements actifs
- **Champs requis** : Raison de résiliation
- **Effet** :
  - Change le statut à "résilié"
  - Enregistre la date et la raison

##### 4. Renouveler
- **Route** : `PATCH /admin/abonnements/{abonnement}/renouveler`
- **Disponible pour** : Abonnements expirés, résiliés ou proches de l'expiration
- **Paramètres** :
  - duree_mois : 3, 6, 12, 24, 36 mois
- **Effet** :
  - Nouvelle date_debut = date_fin actuelle + 1 jour
  - Nouvelle date_fin = date_debut + durée
  - Statut = actif
  - Efface date_resiliation et raison_resiliation

---

## 🎨 Design System

### Codes Couleurs

#### Statuts Box
```css
Actif : #E8F5E9 / #2d9248 (vert)
Inactif : #F5F5F5 / #666 (gris)
```

#### Statuts Casiers
```css
Libre : #E8F5E9 / #2d9248 (vert)
Occupé : #FFEBEE / #C62828 (rouge)
Réservé : #FFF3E0 / #E65100 (orange)
Hors service : #F5F5F5 / #666 (gris)
```

#### Statuts Abonnements
```css
Actif : #E8F5E9 / #2d9248 (vert)
Suspendu : #FFF3E0 / #E65100 (orange)
Résilié : #FFEBEE / #C62828 (rouge)
Expiré : #F5F5F5 / #666 (gris)
```

#### Cards KPI (Gradients)
```css
Noir : linear-gradient(135deg, #3A3A3A, #2A2A2A)
Vert : linear-gradient(135deg, #10B981, #059669)
Rouge : linear-gradient(135deg, #EF4444, #DC2626)
Jaune : linear-gradient(135deg, #F7B801, #e5a900)
Orange : linear-gradient(135deg, #D9542A, #c13d18)
```

### Icônes SVG Utilisées

#### Box
```svg
<path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
```

#### QR Code
```svg
<path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
```

#### Localisation
```svg
<path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
<path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
```

#### Suspendre
```svg
<path d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
```

#### Résilier
```svg
<path d="M6 18L18 6M6 6l12 12"/>
```

#### Renouveler
```svg
<path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
```

#### Réactiver
```svg
<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
```

---

## 🔐 Génération des QR Codes

### Bibliothèque Utilisée
**QRCode.js** - Intégration CDN :
```html
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
```

### Implémentation
```javascript
new QRCode(container, {
    text: qrCodeValue,
    width: 256,
    height: 256,
    colorDark: "#000000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H // Haute correction d'erreur
});
```

### Affichage des QR Codes
- **Modal** : Affichage grand format (256x256px)
- **Informations** :
  - Référence du casier
  - Valeur du QR code
  - QR code visuel haute qualité

---

## 🔄 Méthodes des Modèles

### Box Model

```php
// Relations
entreprise() : BelongsTo
casiers() : HasMany

// Méthodes utilitaires
casiersLibres() : int
casiersOccupes() : int
generateRef() : string
```

### Casier Model

```php
// Relations
box() : BelongsTo
employe() : BelongsTo

// Méthodes utilitaires
estDisponible() : bool
generateRef($boxRef, $numero) : string
generateQRCode() : string
```

### Abonnement Model

```php
// Relations
entreprise() : BelongsTo

// Méthodes utilitaires
estActif() : bool
estExpire() : bool
joursRestants() : int

// Actions
resilier($raison) : void
suspendre() : void
reactiver() : void
renouveler($duree_mois) : void
```

---

## 📡 Routes API

### Boxes & Casiers

```php
GET    /admin/boxes                         // Liste des boxes
GET    /admin/boxes/create                  // Formulaire création
POST   /admin/boxes                         // Créer une box
GET    /admin/boxes/{box}                   // Détails d'une box
GET    /admin/boxes/{box}/edit              // Formulaire modification
PATCH  /admin/boxes/{box}                   // Mettre à jour
DELETE /admin/boxes/{box}                   // Supprimer
PATCH  /admin/boxes/{box}/toggle            // Activer/Désactiver
POST   /admin/boxes/{box}/casiers           // Ajouter des casiers
```

### Abonnements

```php
GET    /admin/abonnements                   // Liste des abonnements
GET    /admin/abonnements/create            // Formulaire création
POST   /admin/abonnements                   // Créer un abonnement
GET    /admin/abonnements/{id}              // Détails
GET    /admin/abonnements/{id}/edit         // Formulaire modification
PATCH  /admin/abonnements/{id}              // Mettre à jour
PATCH  /admin/abonnements/{id}/suspendre    // Suspendre
PATCH  /admin/abonnements/{id}/reactiver    // Réactiver
PATCH  /admin/abonnements/{id}/resilier     // Résilier
PATCH  /admin/abonnements/{id}/renouveler   // Renouveler
```

---

## 🧪 Tests & Validation

### Validation Boxes
```php
'nom' => 'required|string|max:255'
'id_entreprise' => 'required|exists:entreprises,id'
'capacite' => 'required|integer|min:1|max:200'
'adresse' => 'nullable|string'
'lat' => 'nullable|numeric'
'long' => 'nullable|numeric'
```

### Validation Abonnements
```php
'id_entreprise' => 'required|exists:entreprises,id'
'representant' => 'required|string|max:255'
'numero' => 'required|string|max:20'
'fonction' => 'required|string|max:255'
'statut_subvention_commande' => 'required|in:totale,partielle,aucune'
'pourcentage' => 'required|numeric|min:0|max:100'
'nbre_employe' => 'required|integer|min:1'
'duree_mois' => 'required|integer|min:1|max:36'
```

---

## 🎯 Fonctionnalités Clés

### Recherche & Filtrage

#### Boxes - Page Détails
- Recherche par :
  - Numéro de casier
  - Référence
  - QR code
- Filtre par statut :
  - Libre
  - Occupé
  - Réservé
  - Hors service

#### Abonnements
- Filtre par statut
- Filtre par entreprise

### Statistiques en Temps Réel

#### Box
- Total casiers
- Casiers libres
- Casiers occupés
- Taux d'occupation (%)

#### Abonnements
- Total abonnements
- Actifs
- Suspendus
- Résiliés

---

## 📱 Responsive Design

### Points de rupture
```css
Desktop : > 1024px
Tablet : 768px - 1024px
Mobile : < 768px
```

### Adaptations Mobile
- Grilles en colonne unique
- Actions en stack vertical
- Modals plein écran
- Navigation simplifiée

---

## ✅ Checklist d'Implémentation

- [x] Migrations créées (boxes, casiers, abonnements)
- [x] Modèles avec relations
- [x] Contrôleurs complets
- [x] Routes définies
- [x] Vues index, create, show, edit
- [x] Design cohérent avec l'admin
- [x] Icônes SVG (pas d'emojis)
- [x] QR codes fonctionnels
- [x] Statistiques en temps réel
- [x] Filtres et recherche
- [x] Actions contextuelles
- [x] Modals interactives
- [x] Validation des données
- [x] Messages de succès/erreur
- [x] Pagination personnalisée
- [x] Responsive design

---

## 🚀 Utilisation

### Créer une nouvelle Box

1. Aller dans **Boxes** > **Nouvelle Box**
2. Remplir les informations
3. Sélectionner l'entreprise
4. Définir la capacité (nombre de casiers)
5. Soumettre : Les casiers sont générés automatiquement

### Créer un Abonnement

1. Aller dans **Abonnements** > **Nouvel Abonnement**
2. Sélectionner l'entreprise
3. Renseigner le représentant
4. Choisir le type de subvention
5. Définir le nombre d'employés et la durée
6. Soumettre

### Visualiser un QR Code

1. Aller dans les détails d'une box
2. Cliquer sur l'icône QR code d'un casier
3. Le QR code s'affiche en grand format
4. Peut être scanné ou sauvegardé

---

## 🔧 Configuration

### Limites Système
- Capacité maximale par box : **200 casiers**
- Ajout simultané maximum : **50 casiers**
- Durée abonnement maximum : **36 mois**
- Pagination : **10 éléments par page**

### Permissions Requises
- Rôle : **Admin**
- Middleware : **auth**

---

## 📚 Documentation Complémentaire

- [BOXES_ABONNEMENTS_SYSTEM.md](./BOXES_ABONNEMENTS_SYSTEM.md) - Setup initial
- [PAGINATION_PERSONNALISEE.md](./PAGINATION_PERSONNALISEE.md) - Système de pagination
- [DASHBOARD_GRAPHIQUES.md](./DASHBOARD_GRAPHIQUES.md) - Dashboard admin

---

✅ **Système Boxes, Casiers & Abonnements fonctionnel et prêt à l'emploi !**
