# Fix: Gestion des champs JSON dans Laravel

## 🐛 Problème rencontré

### Erreur
```
count(): Argument #1 ($value) must be of type Countable|array, string given
```

### Cause
Les champs JSON (`accompagnements`, `options`) dans la table `item_commandes` étaient mal gérés :

1. **Dans le seeder** : Utilisation de `json_encode()` manuel
2. **Dans le modèle** : Cast automatique en `'array'`
3. **Résultat** : Double encodage → Laravel recevait une string au lieu d'un array

## ✅ Solution appliquée

### 1. Modèle `ItemCommande.php`

Le modèle définit correctement les casts :

```php
protected $casts = [
    'accompagnements' => 'array',
    'options' => 'array',
    'prix' => 'decimal:2',
    'is_subventionne' => 'boolean',
];
```

**Important** : Avec ces casts, Laravel gère automatiquement l'encodage/décodage JSON. **Ne pas utiliser `json_encode()`** manuellement !

### 2. Seeder `CommandeSeeder.php`

**❌ Avant (Incorrect):**
```php
ItemCommande::create([
    'accompagnements' => json_encode([
        ['nom' => 'Alloco', 'quantite' => 1]
    ]),
    'options' => json_encode([
        ['nom' => 'Sauce pimentée', 'quantite' => 1]
    ]),
]);
```

**✅ Après (Correct):**
```php
ItemCommande::create([
    'accompagnements' => [
        ['nom' => 'Alloco', 'quantite' => 1]
    ],
    'options' => [
        ['nom' => 'Sauce pimentée', 'quantite' => 1]
    ],
]);
```

### 3. Vues Blade

Utilisation sécurisée avec vérification de type :

**❌ Avant (Fragile):**
```php
@if($item->accompagnements && count($item->accompagnements) > 0)
    {{ implode(', ', array_column($item->accompagnements, 'nom')) }}
@endif
```

**✅ Après (Robuste):**
```php
@if(is_array($item->accompagnements) && !empty($item->accompagnements))
    {{ implode(', ', array_column($item->accompagnements, 'nom')) }}
@endif
```

### Vues corrigées :
- `resources/views/admin/commandes/show.blade.php`
- `resources/views/admin/livraisons/show.blade.php`

## 📝 Règles à suivre

### ✅ À FAIRE

1. **Définir les casts dans le modèle** pour les champs JSON
2. **Passer des tableaux PHP directement** aux modèles
3. **Utiliser `is_array()` et `!empty()`** dans les vues pour vérifier
4. **Laisser Laravel gérer** l'encodage/décodage automatiquement

### ❌ À NE PAS FAIRE

1. **N'utilisez JAMAIS `json_encode()`** quand un cast `'array'` est défini
2. **N'utilisez JAMAIS `json_decode()`** quand vous récupérez les données
3. **Ne faites pas `count()` sans vérifier** que c'est bien un tableau

## 🔄 Migration des données existantes

Si vous avez des données créées avec l'ancien système (json_encode manuel) :

```bash
php artisan migrate:fresh --seed
```

⚠️ **ATTENTION** : Cette commande supprime TOUTES les données !

## 💡 Exemple complet

### Créer un item de commande

```php
use App\Models\ItemCommande;

ItemCommande::create([
    'commande_id' => 1,
    'plat_id' => 5,
    'accompagnements' => [  // Tableau PHP, pas json_encode !
        ['nom' => 'Frites', 'quantite' => 1],
        ['nom' => 'Salade', 'quantite' => 1],
    ],
    'options' => [
        ['nom' => 'Sauce mayo', 'quantite' => 2],
    ],
    'prix' => 2500,
    'quantite' => 1,
]);
```

### Récupérer et utiliser

```php
$item = ItemCommande::find(1);

// C'est automatiquement un tableau !
foreach ($item->accompagnements as $acc) {
    echo $acc['nom']; // Accès direct, pas besoin de json_decode
}
```

### Dans les vues Blade

```php
@if(is_array($item->accompagnements) && !empty($item->accompagnements))
    <ul>
        @foreach($item->accompagnements as $acc)
            <li>{{ $acc['nom'] }} (x{{ $acc['quantite'] }})</li>
        @endforeach
    </ul>
@endif
```

## 🎯 Vérification

Pour vérifier que tout fonctionne :

1. Créez une commande de test
2. Ajoutez des accompagnements et options
3. Vérifiez dans la vue détails de la commande
4. Pas d'erreur = ✅ Tout fonctionne !

## 📚 Référence Laravel

Documentation officielle sur les casts :
https://laravel.com/docs/11.x/eloquent-mutators#array-and-json-casting

---

**Date de correction** : 20 Janvier 2026  
**Fichiers modifiés** :
- `database/seeders/CommandeSeeder.php`
- `resources/views/admin/commandes/show.blade.php`
- `resources/views/admin/livraisons/show.blade.php`
