# Système de Gestion des Boxes, Casiers et Abonnements 📦

## 🎯 Vue d'ensemble

Système complet pour gérer :
1. **Boxes** - Points de livraison avec casiers intelligents
2. **Casiers** - Compartiments individuels avec QR Code
3. **Abonnements** - Souscriptions des entreprises avec gestion de subventions

---

## 📊 Structure des Tables

### Table `boxes`
```sql
- id
- ref (unique) - Exemple: BOX-20260121-A1B2C3
- nom
- id_entreprise (foreign key)
- lat, long (coordonnées GPS)
- adresse
- capacite (nombre de casiers)
- est_actif (boolean)
- timestamps
```

### Table `casiers`
```sql
- id
- ref (unique) - Exemple: BOX-20260121-A1B2C3-C001
- qr_code (unique) - Exemple: QR-65A9B2C3D4E5F6
- id_box (foreign key)
- id_employe (foreign key, nullable)
- statut (libre, occupe, reserve, hors_service)
- numero_casier
- timestamps
```

### Table `abonnements`
```sql
- id
- id_entreprise (foreign key)
- representant (nom du contact)
- numero (téléphone)
- fonction
- status (actif, suspendu, resilie, expire)
- statut_subvention_commande (totale, partielle, aucune)
- pourcentage (0-100)
- nbre_employe
- date_debut, date_fin
- date_resiliation, raison_resiliation
- timestamps
```

---

## 🎨 Fonctionnalités Abonnements

### Actions disponibles

#### 1. **Résilier** ❌
- Changer le status à 'resilie'
- Enregistrer la date et raison
- Modal avec formulaire de raison obligatoire

#### 2. **Suspendre** ⏸️
- Changer le status à 'suspendu'
- Conserver les dates de validité
- Peut être réactivé

#### 3. **Réactiver** ✅
- Changer le status à 'actif'
- Seulement si non expiré
- Vérification automatique

#### 4. **Renouveler** 🔄
- Créer une nouvelle période
- Choix de la durée (3, 6, 12, 24, 36 mois)
- Calcul automatique des dates

---

## 📦 Fonctionnalités Boxes

### Création d'une Box
1. Renseigner les informations
2. Définir le nombre de casiers
3. **Génération automatique** de tous les casiers

### Génération des Casiers
```php
// Génération automatique lors de la création
for ($i = 1; $i <= $capacite; $i++) {
    Casier::create([
        'ref' => 'BOX-XXX-C' . str_pad($i, 3, '0', STR_PAD_LEFT),
        'qr_code' => 'QR-' . strtoupper(uniqid()),
        'numero_casier' => $i,
        'statut' => 'libre'
    ]);
}
```

### Ajout de Casiers
- Fonction pour ajouter des casiers supplémentaires
- Numérotation automatique continue
- Génération de nouvelles références et QR codes

---

## 🎯 Vue Abonnements

### Stats en Haut
- Total abonnements
- Actifs (vert)
- Suspendus (jaune)
- Résiliés (rouge)

### Filtres
- Par statut (actif, suspendu, résilié, expiré)
- Par entreprise

### Tableau
Colonnes :
- Entreprise + Ville
- Représentant + Fonction
- Contact
- Nombre d'employés
- Subvention (100%, X%, Aucune)
- Période (dates + jours restants)
- Statut (badge coloré)
- Actions (4-5 boutons selon statut)

### Boutons d'Actions
```
Actif:      👁️ 📝 ⏸️ ❌
Suspendu:   👁️ ✅
Résilie:    👁️ 🔄
Expiré:     👁️ 🔄
```

---

## 📋 Routes Nécessaires

