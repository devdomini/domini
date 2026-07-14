# Rapport de Test API Domini

**Date de génération:** 2026-02-02 16:44:10

**Base URL:** `http://localhost:8000/api`

## 📊 Statistiques

- **Total des tests:** 26
- **Tests réussis:** 23 ✅
- **Tests échoués:** 3 ❌
- **Taux de réussite:** 88.46%

---

## 📝 Détails des Tests

### Test 1: Inscription utilisateur ✅

**Endpoint:** `POST /api/auth/register`

**Description:** Créer un nouveau compte employé

**Données de requête:**

```json
{
    "name": "Test User API 1770050637",
    "email": "testapi1770050637@example.com",
    "telephone": "+225062486789",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "employe"
}
```

**Code de statut HTTP:** `201`

**Réponse:**

```json
{
    "success": true,
    "message": "Inscription réussie. Un code de vérification a été envoyé à votre numéro de téléphone.",
    "sms_sent": false,
    "sms_message": "Erreur lors de l'envoi du SMS",
    "data": {
        "user": {
            "name": "Test User API 1770050637",
            "email": "testapi1770050637@example.com",
            "telephone": "+225062486789",
            "role": "employe",
            "is_active": true,
            "updated_at": "2026-02-02T16:43:58.000000Z",
            "created_at": "2026-02-02T16:43:58.000000Z",
            "id": 28
        },
        "access_token": "8|nwofqZFgnaLpSh9ndUltVTsJTKgwp6UtcKMvXCB32d3b5cd0",
        "token_type": "Bearer",
        "expires_at": "2026-03-02T16:43:59.332428Z"
    }
}
```

**Timestamp:** 2026-02-02 16:43:59

---

### Test 2: Connexion ✅

**Endpoint:** `POST /api/auth/login`

**Description:** Se connecter avec email et mot de passe

**Données de requête:**

```json
{
    "email": "testapi1770050637@example.com",
    "password": "password123"
}
```

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "message": "Connexion réussie",
    "data": {
        "user": {
            "id": 28,
            "name": "Test User API 1770050637",
            "email": "testapi1770050637@example.com",
            "email_verified_at": null,
            "role": "employe",
            "telephone": "+225062486789",
            "telephone_verified_at": null,
            "id_entreprise": null,
            "num_box": null,
            "is_active": true,
            "created_at": "2026-02-02T16:43:58.000000Z",
            "updated_at": "2026-02-02T16:43:58.000000Z"
        },
        "access_token": "9|bNcwbUt1wBW96AVeWEaVYvQpMQ7vz3Ay3VEXLUtLade98d81",
        "token_type": "Bearer",
        "expires_at": "2026-03-02T16:43:59.855358Z"
    }
}
```

**Timestamp:** 2026-02-02 16:43:59

---

### Test 3: Mot de passe oublié ✅

**Endpoint:** `POST /api/auth/forgot-password`

**Description:** Demander la réinitialisation du mot de passe

**Données de requête:**

```json
{
    "email": "testapi1770050637@example.com"
}
```

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "message": "Un lien de réinitialisation a été envoyé à votre email"
}
```

**Timestamp:** 2026-02-02 16:44:00

---

### Test 4: Vérification téléphone ❌

**Endpoint:** `POST /api/auth/verify-phone`

**Description:** Vérifier un numéro de téléphone avec le code reçu par SMS (test avec code fictif - nécessite un code réel pour réussir)

**Données de requête:**

```json
{
    "telephone": "+225062486789",
    "code": "123456"
}
```

**Code de statut HTTP:** `400`

**Réponse:**

```json
{
    "success": false,
    "message": "Code de vérification incorrect"
}
```

**Timestamp:** 2026-02-02 16:44:00

---

### Test 5: Renvoyer code vérification ❌

**Endpoint:** `POST /api/auth/resend-verification-code`

**Description:** Renvoyer un nouveau code de vérification par SMS

**Données de requête:**

```json
{
    "telephone": "+225062486789"
}
```

**Code de statut HTTP:** `500`

**Réponse:**

```json
{
    "success": false,
    "message": "Erreur lors de l'envoi du SMS. Le code a été généré mais l'envoi a échoué. Veuillez contacter le support ou réessayer plus tard.",
    "error": "Erreur lors de l'envoi du SMS",
    "note": "Le code de vérification a été généré et stocké dans la base de données"
}
```

**Timestamp:** 2026-02-02 16:44:01

