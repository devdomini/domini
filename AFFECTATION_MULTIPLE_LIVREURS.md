# Affectation Multiple d'Entreprises aux Livreurs

## 🎯 Nouvelle Fonctionnalité

Le système permet maintenant d'**affecter plusieurs entreprises à un ou plusieurs livreurs** grâce à une relation **many-to-many**.

## 🆕 Avant vs Après

### ❌ Avant (One-to-One)
- Un livreur = UNE seule entreprise
- Champ `id_entreprise` dans la table `users`
- Limitation : Un livreur ne pouvait livrer que pour une entreprise

### ✅ Après (Many-to-Many)
- Un livreur = PLUSIEURS entreprises
- Table pivot `entreprise_livreur`
- Flexibilité : Un livreur peut livrer pour plusieurs entreprises

## 📊 Architecture

### Table Pivot `entreprise_livreur`

```sql
CREATE TABLE entreprise_livreur (
    id BIGINT PRIMARY KEY,
    entreprise_id BIGINT,
    user_id BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(entreprise_id, user_id)
);
```

### Relations

**User (Livreur) ↔ Entreprise**
```php
// app/Models/User.php
public function entreprises() {
    return $this->belongsToMany(Entreprise::class, 'entreprise_livreur');
}

// app/Models/Entreprise.php
public function livreurs() {
    return $this->belongsToMany(User::class, 'entreprise_livreur');
}
```

## 🚀 Utilisation

### 1. Affecter plusieurs entreprises depuis la liste

**Étapes :**
1. Aller sur `/admin/livreurs`
2. Cliquer sur l'icône 🏢 (verte) "Gérer entreprises"
3. **Modal s'ouvre** avec la liste de toutes les entreprises
4. **Cocher** une ou plusieurs entreprises
5. Cliquer sur **"Enregistrer"**

**Résultat :** Les entreprises cochées remplacent les affectations précédentes (sync)

### 2. Gérer les entreprises depuis les détails

**Étapes :**
1. Aller sur `/admin/livreurs/{id}`
2. Section "Entreprises affectées" affiche toutes les entreprises
3. Options disponibles :
   - **"+ Gérer"** : Ouvrir modal pour modifier les affectations
   - **"Retirer"** : Retirer UNE entreprise spécifique
   - **"Retirer toutes les entreprises"** : Retirer TOUTES les affectations

### 3. Affichage dans la liste

La colonne "Entreprise" affiche maintenant :
- **Plusieurs badges** si plusieurs entreprises affectées
- **"Non affecté"** si aucune entreprise

## 🎨 Interface

### Modal de sélection multiple

```
┌─────────────────────────────────────┐
│  Gérer les entreprises              │
├─────────────────────────────────────┤
│  Livreur: Jean Kouassi              │
│                                     │
│  Sélectionnez les entreprises *     │
│  ┌──────────────────────────────┐  │
│  │ ☑ Entreprise A               │  │
│  │   Abidjan, Côte d'Ivoire     │  │
│  │                              │  │
│  │ ☑ Entreprise B               │  │
│  │   Yamoussoukro, CI           │  │
│  │                              │  │
│  │ ☐ Entreprise C               │  │
│  │   Bouaké, Côte d'Ivoire      │  │
│  └──────────────────────────────┘  │
│                                     │
│  [Enregistrer]  [Annuler]          │
└─────────────────────────────────────┘
```

### Page détails

```
Entreprises affectées (2)      [+ Gérer]

┌─────────────────────────────────────┐
│ Entreprise A               [Retirer]│
│ 📍 Abidjan, Côte d'Ivoire          │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ Entreprise B               [Retirer]│
│ 📍 Yamoussoukro, Côte d'Ivoire     │
└─────────────────────────────────────┘

[Retirer toutes les entreprises]
```

## 🔄 Méthodes du Controller

### `affectEntreprises(Request, $id)`
Affecter plusieurs entreprises à un livreur

**Paramètres :**
- `entreprises[]` (array) : IDs des entreprises à affecter

**Action :** `sync()` - Remplace toutes les affectations existantes

**Retour :** Message "X entreprise(s) affectée(s) avec succès"

### `removeEntreprise(Request, $id)`
Retirer UNE entreprise spécifique

**Paramètres :**
- `entreprise_id` : ID de l'entreprise à retirer

**Action :** `detach($entreprise_id)` - Retire une seule affectation

**Retour :** Message "Entreprise retirée avec succès"

### `removeAllEntreprises($id)`
Retirer TOUTES les entreprises

**Action :** `detach()` - Retire toutes les affectations

**Retour :** Message "Toutes les entreprises ont été retirées"

## 🗺️ Routes

| Méthode | URL | Action |
|---------|-----|--------|
| POST | `/admin/livreurs/{id}/affect-entreprises` | Affecter plusieurs entreprises |
| DELETE | `/admin/livreurs/{id}/remove-entreprise` | Retirer une entreprise |
| DELETE | `/admin/livreurs/{id}/remove-all-entreprises` | Retirer toutes les entreprises |

## 📝 Validation

### Affecter entreprises

```php
'entreprises' => 'required|array|min:1',
'entreprises.*' => 'exists:entreprises,id',
```

**Messages d'erreur :**
- "Veuillez sélectionner au moins une entreprise."
- "Une des entreprises sélectionnées n'existe pas."

### Retirer entreprise

```php
'entreprise_id' => 'required|exists:entreprises,id',
```

## 💡 Cas d'usage

### Scénario 1 : Livreur multi-zones

