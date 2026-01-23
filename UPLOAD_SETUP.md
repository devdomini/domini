# Configuration des Uploads d'Images

## ✅ Configuration actuelle

### Limites d'upload :
- **Taille maximale par fichier** : 5 Mo (5120 KB)
- **Formats acceptés** : JPG, JPEG, PNG, GIF, WebP
- **Validation côté client** : Oui (JavaScript)
- **Validation côté serveur** : Oui (Laravel)

### Messages d'erreur personnalisés :
- Messages en français
- Affichage clair des erreurs de validation
- Prévisualisation de la taille du fichier avant upload

## 🔧 Vérifications nécessaires

### 1. Lien symbolique storage (✅ Déjà fait)
```bash
php artisan storage:link
```

### 2. Vérifier les permissions des dossiers
```bash
# Windows PowerShell
icacls "storage\app\public" /grant Users:F /T
icacls "public\storage" /grant Users:F /T
```

### 3. Créer le dossier categories (si nécessaire)
```bash
# Windows PowerShell
New-Item -ItemType Directory -Path "storage\app\public\categories" -Force
```

## 📁 Structure des fichiers

```
domini/
├── storage/
│   └── app/
│       └── public/
│           └── categories/          ← Images des catégories uploadées ici
│               ├── image1.jpg
│               └── image2.png
└── public/
    └── storage/                     ← Lien symbolique vers storage/app/public
        └── categories/
            ├── image1.jpg
            └── image2.png
```

## 🚀 Utilisation

### Ajouter une catégorie avec logo :
1. Aller sur `/admin/menu/categories`
2. Cliquer sur la card "Ajouter une catégorie"
3. Remplir le nom
4. Choisir une image (max 5 Mo)
5. Le JavaScript vérifiera la taille avant l'upload
6. Cliquer sur "Créer la catégorie"

### Messages d'erreur possibles :

| Erreur | Cause | Solution |
|--------|-------|----------|
| "La taille du logo ne peut pas dépasser 5 Mo" | Image trop lourde | Compresser l'image ou choisir une autre |
| "Le logo doit être au format: jpeg, png..." | Mauvais format | Convertir en JPG, PNG, GIF ou WebP |
| "Erreur lors de l'upload du logo" | Problème de permissions | Vérifier les permissions des dossiers |

## 🔍 Débogage

### Vérifier la configuration PHP :
```bash
php -i | Select-String -Pattern "upload_max_filesize|post_max_size"
```

### Vérifier si le lien symbolique existe :
```bash
# Windows PowerShell
Test-Path "public\storage"
```

### Afficher les dernières erreurs Laravel :
```bash
Get-Content "storage\logs\laravel.log" -Tail 50
```

## 💡 Conseils

1. **Optimiser les images avant upload** :
   - Utiliser des outils comme TinyPNG, Squoosh
   - Redimensionner à max 800x800 pixels
   - Format WebP recommandé pour une meilleure compression

2. **Si l'upload ne fonctionne toujours pas** :
   - Vérifier que le dossier `storage/app/public/categories` existe
   - Vérifier les permissions en écriture
   - Consulter les logs Laravel (`storage/logs/laravel.log`)
   - Vérifier la configuration PHP (`upload_max_filesize`, `post_max_size`)

3. **Tester avec une petite image** :
   - Commencer avec une image < 500 Ko
   - Format JPG ou PNG standard
   - Vérifier que ça fonctionne avant d'essayer avec des images plus grandes