```php
// Abonnements
Route::prefix('abonnements')->name('abonnements.')->group(function () {
    Route::get('/', [AbonnementController::class, 'index'])->name('index');
    Route::get('/create', [AbonnementController::class, 'create'])->name('create');
    Route::post('/', [AbonnementController::class, 'store'])->name('store');
    Route::get('/{abonnement}', [AbonnementController::class, 'show'])->name('show');
    Route::get('/{abonnement}/edit', [AbonnementController::class, 'edit'])->name('edit');
    Route::patch('/{abonnement}', [AbonnementController::class, 'update'])->name('update');
    Route::patch('/{abonnement}/resilier', [AbonnementController::class, 'resilier'])->name('resilier');
    Route::patch('/{abonnement}/suspendre', [AbonnementController::class, 'suspendre'])->name('suspendre');
    Route::patch('/{abonnement}/reactiver', [AbonnementController::class, 'reactiver'])->name('reactiver');
    Route::patch('/{abonnement}/renouveler', [AbonnementController::class, 'renouveler'])->name('renouveler');
});

// Boxes
Route::prefix('boxes')->name('boxes.')->group(function () {
    Route::get('/', [BoxController::class, 'index'])->name('index');
    Route::get('/create', [BoxController::class, 'create'])->name('create');
    Route::post('/', [BoxController::class, 'store'])->name('store');
    Route::get('/{box}', [BoxController::class, 'show'])->name('show');
    Route::get('/{box}/edit', [BoxController::class, 'edit'])->name('edit');
    Route::patch('/{box}', [BoxController::class, 'update'])->name('update');
    Route::delete('/{box}', [BoxController::class, 'destroy'])->name('destroy');
    Route::patch('/{box}/toggle', [BoxController::class, 'toggleStatus'])->name('toggle');
    Route::post('/{box}/casiers', [BoxController::class, 'ajouterCasiers'])->name('casiers.ajouter');
});
```

---

## ✅ Fichiers Créés

### Migrations
- ✅ `2026_01_21_011323_create_boxes_table.php`
- ✅ `2026_01_21_011335_create_casiers_table.php`
- ✅ `2026_01_21_011341_create_abonnements_table.php`

### Modèles
- ✅ `app/Models/Box.php`
- ✅ `app/Models/Casier.php`
- ✅ `app/Models/Abonnement.php`

### Controllers
- ✅ `app/Http/Controllers/BoxController.php`
- ✅ `app/Http/Controllers/AbonnementController.php`

### Vues
- ✅ `resources/views/admin/abonnements/index.blade.php`

---

## 🚀 Pour Activer

### 1. Lancer les migrations
```bash
php artisan migrate
```

### 2. Ajouter les routes dans `web.php`
Voir section "Routes Nécessaires" ci-dessus

### 3. Ajouter dans la sidebar (`admin.layout.php`)
```html
<a href="{{ route('admin.abonnements.index') }}">
    📋 Abonnements
</a>
<a href="{{ route('admin.boxes.index') }}">
    📦 Boxes & Casiers
</a>
```

---

## 📊 Exemple d'Utilisation

### Créer une Box avec 50 casiers
1. Aller sur `/admin/boxes/create`
2. Remplir le formulaire
3. Capacité: 50
4. → 50 casiers générés automatiquement !

### Créer un Abonnement
1. Aller sur `/admin/abonnements/create`
2. Sélectionner l'entreprise
3. Définir:
   - Représentant
   - Contact
   - Type de subvention
   - Pourcentage
   - Nombre d'employés
   - Durée

### Gérer un Abonnement

**Suspendre** (vacances, problème de paiement):
- Cliquer sur ⏸️
- Confirmer
- Status → Suspendu

**Réactiver**:
- Cliquer sur ✅
- Status → Actif

**Résilier** (annulation définitive):
- Cliquer sur ❌
- Saisir la raison
- Confirmer
- Status → Résilié

**Renouveler** (avant expiration ou après):
- Cliquer sur 🔄
- Choisir la durée
- Nouvelles dates calculées automatiquement

---

## 🎨 Design

### Couleurs des Statuts

**Abonnements:**
- Actif: Vert #10B981
- Suspendu: Jaune #F7B801
- Résilié: Rouge #EF4444
- Expiré: Gris #999

**Casiers:**
- Libre: Vert #10B981
- Occupé: Rouge #EF4444
- Réservé: Jaune #F7B801
- Hors service: Gris #999

---

## 📝 Vues Restantes à Créer

Pour compléter le système:

1. **Abonnements**
   - ✅ `index.blade.php` (Créé)
   - ⏳ `create.blade.php`
   - ⏳ `edit.blade.php`
   - ⏳ `show.blade.php`

2. **Boxes**
   - ⏳ `index.blade.php`
   - ⏳ `create.blade.php`
   - ⏳ `edit.blade.php`
   - ⏳ `show.blade.php` (avec liste des casiers)

---

**🎉 Système de base créé et fonctionnel !**

Pour continuer, ajouter les routes dans `web.php` et créer les vues restantes selon le même modèle que `abonnements/index.blade.php`.
