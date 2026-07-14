# Guide de Test SMS

## 📱 Numéros de test
- **0576009958**
- **0748526787**

## ⚠️ Prérequis

**IMPORTANT** : Ces numéros doivent exister dans la base de données avant de pouvoir recevoir des SMS.

### Vérifier si les numéros existent

```sql
SELECT id, name, telephone FROM users WHERE telephone IN ('0576009958', '0748526787');
```

### Créer des utilisateurs de test (si nécessaire)

Via l'API :
```bash
curl -X POST "http://localhost:8000/api/auth/register" \
  -H "Content-Type: application/json" \
  -d "{\"name\": \"Test User 1\", \"telephone\": \"0576009958\", \"password\": \"password123\", \"password_confirmation\": \"password123\"}"
```

## 🧪 Scripts de test

### 1. Test complet (tous les numéros)

**Windows :**
```bash
test_sms.bat
```

**Linux/Mac :**
```bash
chmod +x test_sms.sh
./test_sms.sh
```

Ce script teste :
- ✅ Renvoi de code de vérification pour les 2 numéros
- ✅ Mot de passe oublié pour les 2 numéros

### 2. Test individuel (un seul numéro)

**Windows :**
```bash
test_sms_individual.bat 0576009958
test_sms_individual.bat 0748526787
```

## 📋 Endpoints testés

### 1. Renvoyer code de vérification
**Endpoint :** `POST /api/auth/resend-verification-code`

**Body :**
```json
{
  "telephone": "0576009958"
}
```

**Réponse attendue :**
```json
{
  "success": true,
  "message": "Code de vérification renvoyé avec succès"
}
```

**SMS reçu :**
```
Votre code de vérification Domini est: 123456. Valide 10 minutes.
```

### 2. Mot de passe oublié

**Endpoint :** `POST /api/auth/forgot-password`

**Body :**
```json
{
  "telephone": "0576009958"
}
```

**Réponse attendue :**
```json
{
  "success": true,
  "message": "Code de réinitialisation envoyé par SMS"
}
```

**SMS reçu :**
```
Votre code de réinitialisation Domini est: 123456. Valide 10 minutes.
```

## 🔍 Vérifications

### 1. Vérifier que le serveur Laravel est démarré

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. Vérifier les logs Laravel

```bash
tail -f storage/logs/laravel.log
```

Cherchez les entrées :
- `Orange SMS - SMS envoyé avec succès`
- `Orange SMS - Erreur lors de l'envoi du SMS`

### 3. Vérifier dans la base de données

```sql
-- Vérifier les codes générés
SELECT id, name, telephone, code_verification, code_expires_at 
FROM users 
WHERE telephone IN ('0576009958', '0748526787');
```

## 🐛 Dépannage

### Erreur : "Ce numéro de téléphone n'existe pas"

**Solution :** Créer un compte avec ce numéro via l'API `/api/auth/register`

### Erreur : "Impossible d'obtenir le token d'accès Orange"

**Solution :** Vérifier la configuration Orange dans `.env` :
```env
ORANGE_CLIENT_ID=XmvK8mAW1FoFaaK0IHlsbmWt8nceqs5C
ORANGE_CLIENT_SECRET=pMN8qnvQz9m5G632ukLrMfvzTEW9Kh4AM8JiqKJrRGdI
ORANGE_SENDER_NUMBER=00000000
```

### SMS non reçu

1. **Vérifier les logs** : `storage/logs/laravel.log`
2. **Vérifier le format du numéro** : Le service formate automatiquement en `+225XXXXXXXXX`
3. **Vérifier le crédit Orange** : S'assurer que le compte Orange a des crédits SMS
4. **Vérifier le numéro d'envoi** : `ORANGE_SENDER_NUMBER` dans `.env`

## 📝 Commandes curl manuelles

### Test 1 : Renvoyer code pour 0576009958

```bash
curl -X POST "http://localhost:8000/api/auth/resend-verification-code" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"telephone\": \"0576009958\"}"
```

### Test 2 : Renvoyer code pour 0748526787

```bash
curl -X POST "http://localhost:8000/api/auth/resend-verification-code" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"telephone\": \"0748526787\"}"
```

### Test 3 : Mot de passe oublié pour 0576009958

```bash
curl -X POST "http://localhost:8000/api/auth/forgot-password" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"telephone\": \"0576009958\"}"
```

### Test 4 : Mot de passe oublié pour 0748526787

```bash
curl -X POST "http://localhost:8000/api/auth/forgot-password" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"telephone\": \"0748526787\"}"
```

## ✅ Checklist de test

- [ ] Serveur Laravel démarré sur `0.0.0.0:8000`
- [ ] Les numéros existent dans la base de données
- [ ] Configuration Orange SMS correcte dans `.env`
- [ ] Exécuter `test_sms.bat`
- [ ] Vérifier la réception des SMS sur les téléphones
- [ ] Vérifier les logs Laravel pour les erreurs éventuelles
- [ ] Vérifier les codes dans la base de données
