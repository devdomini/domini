# Guide : Autoriser le port 8000 dans le pare-feu Windows

## Problème
L'erreur "connexion refusée" indique que le pare-feu Windows bloque le port 8000.

## Solution rapide (Script automatique)

1. **Double-cliquez** sur `autoriser_port_8000.bat`
2. **Clic droit** → **"Exécuter en tant qu'administrateur"**
3. Le script ajoutera automatiquement la règle de pare-feu

## Solution manuelle

### Méthode 1 : Via l'interface graphique

1. Ouvrez **Panneau de configuration** → **Pare-feu Windows Defender**
2. Cliquez sur **Paramètres avancés** (à gauche)
3. Cliquez sur **Règles de trafic entrant** (à gauche)
4. Cliquez sur **Nouvelle règle...** (à droite)
5. Sélectionnez **Port** → **Suivant**
6. Sélectionnez **TCP** et entrez **8000** dans "Ports locaux spécifiques" → **Suivant**
7. Sélectionnez **Autoriser la connexion** → **Suivant**
8. Cochez tous les profils (Domaine, Privé, Public) → **Suivant**
9. Nommez la règle : **"Laravel API Port 8000"** → **Terminer**

### Méthode 2 : Via PowerShell (en tant qu'administrateur)

```powershell
netsh advfirewall firewall add rule name="Laravel API Port 8000" dir=in action=allow protocol=TCP localport=8000
```

## Vérification

1. Exécutez `verifier_port_8000.bat` pour vérifier que :
   - La règle de pare-feu existe
   - Le port 8000 est en écoute
   - Le serveur répond correctement

2. Testez depuis votre téléphone :
   - Ouvrez un navigateur sur votre téléphone
   - Accédez à : `http://192.168.1.134:8000/api/entreprises`
   - Vous devriez voir la réponse JSON

## Dépannage

### Si le port est toujours bloqué :

1. **Vérifiez que le serveur Laravel est démarré** :
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

2. **Vérifiez que vous êtes sur le même réseau Wi-Fi** :
   - PC et téléphone doivent être sur le même réseau

3. **Vérifiez l'IP dans `api_config.dart`** :
   - Doit correspondre à votre IP locale (actuellement : `192.168.1.134`)

4. **Désactivez temporairement le pare-feu** (pour tester uniquement) :
   - Panneau de configuration → Pare-feu Windows Defender
   - Désactiver temporairement (à réactiver après les tests !)

## Notes importantes

- ⚠️ **Ne laissez jamais le pare-feu complètement désactivé en production**
- ✅ Il est préférable d'ajouter une règle spécifique pour le port 8000
- 🔒 En production, utilisez un serveur web professionnel (Apache/Nginx) avec HTTPS
