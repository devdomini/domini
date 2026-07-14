# Vérification du Sender Name Orange SMS

## ✅ Format Corrigé

Le code utilise maintenant correctement :
- **Sender Name** : `SMS 487507`
- **Format dans l'URL** : `tel:SMS+487507` (espaces encodés en `+`)
- **Format dans le body** : `tel:SMS+487507` (identique à l'URL)

## ⚠️ Erreur POL2204

L'erreur `POL2204` indique un problème d'autorisation avec le sender name.

### Causes possibles :

1. **Le sender name n'est pas configuré dans votre compte Orange Developer**
   - Connectez-vous à https://developer.orange.com
   - Vérifiez dans votre dashboard les sender names autorisés
   - Le sender name doit être exactement `SMS 487507` (avec l'espace)

2. **Le sender name n'est pas activé pour l'API SMS**
   - Vérifiez que l'API SMS est activée pour votre compte
   - Vérifiez que le sender name est associé à votre application

3. **Format incorrect du sender name**
   - Peut-être que le format attendu est différent (sans espace, avec tiret, etc.)
   - Vérifiez la documentation Orange pour le format exact

4. **Permissions insuffisantes**
   - Vérifiez que votre compte a les permissions pour utiliser ce sender name
   - Contactez le support Orange si nécessaire

## 🔍 Vérifications à Faire

### 1. Dashboard Orange Developer

1. Connectez-vous à https://developer.orange.com
2. Allez dans votre application
3. Vérifiez la section "SMS" ou "Sender Names"
4. Confirmez que `SMS 487507` est listé et activé

### 2. Documentation Orange

Consultez la documentation officielle :
- https://developer.orange.com/apis/sms-ci/
- Vérifiez le format exact du senderAddress pour les sender names

### 3. Support Orange

Si le problème persiste :
- Contactez le support Orange Developer
- Mentionnez l'erreur `POL2204`
- Fournissez votre sender name : `SMS 487507`

## 📝 Configuration Actuelle

Dans `config/services.php` :
```php
'sender_name' => env('ORANGE_SENDER_NAME', 'SMS 487507'),
```

Dans `.env` (si vous voulez le modifier) :
```env
ORANGE_SENDER_NAME=SMS 487507
```

## 🧪 Test

Le format est maintenant correct. Testez avec :
```bash
powershell -ExecutionPolicy Bypass -File diagnostic_orange_sms.ps1
```

Si l'erreur `POL2204` persiste, c'est un problème de configuration/autorisation côté Orange, pas un problème de code.
