# Configuration de l'Administration Domini

## Installation et Configuration

### 1. Exécuter les migrations

```bash
php artisan migrate:fresh
```

### 2. Créer l'utilisateur admin

```bash
php artisan db:seed --class=AdminUserSeeder
```

### 3. Identifiants par défaut

- **Email:** admin@domini.com
- **Mot de passe:** password123

⚠️ **IMPORTANT:** Changez ces identifiants en production !

## URLs d'accès

- **Page de connexion:** http://localhost:8000/admin/login
- **Dashboard:** http://localhost:8000/admin/dashboard (après connexion)

## Structure des fichiers créés

### Vues
- `resources/views/admin/layout.blade.php` - Layout principal de l'admin
- `resources/views/admin/login.blade.php` - Page de connexion
- `resources/views/admin/dashboard.blade.php` - Tableau de bord

### Controller
- `app/Http/Controllers/AdminController.php` - Gestion de l'authentification et du dashboard

### Seeder
- `database/seeders/AdminUserSeeder.php` - Création de l'utilisateur admin

### Migration
- Modifié `database/migrations/0001_01_01_000000_create_users_table.php`
  - Ajout du champ `role` (admin, entreprise, livreur, employe)
  - Ajout du champ `telephone`
  - Ajout du champ `id_entreprise` (nullable)
  - Ajout du champ `num_box` (nullable)

## Routes disponibles

- `GET /admin/login` - Afficher le formulaire de connexion
- `POST /admin/login` - Traiter la connexion
- `GET /admin/dashboard` - Afficher le dashboard (protégé)
- `POST /admin/logout` - Déconnexion

## Rôles disponibles

1. **admin** - Administrateur système (accès complet)
2. **entreprise** - Compte entreprise
3. **livreur** - Compte livreur
4. **employe** - Compte employé

## Fonctionnalités du Dashboard

### Statistiques en temps réel
- Total employés
- Commandes du jour
- Entreprises actives
- Chiffre d'affaires

### Tableaux de données
- Commandes récentes
- Livraisons en cours

### Menu de navigation
- Dashboard
- Utilisateurs
- Entreprises
- Commandes
- Menu
- Statistiques
- Paramètres

## Sécurité

✅ Authentification Laravel
✅ Protection CSRF
✅ Middleware auth pour les routes protégées
✅ Vérification du rôle admin
✅ Sessions sécurisées
✅ Option "Se souvenir de moi"

## Prochaines étapes

1. Créer les pages de gestion des utilisateurs
2. Créer les pages de gestion des entreprises
3. Créer les pages de gestion des commandes
4. Créer les pages de gestion du menu
5. Ajouter les statistiques avancées
6. Implémenter l'upload de fichiers
7. Ajouter les notifications en temps réel

## Notes importantes

- Le dashboard est accessible uniquement aux utilisateurs avec le rôle "admin"
- Les autres utilisateurs sont automatiquement déconnectés s'ils tentent d'accéder
- Les sessions expirent après inactivité
- Tous les formulaires sont protégés contre les attaques CSRF
