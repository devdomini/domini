# Résolution du problème de Timeout de connexion

## Problème
L'application Flutter affiche : "Timeout de connexion" lors du chargement des entreprises.

## Diagnostic

### ✅ État actuel
- ✅ Serveur Laravel démarré sur `0.0.0.0:8000` (4 processus détectés)
- ✅ IP locale : `192.168.1.134`
- ⚠️ Timeout de connexion depuis le téléphone

## Solutions à essayer (dans l'ordre)

### 1. Autoriser le port 8000 dans le pare-feu ⚠️ PRIORITAIRE

**Exécutez en tant qu'administrateur :**
```bash
autoriser_port_8000.bat
```

Ou manuellement :
1. Panneau de configuration → Pare-feu Windows Defender
2. Paramètres avancés → Règles de trafic entrant
3. Nouvelle règle → Port → TCP → 8000 → Autoriser

### 2. Vérifier que le téléphone et le PC sont sur le même réseau Wi-Fi

**Sur le téléphone :**
- Paramètres → Wi-Fi → Vérifiez le nom du réseau
- Il doit être identique à celui du PC

**Sur le PC :**
- Vérifiez le nom du réseau Wi-Fi dans les paramètres réseau

### 3. Tester la connexion depuis le téléphone

**Ouvrez un navigateur sur votre téléphone et accédez à :**
```
http://192.168.1.134:8000/api/entreprises
```

**Résultats possibles :**
- ✅ **JSON affiché** → Le problème vient de l'app Flutter
- ❌ **Timeout/Erreur** → Problème réseau/pare-feu
- ❌ **Page blanche** → Le serveur ne répond pas correctement

### 4. Vérifier l'IP dans l'application Flutter

**Fichier :** `dominimobile/lib/config/api_config.dart`

**Doit contenir :**
```dart
static const String baseUrl = 'http://192.168.1.134:8000/api';
```

### 5. Redémarrer proprement le serveur

**Exécutez :**
```bash
restart_server.bat
```

Cela va :
- Arrêter tous les processus PHP
- Nettoyer les caches
- Redémarrer le serveur proprement

### 6. Vérifier que le serveur répond en local

**Sur le PC, testez :**
```bash
curl http://localhost:8000/api/entreprises
```

**Ou ouvrez dans un navigateur :**
```
http://localhost:8000/api/entreprises
```

### 7. Diagnostic complet

**Exécutez le script de diagnostic :**
```bash
diagnostic_complet.bat
```

Ce script vérifie :
- L'IP locale
- Le port 8000
- Les règles de pare-feu
- La connexion locale
- La connexion avec l'IP locale

## Solutions spécifiques selon le type d'appareil

### 📱 Émulateur Android

**Dans `api_config.dart`, utilisez :**
```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

### 📱 Appareil physique Android/iOS

**Dans `api_config.dart`, utilisez votre IP locale :**
```dart
static const String baseUrl = 'http://192.168.1.134:8000/api';
```

**Important :** L'appareil et le PC doivent être sur le même réseau Wi-Fi.

## Vérifications supplémentaires

### Vérifier que le serveur écoute sur toutes les interfaces

Le serveur doit être démarré avec :
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**Vérification :**
```bash
netstat -an | findstr ":8000"
```

**Doit afficher :**
```
TCP    0.0.0.0:8000           0.0.0.0:0              LISTENING
```

### Vérifier les processus PHP

**Arrêtez tous les processus PHP en double :**
```bash
taskkill /F /IM php.exe
```

Puis redémarrez avec `restart_server.bat`.

## Timeout augmenté

J'ai augmenté le timeout dans `api_config.dart` de 30 à 60 secondes pour les connexions lentes.

## Si rien ne fonctionne

1. **Désactivez temporairement le pare-feu** (pour tester uniquement)
2. **Vérifiez les logs Laravel** : `storage/logs/laravel.log`
3. **Vérifiez les logs Flutter** dans la console de développement
4. **Testez avec un autre appareil** pour isoler le problème

## Commandes utiles

```bash
# Vérifier l'IP
ipconfig | findstr IPv4

# Vérifier le port 8000
netstat -an | findstr ":8000"

# Tester la connexion locale
curl http://localhost:8000/api/entreprises

# Tester avec l'IP locale
curl http://192.168.1.134:8000/api/entreprises

# Arrêter tous les processus PHP
taskkill /F /IM php.exe

# Redémarrer le serveur
restart_server.bat
```