---

### Test 6: Liste catégories ✅

**Endpoint:** `GET /api/menu/categories`

**Description:** Obtenir toutes les catégories disponibles

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "categories": [
            {
                "id": 4,
                "nom": "Grillades",
                "logo": null,
                "est_disponible": true,
                "qualite": "classic",
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "plats_count": 1
            },
            {
                "id": 6,
                "nom": "Pâtes & Pizzas",
                "logo": null,
                "est_disponible": true,
                "qualite": "classic",
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "plats_count": 2
            },
            {
                "id": 1,
                "nom": "Plats Africains",
                "logo": null,
                "est_disponible": true,
                "qualite": "classic",
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "plats_count": 3
            },
            {
                "id": 3,
                "nom": "Plats Asiatiques",
                "logo": null,
                "est_disponible": true,
                "qualite": "classic",
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "plats_count": 2
            },
            {
                "id": 2,
                "nom": "Plats Européens",
                "logo": null,
                "est_disponible": true,
                "qualite": "classic",
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "plats_count": 2
            },
            {
                "id": 5,
                "nom": "Salades & Léger",
                "logo": null,
                "est_disponible": true,
                "qualite": "classic",
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "plats_count": 1
            }
        ],
        "total": 6
    }
}
```

**Timestamp:** 2026-02-02 16:44:02

---

### Test 7: Liste catégories (filtre qualité) ✅

**Endpoint:** `GET /api/menu/categories?qualite=premium`

**Description:** Obtenir les catégories avec filtre qualité premium

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "categories": [],
        "total": 0
    }
}
```

**Timestamp:** 2026-02-02 16:44:02

---

### Test 8: Détails catégorie ✅

**Endpoint:** `GET /api/menu/categories/1`

