# Documentation API Domini

## 🔐 Authentification

L'API utilise Laravel Sanctum pour l'authentification par tokens. Toutes les routes protégées nécessitent un token Bearer dans l'en-tête `Authorization`.

### Format de l'en-tête
```
Authorization: Bearer {token}
```

---

## 📋 Routes d'Authentification

### 1. Inscription (`POST /api/auth/register`)

**Route publique** - Créer un nouveau compte employé

**Body:**
```json
{
  "name": "Jean Dupont",
  "email": "jean@example.com",
  "telephone": "+2250123456789",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "employe" // optionnel: "employe" ou "livreur"
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Inscription réussie",
  "data": {
    "user": {
      "id": 1,
      "name": "Jean Dupont",
      "email": "jean@example.com",
      "telephone": "+2250123456789",
      "role": "employe"
    },
    "token": "1|xxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

---

### 2. Connexion (`POST /api/auth/login`)

**Route publique** - Se connecter avec email et mot de passe

**Body:**
```json
{
  "email": "jean@example.com",
  "password": "password123"
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Connexion réussie",
  "data": {
    "user": { ... },
    "token": "1|xxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

---

### 3. Informations utilisateur (`GET /api/auth/me`)

**Route protégée** - Obtenir les informations de l'utilisateur connecté

**Headers:**
```
Authorization: Bearer {token}
```

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Jean Dupont",
      "email": "jean@example.com",
      "telephone": "+2250123456789",
      "role": "employe"
    }
  }
}
```

---

### 4. Mettre à jour le profil (`PUT /api/auth/profile`)

**Route protégée** - Modifier les informations du profil

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
  "name": "Jean Dupont Modifié",
  "email": "nouveau@example.com",
  "telephone": "+2250987654321"
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Profil mis à jour avec succès",
  "data": {
    "user": { ... }
  }
}
```

---

### 5. Changer le mot de passe (`POST /api/auth/change-password`)

**Route protégée** - Modifier le mot de passe

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
  "current_password": "ancien_password",
  "password": "nouveau_password",
  "password_confirmation": "nouveau_password"
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Mot de passe modifié avec succès"
}
```

---

### 6. Vérifier le téléphone (`POST /api/auth/verify-phone`)

**Route publique** - Vérifier un numéro de téléphone avec un code

**Body:**
```json
{
  "telephone": "+2250123456789",
  "code": "123456"
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Numéro de téléphone vérifié avec succès",
  "data": {
    "user": { ... }
  }
}
```

---

### 7. Mot de passe oublié (`POST /api/auth/forgot-password`)

**Route publique** - Demander la réinitialisation du mot de passe

**Body:**
```json
{
  "email": "jean@example.com"
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Un lien de réinitialisation a été envoyé à votre email"
}
```

---

### 8. Réinitialiser le mot de passe (`POST /api/auth/reset-password`)

**Route publique** - Réinitialiser le mot de passe avec le token

**Body:**
```json
{
  "token": "xxxxxxxxxxxx",
  "email": "jean@example.com",
  "password": "nouveau_password",
  "password_confirmation": "nouveau_password"
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Mot de passe réinitialisé avec succès"
}
```

---

### 9. Déconnexion (`POST /api/auth/logout`)

**Route protégée** - Se déconnecter (supprime le token)

**Headers:**
```
Authorization: Bearer {token}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Déconnexion réussie"
}
```

---

## 🍽️ Routes du Menu

### 1. Liste des catégories (`GET /api/menu/categories`)

**Route publique** - Obtenir toutes les catégories disponibles

**Query Parameters:**
- `disponible` (boolean): Filtrer par disponibilité (par défaut: true)
- `qualite` (string): Filtrer par qualité (classic, pro, premium)

**Exemple:**
```
GET /api/menu/categories?disponible=true&qualite=premium
```

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "categories": [
      {
        "id": 1,
        "nom": "Plats principaux",
        "logo": "categories/logo.jpg",
        "est_disponible": true,
        "qualite": "premium",
        "plats_count": 15
      }
    ],
    "total": 1
  }
}
```

