# Guide des Seeders - Domini

## 📋 Vue d'ensemble

Les seeders permettent de peupler la base de données avec des données de test réalistes pour le système Domini. Ils créent des données cohérentes pour tous les modules : entreprises, employés, livreurs, catégories, plats, commandes, livraisons et paiements.

## 🌱 Seeders disponibles

### 1. **AdminUserSeeder** (déjà existant)
Crée l'utilisateur administrateur principal.
- **Email** : admin@domini.ci
- **Mot de passe** : admin123

### 2. **EntrepriseSeeder**
Crée 7 entreprises partenaires d'Abidjan :
- Orange Côte d'Ivoire
- MTN Côte d'Ivoire
- Société Générale CI
- Nestlé Côte d'Ivoire
- Bloomfield Investment
- NSIA Banque
- CFAO Technologies (inactive)

### 3. **EmployeSeeder**
Crée 15 employés répartis dans les différentes entreprises :
- 3 employés Orange CI
- 3 employés MTN CI
- 2 employés Société Générale CI
- 3 employés Nestlé CI
- 2 employés Bloomfield
- 2 employés NSIA

**Tous les mots de passe** : `password123`

### 4. **LivreurSeeder**
Crée 8 livreurs actifs :
- Souleymane Bakayoko
- Seydou Doumbia
- Mamadou Diabaté
- Ali Coulibaly
- Adama Konaté
- Lassina Fofana
- Ismaël Touré
- Daouda Sylla

**Tous les mots de passe** : `password123`

### 5. **CategorieSeeder**
Crée 7 catégories de plats :
- Plats Africains
- Plats Européens
- Plats Asiatiques
- Grillades
- Salades & Léger
- Pâtes & Pizzas
- Fast Food (indisponible)

### 6. **PlatSeeder**
Crée 11 plats variés avec leurs accompagnements et options :

**Plats Africains :**
- Attiéké Poisson Braisé (2500 FCFA)
- Riz Sauce Graine (2000 FCFA)
- Foutou Sauce Arachide (2200 FCFA)

**Plats Européens :**
- Steak Frites (3000 FCFA)
- Poulet Rôti & Purée (2800 FCFA)

**Plats Asiatiques :**
- Nouilles Sautées Poulet (2300 FCFA)
- Riz Cantonais (2500 FCFA)

**Grillades :**
- Brochettes de Bœuf (2700 FCFA)

**Salades :**
- Salade César Poulet (2200 FCFA)

**Pâtes & Pizzas :**
- Spaghetti Bolognaise (2400 FCFA)
- Pizza Margherita (2600 FCFA)

### 7. **CommandeSeeder**
Crée 8 commandes avec différents statuts :
- 1 commande en attente
- 1 commande confirmée (en préparation)
- 1 commande prête à livrer
- 2 commandes en cours de livraison
- 2 commandes livrées
- 1 commande annulée

### 8. **LivraisonSeeder**
Crée 6 livraisons avec différents statuts :
- 1 livraison assignée
- 2 livraisons en cours
- 2 livraisons terminées
- 1 livraison en échec

### 9. **PaiementSeeder**
Crée 14 paiements de différents types :
- 8 paiements de commandes (validés)
- 2 paiements de frais de livraison
- 1 paiement en attente
- 1 remboursement
- 3 paiements d'abonnement

## 🚀 Utilisation

### Option 1 : Peupler toute la base de données

Pour exécuter tous les seeders d'un coup :

```bash
cd "C:\Users\JEAN SERI\Desktop\domini\domini"
php artisan db:seed
```

### Option 2 : Réinitialiser et peupler

Pour réinitialiser complètement la base de données et la peupler :

```bash
php artisan migrate:fresh --seed
```

⚠️ **ATTENTION** : Cette commande supprime **toutes les données** existantes !

### Option 3 : Exécuter un seeder spécifique

Pour exécuter un seul seeder :

```bash
# Exemple : seulement les employés
php artisan db:seed --class=EmployeSeeder

# Exemple : seulement les plats
php artisan db:seed --class=PlatSeeder
```

## 📊 Données générées

Après exécution complète des seeders, vous aurez :