**Description:** Obtenir les détails d'une catégorie spécifique

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "categorie": {
            "id": 1,
            "nom": "Plats Africains",
            "logo": null,
            "est_disponible": true,
            "qualite": "classic",
            "created_at": "2026-01-28T03:17:39.000000Z",
            "updated_at": "2026-01-28T03:17:39.000000Z",
            "plats": [
                {
                    "id": 1,
                    "nom": "Attiéké Poisson Braisé",
                    "prix": "2500.00",
                    "image": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "detail": "Attiéké accompagné de poisson braisé frais et sauce tomate oignon",
                    "categorie_id": 1,
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                {
                    "id": 2,
                    "nom": "Riz Sauce Graine",
                    "prix": "2000.00",
                    "image": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "detail": "Riz blanc avec sauce graine de palme, viande de bœuf",
                    "categorie_id": 1,
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                {
                    "id": 3,
                    "nom": "Foutou Sauce Arachide",
                    "prix": "2200.00",
                    "image": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "detail": "Foutou d'igname avec sauce arachide et viande de bœuf",
                    "categorie_id": 1,
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                }
            ]
        }
    }
}
```

**Timestamp:** 2026-02-02 16:44:02

---

### Test 9: Liste plats ✅

**Endpoint:** `GET /api/menu/plats`

**Description:** Obtenir tous les plats disponibles

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "plats": [
            {
                "id": 1,
                "nom": "Attiéké Poisson Braisé",
                "prix": "2500.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Attiéké accompagné de poisson braisé frais et sauce tomate oignon",
                "categorie_id": 1,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 1,
                    "nom": "Plats Africains",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 1,
                        "nom": "Alloco",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "500.00",
                        "disponible": true,
                        "plat_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    },
                    {
                        "id": 2,
                        "nom": "Salade",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "300.00",
                        "disponible": true,
                        "plat_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": [
                    {
                        "id": 1,
                        "nom": "Sauce pimentée",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    },
                    {
                        "id": 2,
                        "nom": "Citron",
                        "image": null,
                        "qte_gratuit": 2,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ]
            },
            {
                "id": 8,
                "nom": "Brochettes de Bœuf",
                "prix": "2700.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "6 brochettes de bœuf marinées et grillées",
                "categorie_id": 4,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 4,
                    "nom": "Grillades",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 10,
                        "nom": "Frites",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "500.00",
                        "disponible": true,
                        "plat_id": 8,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    },
                    {
                        "id": 11,
                        "nom": "Alloco",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "500.00",
                        "disponible": true,
                        "plat_id": 8,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": [
                    {
                        "id": 7,
                        "nom": "Sauce barbecue",
                        "image": null,
                        "qte_gratuit": 2,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 8,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ]
            },
            {
                "id": 3,
                "nom": "Foutou Sauce Arachide",
                "prix": "2200.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Foutou d'igname avec sauce arachide et viande de bœuf",
                "categorie_id": 1,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 1,
                    "nom": "Plats Africains",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 5,
                        "nom": "Poisson fumé",
                        "image": null,
                        "qte_gratuit": 0,
                        "prix_unitaire": "800.00",
                        "disponible": true,
                        "plat_id": 3,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": []
            },
            {
                "id": 6,
                "nom": "Nouilles Sautées Poulet",
                "prix": "2300.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Nouilles chinoises sautées avec poulet et légumes",
                "categorie_id": 3,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 3,
                    "nom": "Plats Asiatiques",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 8,
                        "nom": "Nems",
                        "image": null,
                        "qte_gratuit": 2,
                        "prix_unitaire": "300.00",
                        "disponible": true,
                        "plat_id": 6,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": [
                    {
                        "id": 5,
                        "nom": "Sauce soja",
                        "image": null,
                        "qte_gratuit": 2,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 6,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    },
                    {
                        "id": 6,
                        "nom": "Piment",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 6,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ]
            },
            {
                "id": 11,
                "nom": "Pizza Margherita",
                "prix": "2600.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Pizza tomate, mozzarella, basilic frais",
                "categorie_id": 6,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 6,
                    "nom": "Pâtes & Pizzas",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [],
                "options": [
                    {
                        "id": 10,
                        "nom": "Piment",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 11,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ]
            },
            {
                "id": 5,
                "nom": "Poulet Rôti & Purée",
                "prix": "2800.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Cuisse de poulet rôti avec purée de pommes de terre",
                "categorie_id": 2,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 2,
                    "nom": "Plats Européens",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 7,
                        "nom": "Haricots verts",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "400.00",
                        "disponible": true,
                        "plat_id": 5,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": []
            },
            {
                "id": 7,
                "nom": "Riz Cantonais",
                "prix": "2500.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Riz frit à la cantonaise avec œufs, légumes et crevettes",
                "categorie_id": 3,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 3,
                    "nom": "Plats Asiatiques",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 9,
                        "nom": "Beignet de crevettes",
                        "image": null,
                        "qte_gratuit": 3,
                        "prix_unitaire": "400.00",
                        "disponible": true,
                        "plat_id": 7,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": []
            },
            {
                "id": 2,
                "nom": "Riz Sauce Graine",
                "prix": "2000.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Riz blanc avec sauce graine de palme, viande de bœuf",
                "categorie_id": 1,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 1,
                    "nom": "Plats Africains",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 3,
                        "nom": "Poulet",
                        "image": null,
                        "qte_gratuit": 0,
                        "prix_unitaire": "1000.00",
                        "disponible": true,
                        "plat_id": 2,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    },
                    {
                        "id": 4,
                        "nom": "Alloco",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "500.00",
                        "disponible": true,
                        "plat_id": 2,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": []
            },
            {
                "id": 9,
                "nom": "Salade César Poulet",
                "prix": "2200.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Salade verte, poulet grillé, croûtons, parmesan, sauce césar",
                "categorie_id": 5,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 5,
                    "nom": "Salades & Léger",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [],
                "options": [
                    {
                        "id": 8,
                        "nom": "Pain grillé",
                        "image": null,
                        "qte_gratuit": 2,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 9,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ]
            },
            {
                "id": 10,
                "nom": "Spaghetti Bolognaise",
                "prix": "2400.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Spaghetti avec sauce bolognaise maison et viande hachée",
                "categorie_id": 6,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 6,
                    "nom": "Pâtes & Pizzas",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 12,
                        "nom": "Pain à l'ail",
                        "image": null,
                        "qte_gratuit": 2,
                        "prix_unitaire": "300.00",
                        "disponible": true,
                        "plat_id": 10,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": [
                    {
                        "id": 9,
                        "nom": "Parmesan",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 10,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ]
            },
            {
                "id": 4,
                "nom": "Steak Frites",
                "prix": "3000.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Steak de bœuf grillé avec frites maison",
                "categorie_id": 2,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 2,
                    "nom": "Plats Européens",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 6,
                        "nom": "Légumes grillés",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "500.00",
                        "disponible": true,
                        "plat_id": 4,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": [
                    {
                        "id": 3,
                        "nom": "Sauce poivre",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 4,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    },
                    {
                        "id": 4,
                        "nom": "Sauce champignon",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 4,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ]
            }
        ],
        "total": 11
    }
}
```