**Situation :** Un livreur livre pour 3 entreprises dans différentes zones

**Solution :**
1. Créer le livreur
2. Affecter les 3 entreprises via le modal
3. Le livreur apparaît dans toutes ses entreprises

### Scénario 2 : Réorganisation

**Situation :** Un livreur change de zones

**Solution :**
1. Ouvrir "Gérer entreprises"
2. Décocher anciennes entreprises
3. Cocher nouvelles entreprises
4. Enregistrer (remplacement automatique)

### Scénario 3 : Retrait progressif

**Situation :** Retirer un livreur d'une entreprise spécifique

**Solution :**
1. Aller sur détails du livreur
2. Cliquer sur "Retirer" pour l'entreprise concernée
3. Les autres entreprises restent affectées

### Scénario 4 : Désaffectation totale

**Situation :** Livreur en congé ou inactif

**Solution :**
1. Aller sur détails du livreur
2. Cliquer sur "Retirer toutes les entreprises"
3. Le livreur devient "Non affecté"

## 🔍 Filtrage

Le filtre par entreprise fonctionne avec la relation many-to-many :

```php
$query->whereHas('entreprises', function($q) use ($entrepriseId) {
    $q->where('entreprises.id', $entrepriseId);
});
```

**Résultat :** Affiche tous les livreurs affectés à cette entreprise (parmi d'autres potentiellement)

## 🎨 Design

### Badges dans la liste

```html
<div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
    <span class="badge">Entreprise A</span>
    <span class="badge">Entreprise B</span>
    <span class="badge">Entreprise C</span>
</div>
```

### Checkboxes dans le modal

- ✅ **Cochée** : Entreprise actuellement affectée
- ☐ **Non cochée** : Entreprise non affectée
- Hover effect sur chaque ligne
- Scrollable si beaucoup d'entreprises

## 🔐 Sécurité

### Contrainte d'unicité

```sql
UNIQUE(entreprise_id, user_id)
```

**Protection :** Empêche les doublons dans la table pivot

### Cascade on delete

```php
->onDelete('cascade')
```

**Comportement :**
- Si entreprise supprimée → Affectations supprimées
- Si livreur supprimé → Affectations supprimées

## 📊 Statistiques

### Pour un livreur

```php
$livreur->entreprises->count(); // Nombre d'entreprises
```

### Pour une entreprise

```php
$entreprise->livreurs->count(); // Nombre de livreurs
```

### Livreurs par entreprise

```php
$entreprise->livreurs()
    ->where('is_active', true)
    ->count(); // Livreurs actifs
```

## 🧪 Tests

### Test 1 : Affecter 3 entreprises à 1 livreur

1. Créer un livreur
2. Ouvrir modal "Gérer entreprises"
3. Cocher 3 entreprises
4. Enregistrer
5. ✅ Vérifier : 3 badges affichés dans la liste

### Test 2 : Livreur commun à 2 entreprises

1. Créer un livreur
2. Affecter Entreprise A et B
3. Filtrer par Entreprise A
4. ✅ Vérifier : Le livreur apparaît
5. Filtrer par Entreprise B
6. ✅ Vérifier : Le livreur apparaît

### Test 3 : Retirer une entreprise spécifique

1. Livreur affecté à A, B, C
2. Aller sur détails
3. Retirer entreprise B
4. ✅ Vérifier : A et C restent, B retirée

### Test 4 : Remplacer toutes les affectations

1. Livreur affecté à A, B
2. Ouvrir modal
3. Décocher A et B
4. Cocher C et D
5. Enregistrer
6. ✅ Vérifier : Seules C et D affichées

## 🚨 Points d'attention

### ⚠️ Sync vs Attach

**`sync()`** : Remplace TOUTES les affectations
```php
$livreur->entreprises()->sync([1, 2, 3]); // Seules 1,2,3 restent
```

**`attach()`** : Ajoute SANS supprimer
```php
$livreur->entreprises()->attach(4); // Ajoute 4 aux existantes
```

**Notre choix :** `sync()` pour éviter les doublons et simplifier la gestion

### ⚠️ Compatibilité ancienne colonne

L'ancienne relation `entreprise()` (singular) via `id_entreprise` est conservée pour compatibilité.

**Recommandation :** Utiliser `entreprises()` (plural) pour la nouvelle logique

## 📚 Fichiers modifiés

- ✅ `database/migrations/2026_01_20_000007_create_entreprise_livreur_table.php`
- ✅ `app/Models/User.php` - Relation `entreprises()`
- ✅ `app/Models/Entreprise.php` - Relation `livreurs()`
- ✅ `app/Http/Controllers/LivreurController.php` - Nouvelles méthodes
- ✅ `resources/views/admin/livreurs/index.blade.php` - Modal multiple
- ✅ `resources/views/admin/livreurs/show.blade.php` - Gestion complète
- ✅ `routes/web.php` - Nouvelles routes

## 🎉 Résumé

✅ **Table pivot** créée et migrée
✅ **Relations many-to-many** configurées
✅ **Controller** avec méthodes d'affectation multiple
✅ **Interface** avec modal de sélection multiple
✅ **Affichage** des badges multiples dans la liste
✅ **Gestion** individuelle ou globale des entreprises
✅ **Filtrage** fonctionnel par entreprise
✅ **Validation** et messages d'erreur en français

Le système d'affectation multiple est maintenant **100% opérationnel** ! 🚀

Un livreur peut être affecté à autant d'entreprises que nécessaire, et la gestion est intuitive avec des checkboxes.