| Type de données | Nombre | Détails |
|----------------|--------|---------|
| **Admin** | 1 | Compte administrateur principal |
| **Entreprises** | 7 | 6 actives, 1 inactive |
| **Employés** | 15 | Répartis dans les entreprises |
| **Livreurs** | 8 | Tous actifs |
| **Catégories** | 7 | 6 disponibles, 1 indisponible |
| **Plats** | 11 | Avec accompagnements et options |
| **Commandes** | 8 | Différents statuts |
| **Livraisons** | 6 | Différents statuts |
| **Paiements** | 14 | Commandes, livraisons, abonnements |

## 🔑 Comptes de test

### Compte Admin
- **Email** : admin@domini.ci
- **Mot de passe** : admin123
- **Accès** : `/admin/login`

### Comptes Employés (exemples)
Tous avec le mot de passe : `password123`

- k.yao@orange.ci (Orange CI)
- a.diallo@orange.ci (Orange CI)
- f.traore@mtn.ci (MTN CI)
- e.kouadio@sgci.ci (Société Générale)
- ak.cisse@nestle.ci (Nestlé)

### Comptes Livreurs (exemples)
Tous avec le mot de passe : `password123`

- s.bakayoko@domini.ci
- s.doumbia@domini.ci
- m.diabate@domini.ci

## 🎯 Scénarios de test

Les seeders créent des scénarios réalistes pour tester le système :

### Scénario 1 : Commande en attente
- **Commande** : CMD-... (Kouassi Yao)
- **Statut** : En attente de validation
- **Paiement** : En attente

### Scénario 2 : Commande en préparation
- **Commande** : CMD-... (Aminata Diallo)
- **Statut** : Confirmée, en cours de préparation
- **Paiement** : Payé

### Scénario 3 : Livraison en cours
- **Commande** : CMD-... (Eric Kouadio)
- **Statut** : Prête, livreur en route
- **Livreur** : Souleymane Bakayoko
- **Paiement** : Payé

### Scénario 4 : Commande livrée
- **Commande** : CMD-... (Abdoul Karim Cissé)
- **Statut** : Terminée, livrée
- **Livreur** : Seydou Doumbia
- **Paiement** : Payé + frais de livraison

### Scénario 5 : Commande annulée
- **Commande** : CMD-... (Désirée Konan)
- **Statut** : Annulée
- **Paiement** : Remboursé

## 🔄 Ordre d'exécution

Les seeders s'exécutent dans cet ordre (respectant les dépendances) :

1. **AdminUserSeeder** → Crée l'admin
2. **EntrepriseSeeder** → Crée les entreprises
3. **EmployeSeeder** → Crée les employés (nécessite les entreprises)
4. **LivreurSeeder** → Crée les livreurs
5. **CategorieSeeder** → Crée les catégories
6. **PlatSeeder** → Crée les plats (nécessite les catégories)
7. **CommandeSeeder** → Crée les commandes (nécessite employés et plats)
8. **LivraisonSeeder** → Crée les livraisons (nécessite commandes et livreurs)
9. **PaiementSeeder** → Crée les paiements (nécessite les commandes)

## 🛠️ Personnalisation

Pour ajouter vos propres données, modifiez les fichiers seeders dans :
```
database/seeders/
```

Exemple pour ajouter une entreprise :

```php
// Dans EntrepriseSeeder.php
[
    'nom' => 'Votre Entreprise',
    'adresse' => 'Votre adresse',
    'lat' => 5.xxxx,
    'long' => -4.xxxx,
    'numero' => '+225 XX XX XX XX XX',
    'pays' => 'Côte d\'Ivoire',
    'ville' => 'Abidjan',
    'statut' => true,
]
```

## ⚠️ Notes importantes

1. **Exécution multiple** : Vous pouvez réexécuter les seeders, mais cela créera des doublons. Utilisez `migrate:fresh --seed` pour repartir à zéro.

2. **Données de production** : Ces seeders sont pour le développement uniquement. **Ne les exécutez JAMAIS en production !**

3. **Références uniques** : Les références de commandes et paiements sont générées dynamiquement et seront différentes à chaque exécution.

4. **Mots de passe** : Tous les comptes utilisent des mots de passe simples (`password123`, `admin123`) pour faciliter les tests.

## 🎉 Félicitations !

Votre base de données est maintenant peuplée avec des données réalistes. Vous pouvez :
- ✅ Tester toutes les fonctionnalités de l'admin
- ✅ Voir des commandes à différents stades
- ✅ Suivre des livraisons en cours
- ✅ Consulter l'historique des paiements
- ✅ Gérer les plats et catégories

**Bon développement ! 🚀**
