# 📮 Guide d'Import Postman - API Domini

## 🚀 Import Rapide

### Option 1 : Import depuis les fichiers JSON

1. **Ouvrir Postman**
2. **Cliquer sur "Import"** (bouton en haut à gauche)
3. **Importer les fichiers** :
   - `Domini_API_Collection.postman_collection.json` - Collection complète
   - `Domini_API_Environment.postman_environment.json` - Variables d'environnement

### Option 2 : Import depuis URL (si hébergé)

1. Dans Postman, cliquez sur **Import**
2. Sélectionnez l'onglet **Link**
3. Collez l'URL de la collection

---

## ⚙️ Configuration de l'Environnement

### Variables disponibles

| Variable | Description | Valeur par défaut |
|----------|-------------|-------------------|
| `base_url` | URL de base de l'API | `http://localhost:8000/api` |
| `auth_token` | Token d'authentification (auto-rempli après login) | *(vide)* |
| `user_id` | ID de l'utilisateur connecté | *(vide)* |
| `commande_id` | ID de la dernière commande créée | *(vide)* |

### Configuration

1. **Sélectionner l'environnement** "Domini API - Environment" dans le menu déroulant en haut à droite
2. **Modifier `base_url`** si votre serveur est sur un autre port/domaine
3. Le **token sera automatiquement sauvegardé** après la connexion grâce au script de test dans la requête "Connexion"

---

## 🔐 Workflow d'Authentification

### Étape 1 : Inscription ou Connexion

1. **Option A - Inscription** :
   - Exécutez la requête `🔐 Authentification > Inscription`
   - Modifiez les données si nécessaire
   - Copiez le `token` de la réponse

2. **Option B - Connexion** (Recommandé) :
   - Exécutez la requête `🔐 Authentification > Connexion`
   - Le token sera **automatiquement sauvegardé** dans la variable `auth_token`
   - Vous pouvez maintenant utiliser toutes les routes protégées

### Étape 2 : Utiliser les routes protégées

Toutes les routes protégées utilisent automatiquement le token via :
```
Authorization: Bearer {{auth_token}}
```

---

## 📋 Structure de la Collection

### 🔐 Authentification (9 endpoints)
- ✅ Inscription
- ✅ Connexion (avec auto-sauvegarde du token)
- ✅ Informations utilisateur
- ✅ Mettre à jour le profil
- ✅ Changer le mot de passe
- ✅ Vérifier le téléphone
- ✅ Mot de passe oublié
- ✅ Réinitialiser le mot de passe
- ✅ Déconnexion

### 🍽️ Menu (5 endpoints)
- ✅ Liste des catégories (avec filtres)
- ✅ Détails d'une catégorie
- ✅ Liste des plats (avec filtres multiples)
- ✅ Plats par catégorie
- ✅ Détails d'un plat

### 🛒 Commandes (3 endpoints)
- ✅ Créer une commande
- ✅ Historique des commandes (avec pagination et filtres)
- ✅ Détails d'une commande

### ⭐ Favoris (4 endpoints)
- ✅ Liste des favoris
- ✅ Ajouter aux favoris
- ✅ Vérifier si en favori
- ✅ Retirer des favoris

---

## 🎯 Exemples d'Utilisation

### Exemple 1 : Créer une commande complète

1. **Se connecter** (`🔐 Authentification > Connexion`)
2. **Voir les plats disponibles** (`🍽️ Menu > Plats > Liste des plats`)
3. **Créer une commande** (`🛒 Commandes > Créer une commande`)
   - Modifiez les `plat_id` dans le body selon les plats disponibles
   - Ajustez les quantités et accompagnements

### Exemple 2 : Gérer les favoris

1. **Se connecter**
2. **Voir les détails d'un plat** (`🍽️ Menu > Plats > Détails d'un plat`)
3. **Ajouter aux favoris** (`⭐ Favoris > Ajouter aux favoris`)
   - Utilisez le `plat_id` du plat que vous voulez ajouter
4. **Voir vos favoris** (`⭐ Favoris > Liste des favoris`)

---

## 🔧 Scripts Automatiques

### Script de test dans "Connexion"

Le script suivant s'exécute automatiquement après une connexion réussie :

```javascript
if (pm.response.code === 200) {
    var jsonData = pm.response.json();
    if (jsonData.data && jsonData.data.token) {
        pm.environment.set("auth_token", jsonData.data.token);
        console.log("Token sauvegardé:", jsonData.data.token);
    }
}
```

Cela permet de :
- ✅ Sauvegarder automatiquement le token
- ✅ Éviter de copier-coller manuellement
- ✅ Utiliser immédiatement les routes protégées

---

## 📝 Notes Importantes

### Format des Réponses

Toutes les réponses suivent le format standard :

**Succès :**
```json
{
  "success": true,
  "message": "Message de succès",
  "data": { ... }
}
```

**Erreur :**
```json
{
  "success": false,
  "message": "Message d'erreur",
  "errors": {
    "champ": ["Message d'erreur spécifique"]
  }
}
```

### Codes de Statut HTTP

- `200` - Succès
- `201` - Créé avec succès
- `400` - Requête invalide
- `401` - Non authentifié
- `403` - Accès refusé
- `404` - Ressource non trouvée
- `422` - Erreurs de validation
- `500` - Erreur serveur

### Authentification

- Les routes **publiques** (menu) ne nécessitent pas de token
- Les routes **protégées** nécessitent le header `Authorization: Bearer {token}`
- Le token expire après un certain temps (configurable dans Sanctum)

---

## 🐛 Dépannage

### Problème : "Unauthenticated" (401)

**Solution :**
1. Vérifiez que vous avez bien exécuté la requête "Connexion"
2. Vérifiez que la variable `auth_token` est bien remplie dans l'environnement
3. Réessayez la connexion

### Problème : "Route not found" (404)

**Solution :**
1. Vérifiez que le serveur Laravel est démarré : `php artisan serve`
2. Vérifiez que `base_url` pointe vers le bon port (par défaut : `http://localhost:8000/api`)
3. Vérifiez que les routes API sont bien enregistrées : `php artisan route:list --path=api`

### Problème : "Validation errors" (422)

**Solution :**
1. Vérifiez que tous les champs requis sont remplis
2. Vérifiez le format des données (email valide, téléphone au bon format, etc.)
3. Consultez le message d'erreur dans la réponse pour plus de détails

---

## 📚 Ressources

- **Documentation API complète** : `API_DOCUMENTATION.md`
- **Rapport de test** : `RAPPORT_TEST_API.md`
- **Base URL par défaut** : `http://localhost:8000/api`

---

## ✅ Checklist de Test

- [ ] Serveur Laravel démarré (`php artisan serve`)
- [ ] Base de données migrée et seedée (`php artisan migrate --seed`)
- [ ] Collection Postman importée
- [ ] Environnement Postman sélectionné
- [ ] Connexion réussie (token sauvegardé)
- [ ] Test des routes publiques (menu)
- [ ] Test des routes protégées (commandes, favoris)
- [ ] Vérification des réponses JSON

---

**Bon test ! 🚀**