---

### 2. Détails d'une catégorie (`GET /api/menu/categories/{id}`)

**Route publique** - Obtenir les détails d'une catégorie avec ses plats

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "categorie": {
      "id": 1,
      "nom": "Plats principaux",
      "logo": "categories/logo.jpg",
      "est_disponible": true,
      "qualite": "premium",
      "plats": [ ... ]
    }
  }
}
```

---

### 3. Liste des plats (`GET /api/menu/plats`)

**Route publique** - Obtenir tous les plats disponibles

**Query Parameters:**
- `disponible` (boolean): Filtrer par disponibilité (par défaut: true)
- `categorie_id` (integer): Filtrer par catégorie
- `qualite` (string): Filtrer par qualité (classic, pro, premium)
- `search` (string): Rechercher par nom
- `sort_by` (string): Trier par (nom, prix, created_at) - par défaut: nom
- `sort_order` (string): Ordre (asc, desc) - par défaut: asc

**Exemple:**
```
GET /api/menu/plats?categorie_id=1&qualite=premium&sort_by=prix&sort_order=asc
```

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "plats": [
      {
        "id": 1,
        "nom": "Riz au gras",
        "prix": "2500.00",
        "image": "plats/riz.jpg",
        "detail": "Riz avec sauce au gras",
        "est_disponible": true,
        "qualite": "premium",
        "categorie_id": 1,
        "categorie": { ... },
        "accompagnements": [ ... ],
        "options": [ ... ]
      }
    ],
    "total": 1
  }
}
```

---

### 4. Plats par catégorie (`GET /api/menu/plats/category/{categorieId}`)

**Route publique** - Obtenir tous les plats d'une catégorie

**Query Parameters:**
- `disponible` (boolean): Filtrer par disponibilité
- `qualite` (string): Filtrer par qualité

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "categorie": { ... },
    "plats": [ ... ],
    "total": 5
  }
}
```

---

### 5. Détails d'un plat (`GET /api/menu/plats/{id}`)

**Route publique** - Obtenir les détails complets d'un plat

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "plat": {
      "id": 1,
      "nom": "Riz au gras",
      "prix": "2500.00",
      "image": "plats/riz.jpg",
      "detail": "Riz avec sauce au gras",
      "est_disponible": true,
      "qualite": "premium",
      "categorie": { ... },
      "accompagnements": [
        {
          "id": 1,
          "nom": "Alloco",
          "qte_gratuit": 1,
          "prix_unitaire": "500.00",
          "disponible": true
        }
      ],
      "options": [ ... ]
    }
  }
}
```

---

## 🛒 Routes des Commandes

### 1. Créer une commande (`POST /api/commandes`)

**Route protégée** - Créer une nouvelle commande

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
  "items": [
    {
      "plat_id": 1,
      "quantite": 2,
      "accompagnements": [
        {
          "id": 1,
          "quantite": 2
        }
      ],
      "options": [
        {
          "id": 1,
          "quantite": 1
        }
      ]
    }
  ],
  "montant_total": 5000,
  "lieu": "Bureau principal",
  "lat": 5.3600,
  "long": -4.0083,
  "consigne_cuisinier": "Sans piment",
  "consigne_livreur": "Appeler avant d'arriver",
  "mode_paiement": "mobile_money",
  "numero_telephone": "+2250123456789"
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Commande créée avec succès",
  "data": {
    "commande": {
      "id": 1,
      "ref": "CMD-20260128-ABC123",
      "montant_total": "5000.00",
      "statut_commande": "en_attente",
      "statut_preparation": "en_attente",
      "statut_livraison": "en_attente",
      "statut_paiement": "en_attente",
      "mode_paiement": "mobile_money",
      "items": [ ... ],
      "employe": { ... }
    }
  }
}
```

---

### 2. Historique des commandes (`GET /api/commandes/history`)

**Route protégée** - Obtenir l'historique des commandes de l'utilisateur

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `statut_commande` (string): Filtrer par statut (en_attente, confirmee, annulee, terminee)
- `statut_preparation` (string): Filtrer par statut de préparation
- `statut_livraison` (string): Filtrer par statut de livraison
- `per_page` (integer): Nombre d'éléments par page (par défaut: 15)

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "commandes": [ ... ],
    "pagination": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 15,
      "total": 75
    }
  }
}
```