**Timestamp:** 2026-02-02 16:44:03

---

### Test 10: Liste plats (avec filtres) ✅

**Endpoint:** `GET /api/menu/plats?categorie_id=1&qualite=premium&sort_by=prix&sort_order=asc`

**Description:** Obtenir les plats avec filtres (catégorie, qualité, tri)

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "plats": [],
        "total": 0
    }
}
```

**Timestamp:** 2026-02-02 16:44:03

---

### Test 11: Recherche plats ✅

**Endpoint:** `GET /api/menu/plats?search=riz`

**Description:** Rechercher des plats par nom

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "plats": [
            {
                "id": 7,
                "nom": "Riz Cantonais",
                "prix": "2500.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Riz frit à la cantonaise avec œufs, légumes et crevettes",
                "categorie_id": 3,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 3,
                    "nom": "Plats Asiatiques",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 9,
                        "nom": "Beignet de crevettes",
                        "image": null,
                        "qte_gratuit": 3,
                        "prix_unitaire": "400.00",
                        "disponible": true,
                        "plat_id": 7,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": []
            },
            {
                "id": 2,
                "nom": "Riz Sauce Graine",
                "prix": "2000.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Riz blanc avec sauce graine de palme, viande de bœuf",
                "categorie_id": 1,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 1,
                    "nom": "Plats Africains",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 3,
                        "nom": "Poulet",
                        "image": null,
                        "qte_gratuit": 0,
                        "prix_unitaire": "1000.00",
                        "disponible": true,
                        "plat_id": 2,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    },
                    {
                        "id": 4,
                        "nom": "Alloco",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "500.00",
                        "disponible": true,
                        "plat_id": 2,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": []
            }
        ],
        "total": 2
    }
}
```

**Timestamp:** 2026-02-02 16:44:03

---

### Test 12: Plats par catégorie ✅

**Endpoint:** `GET /api/menu/plats/category/1`

**Description:** Obtenir tous les plats d'une catégorie spécifique

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "categorie": {
            "id": 1,
            "nom": "Plats Africains",
            "logo": null,
            "est_disponible": true,
            "qualite": "classic",
            "created_at": "2026-01-28T03:17:39.000000Z",
            "updated_at": "2026-01-28T03:17:39.000000Z"
        },
        "plats": [
            {
                "id": 1,
                "nom": "Attiéké Poisson Braisé",
                "prix": "2500.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Attiéké accompagné de poisson braisé frais et sauce tomate oignon",
                "categorie_id": 1,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 1,
                    "nom": "Plats Africains",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 1,
                        "nom": "Alloco",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "500.00",
                        "disponible": true,
                        "plat_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    },
                    {
                        "id": 2,
                        "nom": "Salade",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "300.00",
                        "disponible": true,
                        "plat_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": [
                    {
                        "id": 1,
                        "nom": "Sauce pimentée",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    },
                    {
                        "id": 2,
                        "nom": "Citron",
                        "image": null,
                        "qte_gratuit": 2,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ]
            },
            {
                "id": 3,
                "nom": "Foutou Sauce Arachide",
                "prix": "2200.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Foutou d'igname avec sauce arachide et viande de bœuf",
                "categorie_id": 1,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 1,
                    "nom": "Plats Africains",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 5,
                        "nom": "Poisson fumé",
                        "image": null,
                        "qte_gratuit": 0,
                        "prix_unitaire": "800.00",
                        "disponible": true,
                        "plat_id": 3,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": []
            },
            {
                "id": 2,
                "nom": "Riz Sauce Graine",
                "prix": "2000.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Riz blanc avec sauce graine de palme, viande de bœuf",
                "categorie_id": 1,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 1,
                    "nom": "Plats Africains",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 3,
                        "nom": "Poulet",
                        "image": null,
                        "qte_gratuit": 0,
                        "prix_unitaire": "1000.00",
                        "disponible": true,
                        "plat_id": 2,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    },
                    {
                        "id": 4,
                        "nom": "Alloco",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "500.00",
                        "disponible": true,
                        "plat_id": 2,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": []
            }
        ],
        "total": 3
    }
}
```

**Timestamp:** 2026-02-02 16:44:04

---

### Test 13: Détails plat ✅

**Endpoint:** `GET /api/menu/plats/1`

**Description:** Obtenir les détails complets d'un plat

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "plat": {
            "id": 1,
            "nom": "Attiéké Poisson Braisé",
            "prix": "2500.00",
            "image": null,
            "est_disponible": true,
            "qualite": "classic",
            "detail": "Attiéké accompagné de poisson braisé frais et sauce tomate oignon",
            "categorie_id": 1,
            "created_at": "2026-01-28T03:17:39.000000Z",
            "updated_at": "2026-01-28T03:17:39.000000Z",
            "categorie": {
                "id": 1,
                "nom": "Plats Africains",
                "logo": null,
                "est_disponible": true,
                "qualite": "classic",
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z"
            },
            "accompagnements": [
                {
                    "id": 1,
                    "nom": "Alloco",
                    "image": null,
                    "qte_gratuit": 1,
                    "prix_unitaire": "500.00",
                    "disponible": true,
                    "plat_id": 1,
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                {
                    "id": 2,
                    "nom": "Salade",
                    "image": null,
                    "qte_gratuit": 1,
                    "prix_unitaire": "300.00",
                    "disponible": true,
                    "plat_id": 1,
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                }
            ],
            "options": [
                {
                    "id": 1,
                    "nom": "Sauce pimentée",
                    "image": null,
                    "qte_gratuit": 1,
                    "prix_unitaire": "0.00",
                    "disponible": true,
                    "plat_id": 1,
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                {
                    "id": 2,
                    "nom": "Citron",
                    "image": null,
                    "qte_gratuit": 2,
                    "prix_unitaire": "0.00",
                    "disponible": true,
                    "plat_id": 1,
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                }
            ]
        }
    }
}
```

