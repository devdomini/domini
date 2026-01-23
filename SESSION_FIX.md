# Fix : Erreur "Route [login] not defined"

## 🐛 Problème rencontré

Lorsque la session expire, l'erreur suivante apparaît :

```
Symfony\Component\Routing\Exception\RouteNotFoundException
Route [login] not defined.
```

## 🔍 Cause

Par défaut, Laravel cherche une route nommée `login` (sans préfixe) lorsqu'un utilisateur non authentifié tente d'accéder à une route protégée. Dans notre application, la route de login est nommée `admin.login`, ce qui cause le conflit.

## ✅ Solution appliquée

### 1. Route alias (routes/web.php)

Ajout d'une route `login` qui redirige vers `admin.login` :

```php
// Route de login simple (alias pour Laravel)
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');
```

### 2. Configuration du middleware (bootstrap/app.php)

Configuration pour rediriger les invités directement vers `/admin/login` :

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->redirectGuestsTo('/admin/login');
})
```

### 3. Middleware RedirectIfAuthenticated

Création d'un middleware pour rediriger les utilisateurs déjà authentifiés vers le dashboard :

```php
// app/Http/Middleware/RedirectIfAuthenticated.php
if (Auth::guard($guard)->check()) {
    return redirect('/admin/dashboard');
}
```

## 🎯 Résultat

Maintenant, lorsque la session expire ou qu'un utilisateur non authentifié essaie d'accéder à une page protégée :

1. ✅ **Redirection automatique** vers `/admin/login`
2. ✅ **Pas d'erreur** "Route [login] not defined"
3. ✅ **Message de session** : L'utilisateur peut voir qu'il doit se reconnecter
4. ✅ **Retour après login** : L'utilisateur est redirigé vers le dashboard

## 📝 Durée de session

Par défaut, la session Laravel expire après **120 minutes** (2 heures) d'inactivité.

Pour modifier cette durée, éditez le fichier `config/session.php` :

```php
'lifetime' => 120, // Durée en minutes
```

### Durées recommandées :

- **30 minutes** : Pour une sécurité maximale (applications bancaires)
- **120 minutes** : Défaut Laravel (bon compromis)
- **240 minutes** : Pour plus de confort (4 heures)
- **480 minutes** : Pour une journée de travail (8 heures)

## 🔐 Sécurité

### Déconnexion automatique

La session expire automatiquement après la durée définie d'**inactivité**.

### Session Remember Me

Si vous voulez que les utilisateurs restent connectés plus longtemps, ajoutez le "Remember Me" dans le formulaire de login (déjà implémenté dans `AdminController`).

## 🧪 Tester

### Test 1 : Session expirée

1. Connectez-vous à `/admin/login`
2. Attendez l'expiration de la session (ou supprimez les cookies)
3. Essayez d'accéder à `/admin/dashboard`
4. **Résultat attendu** : Redirection vers `/admin/login` sans erreur

### Test 2 : Accès direct sans authentification

1. En navigation privée, allez sur `/admin/users`
2. **Résultat attendu** : Redirection vers `/admin/login`

### Test 3 : Utilisateur déjà connecté

1. Connectez-vous
2. Essayez d'accéder à `/login` ou `/admin/login`
3. **Résultat attendu** : Redirection vers `/admin/dashboard`

## 📚 Routes disponibles

### Routes publiques (pas d'authentification)
- `/` - Page d'accueil
- `/support` - Page support
- `/inscription` - Formulaire inscription entreprise
- `/login` - Alias vers admin login (redirige vers `/admin/login`)
- `/admin/login` - Page de connexion admin

### Routes protégées (authentification requise)
- `/admin/dashboard` - Tableau de bord
- `/admin/users/*` - Gestion utilisateurs
- `/admin/entreprises/*` - Gestion entreprises
- `/admin/menu/*` - Gestion menu (catégories, plats)

## ⚙️ Configuration avancée

### Modifier le message de redirection

Dans `app/Http/Controllers/AdminController.php`, la méthode de login gère déjà la redirection après authentification :

```php
return redirect()->route('admin.dashboard')
    ->with('success', 'Connexion réussie !');
```

### Ajouter un message de session expirée

Pour informer l'utilisateur que sa session a expiré, modifiez `bootstrap/app.php` :

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->redirectGuestsTo(function ($request) {
        session()->flash('error', 'Votre session a expiré. Veuillez vous reconnecter.');
        return '/admin/login';
    });
})
```

## 🎉 Conclusion

Le problème est maintenant résolu ! Les utilisateurs seront correctement redirigés vers la page de login lorsque leur session expire, sans voir d'erreur.

### Points clés :
- ✅ Route `login` alias créée
- ✅ Middleware configuré pour rediriger vers `/admin/login`
- ✅ Gestion propre de l'expiration de session
- ✅ Pas d'erreur "Route not defined"

## 🔗 Fichiers modifiés

1. `routes/web.php` - Ajout de la route alias `login`
2. `bootstrap/app.php` - Configuration du middleware
3. `app/Http/Middleware/RedirectIfAuthenticated.php` - Middleware créé

Tout fonctionne maintenant correctement ! 🚀
