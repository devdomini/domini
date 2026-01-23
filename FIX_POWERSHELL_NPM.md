# 🔧 Résolution : Erreur PowerShell npm

## ❌ Erreur Rencontrée

```
npm : Impossible de charger le fichier C:\Program Files\nodejs\npm.ps1, 
car l'exécution de scripts est désactivée sur ce système.
```

---

## ✅ Solution Rapide

### Méthode 1 : Changer la Politique d'Exécution (RÉSOLU ✅)

Ouvrez PowerShell en **Administrateur** et exécutez :

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

Puis réessayez :
```powershell
npm install
```

**✅ C'est résolu !** Les dépendances npm sont maintenant installées.

---

## 🔍 Explications

### Qu'est-ce que l'Execution Policy ?

Windows PowerShell a une politique de sécurité qui empêche l'exécution de scripts par défaut.

### Les Différentes Politiques

- **Restricted** (défaut) : Aucun script ne peut s'exécuter
- **RemoteSigned** (recommandé) : Scripts locaux OK, scripts téléchargés doivent être signés
- **Unrestricted** : Tous les scripts peuvent s'exécuter (moins sécurisé)
- **Bypass** : Aucune restriction (utilisé par certains outils)

### Pourquoi cette erreur avec npm ?

NPM utilise des scripts PowerShell (`.ps1`) pour fonctionner sur Windows. Si la politique est trop restrictive, ces scripts ne peuvent pas s'exécuter.

---

## 🛡️ Alternatives Sécurisées

### Méthode 2 : Utiliser CMD au lieu de PowerShell

Si vous préférez ne pas changer la politique :

1. Ouvrez **Invite de commandes (CMD)** au lieu de PowerShell
2. Exécutez vos commandes npm normalement :
   ```bash
   npm install
   npm run dev
   npm run build
   ```

### Méthode 3 : Utiliser Git Bash

Si vous avez Git installé :

1. Ouvrez **Git Bash**
2. Naviguez vers votre projet
3. Exécutez npm normalement

---

## 📋 Vérifier la Politique Actuelle

Pour voir votre politique actuelle :

```powershell
Get-ExecutionPolicy -List
```

Résultat attendu après correction :
```
        Scope ExecutionPolicy
        ----- ---------------
MachinePolicy       Undefined
   UserPolicy       Undefined
      Process       Undefined
  CurrentUser    RemoteSigned
 LocalMachine       Undefined
```

---

## 🔄 Revenir à la Politique par Défaut

Si vous voulez remettre la politique par défaut :

```powershell
Set-ExecutionPolicy -ExecutionPolicy Restricted -Scope CurrentUser
```

**Note :** Vous devrez alors utiliser CMD ou Git Bash pour npm.

---

## 🎯 Commandes npm Courantes

Maintenant que npm fonctionne :

### Installation des dépendances
```bash
npm install
```

### Développement (avec rechargement automatique)
```bash
npm run dev
```

### Build pour production
```bash
npm run build
```

### Mettre à jour npm
```bash
npm install -g npm@latest
```

---

## 🆘 Si Ça Ne Marche Toujours Pas

### 1. Redémarrer PowerShell

Fermez complètement PowerShell et rouvrez-le.

### 2. Vérifier que Node.js est installé

```powershell
node --version
npm --version
```

Si erreur, téléchargez Node.js : https://nodejs.org/

### 3. Utiliser PowerShell en Administrateur

Clic droit sur PowerShell → **Exécuter en tant qu'administrateur**

Puis :
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope LocalMachine
```

### 4. Vérifier la Variable d'Environnement PATH

Dans PowerShell :
```powershell
$env:Path -split ';' | Select-String node
```

Devrait afficher le chemin vers Node.js (ex: `C:\Program Files\nodejs\`)

---

## ✅ Checklist

- [x] Politique d'exécution changée à RemoteSigned
- [x] `npm install` fonctionne
- [x] Dépendances installées (83 packages)
- [x] Aucune vulnérabilité trouvée
- [ ] Optionnel : `npm run build` pour production

---

## 📚 Ressources

- **Documentation PowerShell Execution Policy** : 
  https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_execution_policies

- **Documentation npm** : 
  https://docs.npmjs.com/

- **Node.js** : 
  https://nodejs.org/

---

✅ **Problème résolu ! Vous pouvez maintenant utiliser npm normalement dans PowerShell !**

*Dernière mise à jour : 21 Janvier 2026*