**Timestamp:** 2026-02-02 16:44:04

---

### Test 14: Informations utilisateur ✅

**Endpoint:** `GET /api/auth/me`

**Description:** Obtenir les informations de l'utilisateur connecté

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "user": {
            "id": 28,
            "name": "Test User API 1770050637",
            "email": "testapi1770050637@example.com",
            "email_verified_at": null,
            "role": "employe",
            "telephone": "+225062486789",
            "code_verification": "377023",
            "code_expires_at": "2026-02-02T16:54:01.000000Z",
            "telephone_verified_at": null,
            "id_entreprise": null,
            "num_box": null,
            "is_active": true,
            "created_at": "2026-02-02T16:43:58.000000Z",
            "updated_at": "2026-02-02T16:44:01.000000Z"
        }
    }
}
```

**Timestamp:** 2026-02-02 16:44:04

---

### Test 15: Mise à jour profil ❌

**Endpoint:** `PUT /api/auth/profile`

**Description:** Modifier les informations du profil utilisateur

**Données de requête:**

```json
{
    "name": "Test User API Modifié",
    "email": "testapi@example.com"
}
```

**Code de statut HTTP:** `422`

**Réponse:**

```json
{
    "success": false,
    "message": "Erreurs de validation",
    "errors": {
        "email": [
            "Cet email est déjà utilisé."
        ]
    }
}
```

**Timestamp:** 2026-02-02 16:44:05

---

### Test 16: Changer mot de passe ✅

**Endpoint:** `POST /api/auth/change-password`

**Description:** Modifier le mot de passe de l'utilisateur

**Données de requête:**

```json
{
    "current_password": "password123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "message": "Mot de passe modifié avec succès"
}
```

**Timestamp:** 2026-02-02 16:44:06

---

### Test 17: Rafraîchir token ✅

**Endpoint:** `POST /api/auth/refresh-token`

**Description:** Rafraîchir le token d'authentification et obtenir un nouveau token avec expiration d'1 mois

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "message": "Token rafraîchi avec succès",
    "data": {
        "user": {
            "id": 28,
            "name": "Test User API 1770050637",
            "email": "testapi1770050637@example.com",
            "email_verified_at": null,
            "role": "employe",
            "telephone": "+225062486789",
            "telephone_verified_at": null,
            "id_entreprise": null,
            "num_box": null,
            "is_active": true,
            "created_at": "2026-02-02T16:43:58.000000Z",
            "updated_at": "2026-02-02T16:44:06.000000Z"
        },
        "access_token": "10|DKgzo7lgNJxoAXGV6qNqKRT5aXmxfq9NFoLjRBLb1dd74da9",
        "token_type": "Bearer",
        "expires_at": "2026-03-02T16:44:07.086442Z"
    }
}
```

**Timestamp:** 2026-02-02 16:44:07

