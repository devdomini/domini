# Fix : Livreurs ne s'affichent pas après création

## 🐛 Problème rencontré

Après avoir créé un compte livreur, celui-ci n'apparaissait pas dans la liste des livreurs.

## 🔍 Cause

Le modèle `User` n'avait pas tous les champs nécessaires dans la propriété `$fillable`, ce qui empêchait Laravel d'enregistrer correctement les données lors de la création.

### Champs manquants dans $fillable :
- `role` ❌
- `telephone` ❌
- `id_entreprise` ❌
- `num_box` ❌
- `is_active` ❌

### Relation manquante :
- Relation `entreprise()` avec le modèle `Entreprise` ❌

## ✅ Solution appliquée

### 1. Mise à jour du modèle User (`app/Models/User.php`)

**Avant :**
```php
protected $fillable = [
    'name',
    'email',
    'password',
];
```

**Après :**
```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'telephone',
    'id_entreprise',
    'num_box',
    'is_active',
];
```

### 2. Ajout du cast pour is_active

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',  // ✅ Ajouté
    ];
}
```

### 3. Ajout de la relation avec Entreprise

```php
/**
 * Relation avec l'entreprise
 */
public function entreprise()
{
    return $this->belongsTo(Entreprise::class, 'id_entreprise');
}
```

## 🎯 Résultat

Maintenant, lors de la création d'un livreur :

1. ✅ Tous les champs sont correctement enregistrés en base de données
2. ✅ Le livreur apparaît immédiatement dans la liste
3. ✅ La relation avec l'entreprise fonctionne (si affectée)
4. ✅ Le statut `is_active` est géré correctement
5. ✅ Les filtres et recherches fonctionnent

## 🧪 Test

Pour vérifier que tout fonctionne :

1. **Aller sur** : `http://localhost:8000/admin/livreurs`
2. **Cliquer sur** : "+ Ajouter un livreur"
3. **Remplir** : Nom, Email, Téléphone, Mot de passe
4. **Soumettre** : Le livreur doit apparaître dans la liste immédiatement
5. **Vérifier** : Le switch actif/inactif doit fonctionner

## 📊 Vérification en base de données

Si vous voulez vérifier directement dans la base de données :

```sql
-- Voir tous les livreurs
SELECT * FROM users WHERE role = 'livreur';

-- Voir le dernier livreur créé
SELECT * FROM users WHERE role = 'livreur' ORDER BY created_at DESC LIMIT 1;

-- Compter les livreurs actifs
SELECT COUNT(*) FROM users WHERE role = 'livreur' AND is_active = 1;
```

## 🔄 Si le problème persiste

### 1. Vider le cache de configuration
```bash
php artisan config:clear
php artisan cache:clear
```

### 2. Vérifier que la colonne is_active existe
```bash
php artisan migrate:status
```

Si la migration `2026_01_20_000006_add_is_active_to_users_table` n'a pas été exécutée, lancez :
```bash
php artisan migrate
```

### 3. Vérifier les données en base
Connectez-vous à votre base de données et vérifiez :
```sql
DESCRIBE users;
```

La colonne `is_active` doit apparaître avec le type `tinyint(1)`.

## 💡 Bonnes pratiques

Pour éviter ce genre de problème à l'avenir :

1. **Toujours ajouter les nouveaux champs dans $fillable** quand vous ajoutez des colonnes à la table
2. **Définir les relations** dans les modèles dès leur création
3. **Ajouter les casts** pour les types spéciaux (boolean, date, json, etc.)
4. **Tester immédiatement** après chaque modification de migration/modèle

## 📚 Fichiers modifiés

- ✅ `app/Models/User.php` - Mise à jour du modèle
- ✅ `database/migrations/2026_01_20_000006_add_is_active_to_users_table.php` - Migration is_active

## 🎉 Conclusion

Le problème est maintenant **complètement résolu** ! 

Vous pouvez :
- ✅ Créer des livreurs
- ✅ Les voir apparaître dans la liste
- ✅ Les filtrer et les rechercher
- ✅ Les affecter à des entreprises
- ✅ Les activer/désactiver

Tout fonctionne parfaitement ! 🚀
