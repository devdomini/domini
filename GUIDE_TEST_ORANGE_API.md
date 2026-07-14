# Guide de Test API Orange SMS Directement

## 📋 Configuration

- **Base URL** : `https://api.orange.com`
- **Authorization Header** : `Basic WG12SzhtQVcxRm9GYWFLMElIbHNibVd0OG5jZXFzNUM6cE1OOHFudlF6OW01RzYzMnVrTHJNZnZ6VEVXOUtoNEFNOEppcUtKclJHZEk=`
- **Numéros de test** : `0576009958`, `0748526787`
- **Sender Number** : `00000000` (à remplacer par votre numéro réel)

## 🔑 Étape 1 : Obtenir le Token d'Accès

### Windows (Batch)
```bash
test_orange_get_token.bat
```

### Windows (PowerShell) - RECOMMANDÉ
```powershell
.\test_orange_powershell.ps1
```

### Linux/Mac (curl)
```bash
curl -X POST "https://api.orange.com/oauth/v3/token" \
  -H "Authorization: Basic WG12SzhtQVcxRm9GYWFLMElIbHNibVd0OG5jZXFzNUM6cE1OOHFudlF6OW01RzYzMnVrTHJNZnZ6VEVXOUtoNEFNOEppcUtKclJHZEk=" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "grant_type=client_credentials"
```

**Réponse attendue :**
```json
{
  "access_token": "votre_token_ici",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

## 📱 Étape 2 : Envoyer un SMS

### Format du Numéro
- **Entrée** : `0576009958` (format local)
- **Formaté** : `225576009958` (avec indicatif pays)

### Windows (Batch avec token)
```bash
test_orange_send_sms.bat YOUR_TOKEN_HERE 0576009958 "Message de test"
```

### Windows (PowerShell) - RECOMMANDÉ
```powershell
.\test_orange_powershell.ps1
```

### Linux/Mac (curl)
```bash
TOKEN="votre_token_ici"
PHONE="225576009958"  # Formaté depuis 0576009958
SENDER="tel:+22500000000"
MESSAGE="Test SMS Domini"

curl -X POST "https://api.orange.com/smsmessaging/v1/outbound/$SENDER/requests" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"outboundSMSMessageRequest\": {
      \"address\": \"tel:$PHONE\",
      \"senderAddress\": \"$SENDER\",
      \"outboundSMSTextMessage\": {
        \"message\": \"$MESSAGE\"
      }
    }
  }"
```

## 📝 Scripts Disponibles

### 1. `test_orange_get_token.bat`
Obtient uniquement le token d'accès Orange.

### 2. `test_orange_send_sms.bat`
Envoie un SMS avec un token fourni.
**Usage :** `test_orange_send_sms.bat [TOKEN] [NUMERO] [MESSAGE]`

### 3. `test_orange_complete.bat`
Test complet (token + envoi SMS) - nécessite copier-coller du token.

### 4. `test_orange_powershell.ps1` ⭐ RECOMMANDÉ
Script PowerShell qui automatise tout :
- Obtient le token automatiquement
- Parse le JSON
- Envoie les SMS aux 2 numéros
- Affiche les résultats

**Exécution :**
```powershell
powershell -ExecutionPolicy Bypass -File test_orange_powershell.ps1
```

## 🔍 Formatage des Numéros

Le service formate automatiquement :
- `0576009958` → `225576009958`
- `0748526787` → `225748526787`
- `+2250576009958` → `225576009958`
- `2250576009958` → `225576009958` (déjà formaté)

## 📊 Structure de la Requête SMS

```json
{
  "outboundSMSMessageRequest": {
    "address": "tel:225576009958",
    "senderAddress": "tel:+22500000000",
    "outboundSMSTextMessage": {
      "message": "Votre message ici"
    }
  }
}
```

## ✅ Réponse Succès

```json
{
  "outboundSMSMessageRequest": {
    "resourceURL": "https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B22500000000/requests/..."
  }
}
```

## ❌ Erreurs Possibles

### 401 Unauthorized
- Token invalide ou expiré
- **Solution** : Obtenir un nouveau token

### 400 Bad Request
- Format de numéro incorrect
- **Solution** : Vérifier le formatage (doit être `225XXXXXXXXX`)

### 403 Forbidden
- Crédits insuffisants
- Numéro d'envoi non autorisé
- **Solution** : Vérifier votre compte Orange

## 🧪 Test Complet Automatisé

### PowerShell (Recommandé)
```powershell
.\test_orange_powershell.ps1
```

Ce script :
1. ✅ Obtient le token automatiquement
2. ✅ Formate les numéros
3. ✅ Envoie les SMS aux 2 numéros
4. ✅ Affiche les résultats

## 📋 Checklist de Test

- [ ] Exécuter `test_orange_get_token.bat` pour obtenir le token
- [ ] Copier le `access_token` de la réponse
- [ ] Exécuter `test_orange_send_sms.bat` avec le token
- [ ] Vérifier la réception des SMS sur les téléphones
- [ ] Ou utiliser directement `test_orange_powershell.ps1` (automatique)

## 🔧 Dépannage

### Le token expire rapidement
- Les tokens Orange expirent après 1 heure
- Obtenez un nouveau token si nécessaire

### SMS non reçu
1. Vérifier le format du numéro (doit être `225XXXXXXXXX`)
2. Vérifier les crédits Orange
3. Vérifier que le numéro d'envoi est correct (`ORANGE_SENDER_NUMBER`)
4. Vérifier les logs Orange (si disponibles)

### Erreur de parsing JSON
- Utilisez PowerShell (`test_orange_powershell.ps1`) qui gère mieux le JSON
- Ou utilisez `jq` sur Linux/Mac pour parser le JSON
