# Système de Gestion des Commandes, Livraisons et Paiements

## 📋 Vue d'ensemble

Ce document décrit le système complet de gestion des commandes, livraisons et paiements pour Domini.

## 🗄️ Structure de la base de données

### 1. Table `commandes`
Stocke toutes les commandes des employés.

**Champs:**
- `id` - Identifiant unique
- `ref` - Référence unique de la commande (ex: CMD-20260120-ABC123)
- `id_employe` - ID de l'employé qui commande (nullable)
- `montant_total` - Montant total de la commande (nullable)
- `statut_commande` - Statut de la commande (en_attente, confirmee, annulee, terminee)
- `statut_preparation` - Statut de préparation (en_attente, en_cours, prete)
- `statut_livraison` - Statut de livraison (en_attente, en_cours, livree, echec)
- `lieu` - Adresse de livraison (nullable)
- `lat` / `long` - Coordonnées GPS (nullable)
- `consigne_cuisinier` - Instructions pour le cuisinier (nullable)
- `consigne_livreur` - Instructions pour le livreur (nullable)
- `statut_paiement` - Statut du paiement (en_attente, paye, rembourse)
- `mode_paiement` - Mode de paiement (especes, carte, mobile_money, wallet)
- `numero_telephone` - Numéro de téléphone du client (nullable)

### 2. Table `item_commandes`
Stocke les articles d'une commande (relation one-to-many avec commandes).

**Champs:**
- `id` - Identifiant unique
- `commande_id` - ID de la commande
- `plat_id` - ID du plat commandé (nullable)
- `accompagnements` - Liste des accompagnements en JSON (nullable)
- `options` - Liste des options en JSON (nullable)
- `prix` - Prix unitaire
- `is_subventionne` - Indique si le plat est subventionné par l'entreprise
- `quantite` - Quantité commandée (défaut: 1)

### 3. Table `livraisons`
Gère les livraisons associées aux commandes.

**Champs:**
- `id` - Identifiant unique
- `commande_id` - ID de la commande
- `livreur_id` - ID du livreur assigné (nullable)
- `client_id` - ID du client (nullable)
- `montant_livraison` - Frais de livraison (défaut: 0)
- `statut` - Statut (en_attente, assignee, en_cours, livree, echec)
- `heure_assignation` - Heure d'assignation au livreur (nullable)
- `heure_prise_en_charge` - Heure de prise en charge (nullable)
- `heure_livraison` - Heure de livraison effective (nullable)
- `commentaire` - Commentaires du livreur (nullable)

### 4. Table `paiements`
Trace tous les paiements (commandes, abonnements, remboursements, etc.).

**Champs:**
- `id` - Identifiant unique
- `type` - Type de paiement (abonnement, commande, remboursement, paiement_livraison)
- `montant` - Montant du paiement
- `ref` - Référence unique (ex: PAY-20260120-ABC123)
- `mode_paiement` - Mode (especes, carte, mobile_money, wallet, virement)
- `statut` - Statut (en_attente, valide, echoue, rembourse)
- `commande_id` - ID de la commande associée (nullable)
- `user_id` - ID de l'utilisateur concerné (nullable)
- `description` - Description du paiement (nullable)
- `metadata` - Données supplémentaires en JSON (nullable)

## 🎯 Fonctionnalités

### Gestion des Commandes (`/admin/commandes`)

**Liste des commandes:**
- Affichage de toutes les commandes avec filtres
- Statistiques en temps réel (total, en attente, confirmées, annulées)
- Filtres par statut (commande, préparation, livraison)
- Actions: Voir détails, Valider, Annuler, Affecter un livreur

**Détails d'une commande:**
- Visualisation complète de la commande
- Modification des statuts (commande, préparation, livraison)
- Liste des articles commandés avec accompagnements et options
- Informations client et entreprise
- Affectation d'un livreur
- Consignes pour le cuisinier et le livreur
- Informations de livraison (lieu, carte)
- État du paiement

**Actions disponibles:**
- Valider une commande
- Annuler une commande
- Changer le statut de la commande
- Changer le statut de préparation
- Changer le statut de livraison
- Affecter un livreur

### Gestion des Livraisons (`/admin/livraisons`)

**Liste des livraisons:**
- Affichage de toutes les livraisons
- Statistiques (total, en cours, livrées, échecs)
- Filtres par statut et livreur
- Informations: commande, client, livreur, montant, horaires
- Action: Changer le statut