---

### Test 18: Créer commande ✅

**Endpoint:** `POST /api/commandes`

**Description:** Créer une nouvelle commande

**Données de requête:**

```json
{
    "items": [
        {
            "plat_id": 1,
            "quantite": 2,
            "accompagnements": [],
            "options": []
        }
    ],
    "montant_total": 5000,
    "lieu": "Bureau principal",
    "lat": 5.36,
    "long": -4.0083,
    "consigne_cuisinier": "Sans piment",
    "consigne_livreur": "Appeler avant d'arriver",
    "mode_paiement": "mobile_money",
    "numero_telephone": "+2250123456789"
}
```

**Code de statut HTTP:** `201`

**Réponse:**

```json
{
    "success": true,
    "message": "Commande créée avec succès",
    "data": {
        "commande": {
            "ref": "CMD-20260202-76E0B6",
            "id_employe": 28,
            "montant_total": "5000.00",
            "statut_commande": "en_attente",
            "statut_preparation": "en_attente",
            "statut_livraison": "en_attente",
            "statut_paiement": "en_attente",
            "lieu": "Bureau principal",
            "lat": "5.3600000",
            "long": "-4.0083000",
            "consigne_cuisinier": "Sans piment",
            "consigne_livreur": "Appeler avant d'arriver",
            "mode_paiement": "mobile_money",
            "numero_telephone": "+2250123456789",
            "updated_at": "2026-02-02T16:44:07.000000Z",
            "created_at": "2026-02-02T16:44:07.000000Z",
            "id": 13,
            "items": [
                {
                    "id": 14,
                    "commande_id": 13,
                    "plat_id": 1,
                    "accompagnements": [],
                    "options": [],
                    "prix": "5000.00",
                    "is_subventionne": false,
                    "quantite": 2,
                    "created_at": "2026-02-02T16:44:07.000000Z",
                    "updated_at": "2026-02-02T16:44:07.000000Z",
                    "plat": {
                        "id": 1,
                        "nom": "Attiéké Poisson Braisé",
                        "prix": "2500.00",
                        "image": null,
                        "est_disponible": true,
                        "qualite": "classic",
                        "detail": "Attiéké accompagné de poisson braisé frais et sauce tomate oignon",
                        "categorie_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                }
            ],
            "employe": {
                "id": 28,
                "name": "Test User API 1770050637",
                "email": "testapi1770050637@example.com",
                "email_verified_at": null,
                "role": "employe",
                "telephone": "+225062486789",
                "code_verification": "377023",
                "code_expires_at": "2026-02-02T16:54:01.000000Z",
                "telephone_verified_at": null,
                "id_entreprise": null,
                "num_box": null,
                "is_active": true,
                "created_at": "2026-02-02T16:43:58.000000Z",
                "updated_at": "2026-02-02T16:44:06.000000Z"
            }
        }
    }
}
```

**Timestamp:** 2026-02-02 16:44:07

---

### Test 19: Historique commandes ✅

**Endpoint:** `GET /api/commandes/history`