---

### 3. Détails d'une commande (`GET /api/commandes/{id}`)

**Route protégée** - Obtenir les détails d'une commande spécifique

**Headers:**
```
Authorization: Bearer {token}
```

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "commande": {
      "id": 1,
      "ref": "CMD-20260128-ABC123",
      "montant_total": "5000.00",
      "statut_commande": "confirmee",
      "items": [ ... ],
      "livraison": {
        "livreur": { ... }
      },
      "paiements": [ ... ]
    }
  }
}
```

---

## ⭐ Routes des Favoris

### 1. Liste des favoris (`GET /api/favoris`)

**Route protégée** - Obtenir tous les plats favoris de l'utilisateur

**Headers:**
```
Authorization: Bearer {token}
```

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "favoris": [
      {
        "id": 1,
        "user_id": 1,
        "plat_id": 1,
        "plat": {
          "id": 1,
          "nom": "Riz au gras",
          "prix": "2500.00",
          "categorie": { ... }
        }
      }
    ],
    "total": 1
  }
}
```

---

### 2. Ajouter aux favoris (`POST /api/favoris`)

**Route protégée** - Ajouter un plat aux favoris

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
  "plat_id": 1
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Plat ajouté aux favoris",
  "data": {
    "favori": {
      "id": 1,
      "user_id": 1,
      "plat_id": 1,
      "plat": { ... }
    }
  }
}
```

---

### 3. Vérifier si un plat est en favori (`GET /api/favoris/check/{platId}`)

**Route protégée** - Vérifier si un plat est dans les favoris

**Headers:**
```
Authorization: Bearer {token}
```

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "is_favori": true
  }
}
```

---

### 4. Retirer des favoris (`DELETE /api/favoris/{platId}`)

**Route protégée** - Retirer un plat des favoris

**Headers:**
```
Authorization: Bearer {token}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Plat retiré des favoris"
}
```

---

## 📝 Codes de Statut HTTP

- `200` - Succès
- `201` - Créé avec succès
- `400` - Requête invalide
- `401` - Non authentifié
- `403` - Accès refusé
- `404` - Ressource non trouvée
- `422` - Erreurs de validation
- `500` - Erreur serveur

---

## 🔒 Gestion des Erreurs

Toutes les erreurs suivent le même format :

```json
{
  "success": false,
  "message": "Message d'erreur",
  "errors": {
    "champ": ["Message d'erreur spécifique"]
  }
}
```

---

## 🚀 Exemple d'utilisation avec cURL

### Connexion
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "jean@example.com",
    "password": "password123"
  }'
```

### Obtenir les plats (avec token)
```bash
curl -X GET http://localhost:8000/api/menu/plats \
  -H "Authorization: Bearer 1|xxxxxxxxxxxx"
```

### Créer une commande
```bash
curl -X POST http://localhost:8000/api/commandes \
  -H "Authorization: Bearer 1|xxxxxxxxxxxx" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [{"plat_id": 1, "quantite": 2}],
    "montant_total": 5000,
    "mode_paiement": "mobile_money"
  }'
```

---

## 📌 Notes importantes

1. **Base URL**: Toutes les routes commencent par `/api`
2. **Authentification**: Utilisez le token reçu lors de la connexion dans l'en-tête `Authorization`
3. **Format des dates**: Les dates sont au format ISO 8601
4. **Format des prix**: Les prix sont en FCFA (Franc CFA)
5. **Pagination**: Les listes paginées incluent des informations de pagination dans la réponse