**Détails d'une livraison:**
- Visualisation complète de la livraison
- Chronologie avec horodatage (assignation, prise en charge, livraison)
- Détails de la commande associée
- Informations du livreur et du client
- Lieu de livraison avec lien carte Google Maps
- Montants (commande + livraison)
- Modification du statut

**Suivi chronologique:**
1. ⏰ Livraison assignée
2. 📦 Prise en charge par le livreur
3. ✅ Livraison effectuée

### Gestion des Paiements (`/admin/paiements`)

**Liste des paiements:**
- Affichage de tous les paiements
- Statistiques financières (total validé, en attente, remboursé)
- Filtres par type, statut, mode de paiement
- Traçabilité complète: référence, type, utilisateur, commande
- Code couleur pour les montants (vert = crédit, rouge = débit)

**Types de paiements:**
- 🔵 Abonnement
- 🔷 Commande
- 🟡 Remboursement
- 🟢 Paiement livraison

**Modes de paiement:**
- Espèces
- Carte bancaire
- Mobile Money
- Wallet
- Virement

## 🔗 Relations entre les modèles

```
Commande
  └── a plusieurs ItemCommande
  └── a une Livraison
  └── a plusieurs Paiements
  └── appartient à un User (employé)

ItemCommande
  └── appartient à une Commande
  └── appartient à un Plat

Livraison
  └── appartient à une Commande
  └── appartient à un User (livreur)
  └── appartient à un User (client)

Paiement
  └── peut appartenir à une Commande
  └── peut appartenir à un User
```

## 🚀 Utilisation

### 1. Créer une commande
Les commandes sont créées par les employés via l'application mobile/web. Chaque commande génère automatiquement une référence unique.

### 2. Valider et préparer
L'admin peut:
1. Valider la commande
2. Mettre le statut de préparation à "en cours"
3. Marquer la préparation comme "prête"

### 3. Affecter un livreur
Depuis la liste ou les détails d'une commande:
1. Cliquer sur "Affecter un livreur"
2. Sélectionner un livreur disponible
3. La livraison est créée et le livreur notifié

### 4. Suivre la livraison
Le système enregistre automatiquement:
- L'heure d'assignation
- L'heure de prise en charge (quand le livreur accepte)
- L'heure de livraison effective

### 5. Gérer les paiements
Tous les paiements sont tracés automatiquement avec:
- Une référence unique
- Le type et le mode de paiement
- Le statut
- Les métadonnées (si nécessaire)

## 📊 Statistiques disponibles

**Commandes:**
- Total des commandes
- Commandes en attente
- Commandes confirmées
- Commandes annulées

**Livraisons:**
- Total des livraisons
- Livraisons en cours
- Livraisons livrées
- Échecs de livraison

**Paiements:**
- Montant total validé
- Montant en attente
- Montant remboursé
- Nombre total de transactions

## 🎨 Design et UX

Le système utilise la palette de couleurs Domini:
- **Orange principal**: #D9542A (actions, liens importants)
- **Jaune**: #F7B801 (alertes, en attente)
- **Gris foncé**: #3A3A3A (texte, navigation)
- **Beige**: #FDFBF8 (arrière-plan)

Badges de statut colorés pour une reconnaissance visuelle rapide:
- 🟡 En attente (warning)
- 🔵 En cours (info)
- 🟢 Validé/Livré (success)
- 🔴 Annulé/Échec (danger)

## 📱 Responsive

Toutes les vues sont entièrement responsives et s'adaptent aux:
- Desktop (> 1024px)
- Tablette (768px - 1024px)
- Mobile (< 768px)

## 🔐 Sécurité

- Toutes les routes sont protégées par le middleware `auth`
- Validation des données côté serveur
- Protection CSRF sur tous les formulaires
- Relations avec contraintes d'intégrité référentielle

## 🚦 Prochaines étapes suggérées

1. **Notifications en temps réel** (via WebSocket ou Pusher)
2. **API mobile** pour les livreurs et employés
3. **Impression de tickets** de commande
4. **Export des rapports** (PDF, Excel)
5. **Tableau de bord avec graphiques** (Chart.js)
6. **Géolocalisation en temps réel** des livreurs
7. **Notes et évaluations** des livraisons
8. **Gestion des stocks** en temps réel

---

✨ **Le système est maintenant opérationnel !**

Pour accéder aux modules:
- Commandes: `/admin/commandes`
- Livraisons: `/admin/livraisons`
- Paiements: `/admin/paiements`