**Description:** Obtenir l'historique des commandes de l'utilisateur

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "commandes": [
            {
                "id": 13,
                "ref": "CMD-20260202-76E0B6",
                "id_employe": 28,
                "montant_total": "5000.00",
                "statut_commande": "en_attente",
                "statut_preparation": "en_attente",
                "statut_livraison": "en_attente",
                "lieu": "Bureau principal",
                "lat": "5.3600000",
                "long": "-4.0083000",
                "consigne_cuisinier": "Sans piment",
                "consigne_livreur": "Appeler avant d'arriver",
                "statut_paiement": "en_attente",
                "mode_paiement": "mobile_money",
                "numero_telephone": "+2250123456789",
                "created_at": "2026-02-02T16:44:07.000000Z",
                "updated_at": "2026-02-02T16:44:07.000000Z",
                "items": [
                    {
                        "id": 14,
                        "commande_id": 13,
                        "plat_id": 1,
                        "accompagnements": [],
                        "options": [],
                        "prix": "5000.00",
                        "is_subventionne": false,
                        "quantite": 2,
                        "created_at": "2026-02-02T16:44:07.000000Z",
                        "updated_at": "2026-02-02T16:44:07.000000Z",
                        "plat": {
                            "id": 1,
                            "nom": "Attiéké Poisson Braisé",
                            "prix": "2500.00",
                            "image": null,
                            "est_disponible": true,
                            "qualite": "classic",
                            "detail": "Attiéké accompagné de poisson braisé frais et sauce tomate oignon",
                            "categorie_id": 1,
                            "created_at": "2026-01-28T03:17:39.000000Z",
                            "updated_at": "2026-01-28T03:17:39.000000Z"
                        }
                    }
                ],
                "livraison": null
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 15,
            "total": 1
        }
    }
}
```

**Timestamp:** 2026-02-02 16:44:07

---

### Test 20: Historique commandes (avec filtres) ✅

**Endpoint:** `GET /api/commandes/history?statut_commande=en_attente&per_page=10`

**Description:** Obtenir l'historique avec filtres de statut

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "commandes": [
            {
                "id": 13,
                "ref": "CMD-20260202-76E0B6",
                "id_employe": 28,
                "montant_total": "5000.00",
                "statut_commande": "en_attente",
                "statut_preparation": "en_attente",
                "statut_livraison": "en_attente",
                "lieu": "Bureau principal",
                "lat": "5.3600000",
                "long": "-4.0083000",
                "consigne_cuisinier": "Sans piment",
                "consigne_livreur": "Appeler avant d'arriver",
                "statut_paiement": "en_attente",
                "mode_paiement": "mobile_money",
                "numero_telephone": "+2250123456789",
                "created_at": "2026-02-02T16:44:07.000000Z",
                "updated_at": "2026-02-02T16:44:07.000000Z",
                "items": [
                    {
                        "id": 14,
                        "commande_id": 13,
                        "plat_id": 1,
                        "accompagnements": [],
                        "options": [],
                        "prix": "5000.00",
                        "is_subventionne": false,
                        "quantite": 2,
                        "created_at": "2026-02-02T16:44:07.000000Z",
                        "updated_at": "2026-02-02T16:44:07.000000Z",
                        "plat": {
                            "id": 1,
                            "nom": "Attiéké Poisson Braisé",
                            "prix": "2500.00",
                            "image": null,
                            "est_disponible": true,
                            "qualite": "classic",
                            "detail": "Attiéké accompagné de poisson braisé frais et sauce tomate oignon",
                            "categorie_id": 1,
                            "created_at": "2026-01-28T03:17:39.000000Z",
                            "updated_at": "2026-01-28T03:17:39.000000Z"
                        }
                    }
                ],
                "livraison": null
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 10,
            "total": 1
        }
    }
}
```

**Timestamp:** 2026-02-02 16:44:08

---

### Test 21: Détails commande ✅

**Endpoint:** `GET /api/commandes/13`

**Description:** Obtenir les détails d'une commande spécifique

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "commande": {
            "id": 13,
            "ref": "CMD-20260202-76E0B6",
            "id_employe": 28,
            "montant_total": "5000.00",
            "statut_commande": "en_attente",
            "statut_preparation": "en_attente",
            "statut_livraison": "en_attente",
            "lieu": "Bureau principal",
            "lat": "5.3600000",
            "long": "-4.0083000",
            "consigne_cuisinier": "Sans piment",
            "consigne_livreur": "Appeler avant d'arriver",
            "statut_paiement": "en_attente",
            "mode_paiement": "mobile_money",
            "numero_telephone": "+2250123456789",
            "created_at": "2026-02-02T16:44:07.000000Z",
            "updated_at": "2026-02-02T16:44:07.000000Z",
            "items": [
                {
                    "id": 14,
                    "commande_id": 13,
                    "plat_id": 1,
                    "accompagnements": [],
                    "options": [],
                    "prix": "5000.00",
                    "is_subventionne": false,
                    "quantite": 2,
                    "created_at": "2026-02-02T16:44:07.000000Z",
                    "updated_at": "2026-02-02T16:44:07.000000Z",
                    "plat": {
                        "id": 1,
                        "nom": "Attiéké Poisson Braisé",
                        "prix": "2500.00",
                        "image": null,
                        "est_disponible": true,
                        "qualite": "classic",
                        "detail": "Attiéké accompagné de poisson braisé frais et sauce tomate oignon",
                        "categorie_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                }
            ],
            "livraison": null,
            "paiements": []
        }
    }
}
```

**Timestamp:** 2026-02-02 16:44:08

---

### Test 22: Liste favoris ✅

**Endpoint:** `GET /api/favoris`

**Description:** Obtenir tous les plats favoris de l'utilisateur

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "favoris": [],
        "total": 0
    }
}
```

