# 📱 Intégration Orange SMS - Vérification par SMS

## ✅ Modifications Effectuées

### 1. Service Orange SMS (`app/Services/OrangeSmsService.php`)

Service créé pour gérer l'envoi de SMS via l'API Orange :

**Fonctionnalités :**
- ✅ Obtention automatique du token d'accès Orange
- ✅ Envoi de SMS avec formatage automatique des numéros
- ✅ Génération de codes de vérification à 6 chiffres
- ✅ Gestion des erreurs et logging

**Configuration :**
- Client ID: `XmvK8mAW1FoFaaK0IHlsbmWt8nceqs5C`
- Client Secret: `pMN8qnvQz9m5G632ukLrMfvzTEW9Kh4AM8JiqKJrRGdI`
- Authorization Header: `Basic WG12SzhtQVcxRm9GYWFLMElIbHNibVd0OG5jZXFzNUM6cE1OOHFudlF6OW01RzYzMnVrTHJNZnZ6VEVXOUtoNEFNOEppcUtKclJHZEk=`

### 2. Modèle User (`app/Models/User.php`)

**Champs ajoutés :**
- `code_verification` (string, 6 caractères) - Code de vérification SMS
- `code_expires_at` (timestamp) - Date d'expiration du code (10 minutes)
- `telephone_verified_at` (timestamp) - Date de vérification du téléphone

### 3. Migration (`database/migrations/2026_02_02_161512_add_code_verification_to_users_table.php`)

Migration créée pour ajouter les colonnes nécessaires à la table `users`.

**Pour exécuter :**
```bash
php artisan migrate
```

### 4. Contrôleur AuthApiController (`app/Http/Controllers/Api/AuthApiController.php`)

**Modifications apportées :**

#### a) Méthode `register()` - Modifiée
- ✅ Génère automatiquement un code de vérification à 6 chiffres
- ✅ Envoie un SMS avec le code lors de l'inscription
- ✅ Stocke le code et sa date d'expiration (10 minutes)
- ✅ Le code n'est **pas** retourné dans la réponse JSON (sécurité)

#### b) Méthode `verifyPhone()` - Modifiée
- ✅ Vérifie que le code existe
- ✅ Vérifie que le code n'a pas expiré
- ✅ Vérifie que le code correspond
- ✅ Marque le téléphone comme vérifié (`telephone_verified_at`)
- ✅ Supprime le code après vérification réussie

#### c) Nouvelle méthode `resendVerificationCode()` - Ajoutée
- ✅ Génère un nouveau code de vérification
- ✅ Envoie un nouveau SMS avec le code
- ✅ Met à jour le code et sa date d'expiration

### 5. Routes API (`routes/api.php`)

**Nouvelle route ajoutée :**
```php
Route::post('/auth/resend-verification-code', [AuthApiController::class, 'resendVerificationCode']);
```

### 6. Configuration (`config/services.php`)

Configuration Orange SMS ajoutée :
```php
'orange' => [
    'client_id' => env('ORANGE_CLIENT_ID', 'XmvK8mAW1FoFaaK0IHlsbmWt8nceqs5C'),
    'client_secret' => env('ORANGE_CLIENT_SECRET', 'pMN8qnvQz9m5G632ukLrMfvzTEW9Kh4AM8JiqKJrRGdI'),
    'authorization_header' => env('ORANGE_AUTH_HEADER', 'Basic WG12SzhtQVcxRm9GYWFLMElIbHNibVd0OG5jZXFzNUM6cE1OOHFudlF6OW01RzYzMnVrTHJNZnZ6VEVXOUtoNEFNOEppcUtKclJHZEk='),
    'sender_name' => env('ORANGE_SENDER_NAME', 'DOMINI'),
    'sender_number' => env('ORANGE_SENDER_NUMBER', '00000000'),
],
```

### 7. Collection Postman (`Domini_API_Collection.postman_collection.json`)

✅ Collection mise à jour avec la nouvelle route `resend-verification-code`

---

## 🔄 Workflow Complet

### 1. Inscription
```
POST /api/auth/register
{
  "name": "Jean Dupont",
  "email": "jean@example.com",
  "telephone": "+2250123456789",
  "password": "password123",
  "password_confirmation": "password123"
}

→ Code généré et envoyé par SMS
→ Réponse: Token + User (sans le code)
```

### 2. Vérification du téléphone
```
POST /api/auth/verify-phone
{
  "telephone": "+2250123456789",
  "code": "123456"
}

→ Vérifie le code
→ Marque le téléphone comme vérifié
```

