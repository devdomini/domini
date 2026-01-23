# 📋 Menu Public Dynamique - Domini

Ce document explique comment le menu public dynamique fonctionne et comment l'utiliser.

---

## 🎯 Fonctionnalités

### Affichage Dynamique
- ✅ **Catégories depuis la DB** : Les catégories sont récupérées depuis la table `categories`
- ✅ **Plats depuis la DB** : Les plats sont récupérés depuis la table `plats`
- ✅ **Filtrage par catégorie** : Filtrage interactif avec animations
- ✅ **Images dynamiques** : Affichage des images uploadées avec fallback
- ✅ **Informations complètes** : Nom, prix, description, catégorie

### Animations et UX
- ✅ **Animations au clic** : Transition fluide entre les catégories
- ✅ **Effet cascade** : Les cartes apparaissent progressivement
- ✅ **Message vide** : Affichage d'un message si aucun plat dans une catégorie
- ✅ **Bouton commander** : Alerte pour télécharger l'application

---

## 🗂️ Structure des Fichiers

### Controller
**`app/Http/Controllers/MenuPublicController.php`**
```php
<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Plat;

class MenuPublicController extends Controller
{
    public function index()
    {
        // Récupère catégories et plats disponibles
        $categories = Categorie::where('est_disponible', true)->orderBy('nom')->get();
        $plats = Plat::with('categorie')->where('est_disponible', true)->orderBy('nom')->get();
        
        return view('menu', compact('categories', 'plats'));
    }
}
```

### Route
**`routes/web.php`**
```php
Route::get('/notre-menu', [App\Http\Controllers\MenuPublicController::class, 'index'])
    ->name('menu.public');
```

### Vue
**`resources/views/menu.blade.php`**
- Affichage des catégories dynamiques
- Affichage des plats dynamiques
- Filtrage JavaScript avec animations

---

## 📊 Données Affichées

### Pour chaque Catégorie
```php
{{ $categorie->nom }}        // Nom de la catégorie
{{ $categorie->id }}          // ID pour le filtrage
```

### Pour chaque Plat
```php
{{ $plat->nom }}              // Nom du plat
{{ $plat->prix }}             // Prix (formaté en FCFA)
{{ $plat->detail }}           // Description du plat
{{ $plat->image }}            // Chemin de l'image
{{ $plat->categorie->nom }}   // Nom de la catégorie
{{ $plat->id_categorie }}     // ID catégorie (pour filtrage)
```

---

## 🎨 Filtrage par Catégorie

### Fonctionnement JavaScript
1. **Clic sur une catégorie** → Le bouton devient actif
2. **Filtrage des cartes** → Affichage des plats correspondants
3. **Animation cascade** → Les cartes apparaissent progressivement
4. **Message vide** → Si aucun plat, affichage d'un message

### Attributs HTML
```html
<!-- Bouton de catégorie -->
<button data-category="{{ $categorie->id }}">{{ $categorie->nom }}</button>

<!-- Carte de plat -->
<div class="menu-item-card" data-category="{{ $plat->id_categorie }}">
    <!-- Contenu du plat -->
</div>
```

---

## 🖼️ Gestion des Images

### Affichage
```blade
@if($plat->image)
    <img src="{{ asset('storage/' . $plat->image) }}" alt="{{ $plat->nom }}" 
         onerror="this.src='{{ asset('menu/closeup-roasted-meat-with-sauce-vegetables-fries-plate-table.jpg') }}'">
@else
    <img src="{{ asset('menu/closeup-roasted-meat-with-sauce-vegetables-fries-plate-table.jpg') }}" alt="{{ $plat->nom }}">
@endif
```

### Fallback
- Si l'image n'existe pas → Image par défaut
- `onerror` pour gérer les images manquantes

---

## 🎭 Badges Automatiques

### Badge "Populaire"
Les 2 premiers plats affichés reçoivent automatiquement un badge "Populaire" :

```blade
@if($loop->index < 2)
    <span class="menu-item-badge">Populaire</span>
@endif
```

---

## 📱 Bouton Commander

### Fonctionnalité
```html
<button onclick="alert('Téléchargez l\'application Domini pour commander !')">
    Commander
</button>
```

**Important** : Ce bouton affiche actuellement une alerte. À l'avenir, il devrait :
- Rediriger vers l'App Store / Play Store
- Ou ouvrir un deep link vers l'application mobile

---

## ✨ Animations CSS

### Apparition Initiale
```css
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.menu-item-card:nth-child(1) { animation-delay: 0.1s; }
.menu-item-card:nth-child(2) { animation-delay: 0.2s; }
/* ... jusqu'à 9 */
```

### Transitions
```css
.menu-item-card {
    transition: all 0.3s ease, opacity 0.3s ease, transform 0.3s ease;
}
```

---

## 🚀 Utilisation

### 1. Ajouter des Plats
Via le panneau admin (`/admin/menu/plats`), ajoutez des plats avec :
- Nom
- Prix
- Image
- Description
- Catégorie
- Statut "Disponible" activé

### 2. Créer des Catégories
Via le panneau admin (`/admin/menu/categories`), créez des catégories avec :
- Nom
- Logo (optionnel)
- Statut "Disponible" activé

### 3. Visiter la Page
Accédez à : `http://localhost:8000/notre-menu`

---

## 🔄 Workflow Complet

```
1. Admin ajoute plats et catégories
              ↓
2. MenuPublicController récupère les données
              ↓
3. Vue menu.blade.php affiche dynamiquement
              ↓
4. JavaScript gère le filtrage interactif
              ↓
5. Utilisateur navigue et filtre
```

---

## 📝 Notes Importantes

1. **Seuls les plats disponibles** sont affichés (`est_disponible = true`)
2. **Seules les catégories disponibles** sont affichées (`est_disponible = true`)
3. **Images** : Assurez-vous que `php artisan storage:link` a été exécuté
4. **Fallback** : Une image par défaut est affichée si l'image du plat est manquante
5. **Performance** : Les relations sont chargées avec `with('categorie')` pour éviter le N+1

---

## 🛠️ Personnalisation

### Modifier le Message Vide
```javascript
emptyMsg.innerHTML = `
    <h3>Votre message personnalisé</h3>
    <p>Votre sous-message</p>
`;
```

### Changer l'Image par Défaut
```blade
<img src="{{ asset('votre/image/par/defaut.jpg') }}" alt="{{ $plat->nom }}">
```

### Modifier les Animations
```css
.menu-item-card {
    animation: votreAnimation 0.6s ease forwards;
}
```

---

## 🐛 Dépannage

### Les plats ne s'affichent pas
- Vérifiez que `est_disponible = true` dans la DB
- Vérifiez que `php artisan storage:link` a été exécuté
- Vérifiez les permissions du dossier `storage/app/public`

### Le filtrage ne fonctionne pas
- Vérifiez que les `data-category` correspondent aux IDs
- Ouvrez la console du navigateur pour voir les erreurs JS

### Les images ne s'affichent pas
- Vérifiez que le chemin est correct : `storage/plats/image.jpg`
- Vérifiez que le fichier existe physiquement
- Le fallback `onerror` devrait afficher l'image par défaut

---

Créé le : 21 janvier 2026
Dernière mise à jour : 21 janvier 2026