**Timestamp:** 2026-02-02 16:44:08

---

### Test 23: Ajouter favori ✅

**Endpoint:** `POST /api/favoris`

**Description:** Ajouter un plat aux favoris

**Données de requête:**

```json
{
    "plat_id": 1
}
```

**Code de statut HTTP:** `201`

**Réponse:**

```json
{
    "success": true,
    "message": "Plat ajouté aux favoris",
    "data": {
        "favori": {
            "user_id": 28,
            "plat_id": 1,
            "updated_at": "2026-02-02T16:44:09.000000Z",
            "created_at": "2026-02-02T16:44:09.000000Z",
            "id": 4,
            "plat": {
                "id": 1,
                "nom": "Attiéké Poisson Braisé",
                "prix": "2500.00",
                "image": null,
                "est_disponible": true,
                "qualite": "classic",
                "detail": "Attiéké accompagné de poisson braisé frais et sauce tomate oignon",
                "categorie_id": 1,
                "created_at": "2026-01-28T03:17:39.000000Z",
                "updated_at": "2026-01-28T03:17:39.000000Z",
                "categorie": {
                    "id": 1,
                    "nom": "Plats Africains",
                    "logo": null,
                    "est_disponible": true,
                    "qualite": "classic",
                    "created_at": "2026-01-28T03:17:39.000000Z",
                    "updated_at": "2026-01-28T03:17:39.000000Z"
                },
                "accompagnements": [
                    {
                        "id": 1,
                        "nom": "Alloco",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "500.00",
                        "disponible": true,
                        "plat_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    },
                    {
                        "id": 2,
                        "nom": "Salade",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "300.00",
                        "disponible": true,
                        "plat_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ],
                "options": [
                    {
                        "id": 1,
                        "nom": "Sauce pimentée",
                        "image": null,
                        "qte_gratuit": 1,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    },
                    {
                        "id": 2,
                        "nom": "Citron",
                        "image": null,
                        "qte_gratuit": 2,
                        "prix_unitaire": "0.00",
                        "disponible": true,
                        "plat_id": 1,
                        "created_at": "2026-01-28T03:17:39.000000Z",
                        "updated_at": "2026-01-28T03:17:39.000000Z"
                    }
                ]
            }
        }
    }
}
```

**Timestamp:** 2026-02-02 16:44:09

---

### Test 24: Vérifier favori ✅

**Endpoint:** `GET /api/favoris/check/1`

**Description:** Vérifier si un plat est dans les favoris

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "data": {
        "is_favori": true
    }
}
```

**Timestamp:** 2026-02-02 16:44:09

---

### Test 25: Retirer favori ✅

**Endpoint:** `DELETE /api/favoris/1`

**Description:** Retirer un plat des favoris

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "message": "Plat retiré des favoris"
}
```

**Timestamp:** 2026-02-02 16:44:09

---

### Test 26: Déconnexion ✅

**Endpoint:** `POST /api/auth/logout`

**Description:** Se déconnecter et supprimer le token

**Code de statut HTTP:** `200`

**Réponse:**

```json
{
    "success": true,
    "message": "Déconnexion réussie"
}
```

**Timestamp:** 2026-02-02 16:44:10

---

## 📋 Résumé par Catégorie

### Authentification

- **Tests:** 9
- **Réussis:** 6 ✅
- **Échoués:** 3 ❌

### Menu

- **Tests:** 9
- **Réussis:** 9 ✅
- **Échoués:** 0 ❌

### Commandes

- **Tests:** 4
- **Réussis:** 4 ✅
- **Échoués:** 0 ❌

### Favoris

- **Tests:** 4
- **Réussis:** 4 ✅
- **Échoués:** 0 ❌

## 🔧 Commandes cURL pour Reproduction

### Authentification

```bash
# Inscription
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","telephone":"+2250123456789","password":"password123","password_confirmation":"password123"}'

# Connexion
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

```

### Menu

```bash
# Liste des catégories
curl -X GET "http://localhost:8000/api/menu/categories"

# Liste des plats
curl -X GET "http://localhost:8000/api/menu/plats"

# Détails d'un plat
curl -X GET "http://localhost:8000/api/menu/plats/1"

```

### Commandes (nécessite authentification)

```bash
# Créer une commande
curl -X POST "http://localhost:8000/api/commandes" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"items":[{"plat_id":1,"quantite":2}],"montant_total":5000,"mode_paiement":"mobile_money"}'

```