### 3. Renvoyer le code (si nécessaire)
```
POST /api/auth/resend-verification-code
{
  "telephone": "+2250123456789"
}

→ Génère un nouveau code
→ Envoie un nouveau SMS
```

---

## 📝 Format des Messages SMS

**Lors de l'inscription :**
```
Votre code de vérification Domini est: 123456. Valide 10 minutes.
```

**Lors du renvoi :**
```
Votre code de vérification Domini est: 654321. Valide 10 minutes.
```

---

## 🔒 Sécurité

1. **Code masqué** : Le code n'est jamais retourné dans les réponses API
2. **Expiration** : Les codes expirent après 10 minutes
3. **Validation** : Vérification stricte du code avant validation
4. **Suppression** : Le code est supprimé après vérification réussie

---

## ⚙️ Configuration Orange SMS

### Variables d'environnement (optionnel)

Vous pouvez ajouter ces variables dans votre `.env` :

```env
ORANGE_CLIENT_ID=XmvK8mAW1FoFaaK0IHlsbmWt8nceqs5C
ORANGE_CLIENT_SECRET=pMN8qnvQz9m5G632ukLrMfvzTEW9Kh4AM8JiqKJrRGdI
ORANGE_AUTH_HEADER=Basic WG12SzhtQVcxRm9GYWFLMElIbHNibVd0OG5jZXFzNUM6cE1OOHFudlF6OW01RzYzMnVrTHJNZnZ6VEVXOUtoNEFNOEppcUtKclJHZEk=
ORANGE_SENDER_NAME=DOMINI
ORANGE_SENDER_NUMBER=00000000
```

**Note :** Remplacez `ORANGE_SENDER_NUMBER` par votre numéro d'envoi Orange réel.

---

## 🧪 Tests

### Test d'inscription avec SMS
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "telephone": "+2250123456789",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Test de vérification
```bash
curl -X POST http://localhost:8000/api/auth/verify-phone \
  -H "Content-Type: application/json" \
  -d '{
    "telephone": "+2250123456789",
    "code": "123456"
  }'
```

### Test de renvoi de code
```bash
curl -X POST http://localhost:8000/api/auth/resend-verification-code \
  -H "Content-Type: application/json" \
  -d '{
    "telephone": "+2250123456789"
  }'
```

---

## 📊 Format des Réponses

### Inscription réussie
```json
{
  "success": true,
  "message": "Inscription réussie. Un code de vérification a été envoyé à votre numéro de téléphone.",
  "sms_sent": true,
  "sms_message": "SMS envoyé avec succès",
  "data": {
    "user": {
      "id": 1,
      "name": "Jean Dupont",
      "email": "jean@example.com",
      "telephone": "+2250123456789",
      ...
    },
    "token": "1|xxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

### Vérification réussie
```json
{
  "success": true,
  "message": "Numéro de téléphone vérifié avec succès",
  "data": {
    "user": {
      "id": 1,
      "telephone_verified_at": "2026-01-28T10:30:00.000000Z",
      ...
    }
  }
}
```

### Erreurs possibles
```json
{
  "success": false,
  "message": "Code de vérification incorrect"
}
```

```json
{
  "success": false,
  "message": "Le code de vérification a expiré. Veuillez demander un nouveau code."
}
```

---

## 🚀 Prochaines Étapes

1. **Exécuter la migration** :
   ```bash
   php artisan migrate
   ```

2. **Configurer le numéro d'envoi Orange** :
   - Modifier `ORANGE_SENDER_NUMBER` dans `.env` ou `config/services.php`
   - Utiliser votre numéro Orange réel

3. **Tester l'envoi de SMS** :
   - Créer un compte de test
   - Vérifier la réception du SMS
   - Tester la vérification du code

4. **Surveiller les logs** :
   - Les erreurs Orange SMS sont loggées dans `storage/logs/laravel.log`

---

## 📌 Notes Importantes

- ⏱️ **Durée de validité** : Les codes expirent après **10 minutes**
- 🔄 **Renvoi** : Un nouveau code peut être demandé à tout moment
- 📱 **Format téléphone** : Accepte les formats `+2250123456789`, `2250123456789`, `0123456789`
- 🔒 **Sécurité** : Le code n'est jamais exposé dans les réponses API
- 📝 **Logs** : Toutes les erreurs sont enregistrées pour le débogage

---

**Intégration terminée ! 🎉**
