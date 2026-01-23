# 🧪 Guide de Test - Boxes & Abonnements Domini

## 🚀 Démarrage Rapide

### Étape 1 : Vérifier que le serveur tourne
```bash
cd "C:\Users\JEAN SERI\Desktop\domini\domini"
php artisan serve
```

### Étape 2 : Se connecter à l'admin
```
URL : http://localhost:8000/admin/login
Email : admin@domini.com
Password : password123
```

---

## 📦 Test des Boxes & Casiers

### 1. Accéder à la liste des boxes
- Cliquer sur **"Boxes & Casiers"** dans le menu latéral
- Vous devriez voir une liste vide (ou les boxes existantes)

### 2. Créer une nouvelle box

**Navigation** : `Boxes & Casiers` > `+ Nouvelle Box`

**Remplir le formulaire** :
- Nom : `Box Test Plateau`
- Entreprise : Sélectionner une entreprise existante
- Adresse : `Boulevard Lagunaire, Plateau`
- Latitude : `5.3364` (optionnel)
- Longitude : `-4.0267` (optionnel)
- Nombre de casiers : `20`

**Vérifications** :
- ✅ Les icônes SVG s'affichent correctement (pas d'emojis)
- ✅ Le compteur de casiers se met à jour dynamiquement
- ✅ L'info box affiche le bon nombre de casiers

**Soumettre** : Cliquer sur `Créer la Box et Générer les Casiers`

### 3. Vérifier la création
- **Redirection** vers la liste des boxes
- **Message de succès** : "Box créée avec succès ! 20 casiers générés."
- **Card de la box** affichée avec :
  - Icône de box en SVG
  - Badge "Actif"
  - Statistiques : 20 total, 20 libres, 0 occupés, 0% occupation

### 4. Voir les détails d'une box

**Cliquer sur** : `Voir Détails`

**Vérifications sur la page de détails** :
- ✅ 4 cartes KPI avec gradients
- ✅ Informations de la box bien formatées
- ✅ Lien Google Maps fonctionnel (si GPS renseigné)
- ✅ Barre de recherche active
- ✅ Filtre par statut fonctionnel
- ✅ Tableau des 20 casiers générés

### 5. Tester les QR Codes

**Dans le tableau des casiers** :
- Cliquer sur l'icône QR code d'un casier
- **Vérifications** :
  - ✅ Modal s'ouvre avec fond sombre
  - ✅ QR code réel affiché (pas d'emoji ❌)
  - ✅ Référence du casier affichée (ex: BOX-20260121-XXXXXX-C001)
  - ✅ Valeur du QR code affichée (ex: QR-65A7F3...)
  - ✅ QR code de 256x256px, noir sur blanc
  - ✅ Bouton "Fermer" fonctionne
  - ✅ Clic à l'extérieur ferme le modal
  - ✅ Touche Escape ferme le modal

### 6. Tester la recherche et les filtres

**Recherche** :
- Taper `C005` → Seul le casier 5 s'affiche
- Taper une référence complète → Casier correspondant affiché
- Effacer → Tous les casiers réapparaissent

**Filtre par statut** :
- Sélectionner "Libre" → Tous les casiers s'affichent (car tous libres)
- Sélectionner "Occupé" → Aucun casier (liste vide normale)

### 7. Ajouter des casiers supplémentaires

**Dans la liste des boxes** :
- Cliquer sur `+ Ajouter Casiers`
- **Modal s'ouvre**
- Entrer : `10`
- Cliquer sur `Ajouter`

**Vérifications** :
- Message de succès : "10 casiers ajoutés avec succès !"
- Capacité passée de 20 à 30
- Statistiques mises à jour automatiquement

---

## 📋 Test des Abonnements

### 1. Accéder à la liste des abonnements
- Cliquer sur **"Abonnements"** dans le menu latéral
- Statistiques affichées en cartes KPI

### 2. Créer un nouvel abonnement

**Navigation** : `Abonnements` > `+ Nouvel Abonnement`

**Remplir le formulaire** :
- Entreprise : Sélectionner une entreprise
- Représentant : `Jean Kouassi`
- Fonction : `Directeur RH`
- Téléphone : `0707123456`
- Type de subvention : `Partielle`
- Pourcentage : `75` (s'affiche seulement si "Partielle")
- Nombre d'employés : `50`
- Durée : `12 mois (1 an)`

**Vérifications** :
- ✅ Aucun emoji visible
- ✅ Le champ pourcentage apparaît/disparaît selon le type
- ✅ Formulaire bien validé

**Soumettre** : Cliquer sur `Créer l'Abonnement`

### 3. Vérifier la création
- **Redirection** vers la liste
- **Message de succès** : "Abonnement créé avec succès !"
- **Nouvelle ligne** dans le tableau avec :
  - Badge "Actif" en vert
  - Subvention : 75% en jaune
  - Période : dates + "365 jours restants"
  - Actions disponibles

### 4. Tester les actions

#### A. Suspendre un abonnement
- Cliquer sur l'icône **Pause** (SVG, pas emoji)
- Modal s'ouvre : "Suspendre l'abonnement"
- Confirmer
- Badge passe à "Suspendu" (orange)
- Action "Réactiver" apparaît

#### B. Réactiver un abonnement suspendu
- Cliquer sur l'icône **Check** (SVG)
- Confirmer
- Badge repasse à "Actif" (vert)

#### C. Résilier un abonnement
- Cliquer sur l'icône **X** (SVG)
- Modal s'ouvre avec champ "Raison"
- Entrer : `Fin de contrat à l'amiable`
- Confirmer
- Badge passe à "Résilié" (rouge)
- Action "Renouveler" apparaît

#### D. Renouveler un abonnement
- Cliquer sur l'icône **Refresh** (SVG)
- Modal s'ouvre
- Sélectionner durée : `6 mois`
- Confirmer
- Message : "Abonnement renouvelé pour 6 mois !"
- Badge repasse à "Actif"
- Dates mises à jour

### 5. Tester les filtres

**Filtre par statut** :
- Sélectionner "Actif" → Seuls les actifs
- Sélectionner "Suspendu" → Seuls les suspendus
- Sélectionner "Résilié" → Seuls les résiliés

**Filtre par entreprise** :
- Sélectionner une entreprise → Seuls ses abonnements
- Revenir à "Toutes" → Tous les abonnements

---

## ✅ Checklist Globale

### Design & UI
- [ ] Aucun emoji visible (tous remplacés par SVG)
- [ ] Couleurs cohérentes avec le thème admin
- [ ] Gradients sur les cartes KPI
- [ ] Badges colorés selon le statut
- [ ] Icônes SVG bien rendues
- [ ] Responsive sur mobile/tablet/desktop

### Boxes & Casiers
- [ ] Création de box fonctionne
- [ ] Génération automatique des casiers
- [ ] QR codes réels affichés (pas de placeholder)
- [ ] Modal QR code professionnel
- [ ] Recherche dynamique fonctionne
- [ ] Filtres par statut fonctionnent
- [ ] Ajout de casiers supplémentaires OK
- [ ] Statistiques en temps réel
- [ ] Actions (voir, modifier, supprimer) OK

### Abonnements
- [ ] Création d'abonnement OK
- [ ] Champ pourcentage conditionnel
- [ ] Dates calculées automatiquement
- [ ] Suspension/Réactivation OK
- [ ] Résiliation avec raison OK
- [ ] Renouvellement avec durée OK
- [ ] Filtres fonctionnels
- [ ] Statistiques dashboard OK
- [ ] Jours restants calculés
- [ ] Actions contextuelles selon statut

### Fonctionnalités Techniques
- [ ] Validation des formulaires
- [ ] Messages flash (succès/erreur)
- [ ] Pagination à 10 éléments
- [ ] Relations Eloquent chargées
- [ ] Aucune erreur de linter
- [ ] Aucune erreur JavaScript console
- [ ] Modals ferment bien (X, extérieur, Escape)

---

## 🐛 En cas de Problème

### QR codes ne s'affichent pas
1. Vérifier la console JavaScript (F12)
2. Vérifier que la bibliothèque QRCode.js est chargée :
   ```
   https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js
   ```
3. Vérifier qu'il n'y a pas d'erreur CORS

### Statistiques à 0
1. Vérifier que les relations sont bien chargées
2. Vérifier les méthodes dans les modèles :
   - `Box::casiersLibres()`
   - `Box::casiersOccupes()`
   - `Abonnement::joursRestants()`

### Icônes ne s'affichent pas
1. Vérifier le code SVG dans le HTML
2. Vérifier qu'il n'y a pas de conflit CSS
3. Vérifier les classes `sidebar-icon`, `btn-icon`

### Actions ne fonctionnent pas
1. Vérifier les routes dans `web.php`
2. Vérifier les méthodes dans les contrôleurs
3. Vérifier le token CSRF (@csrf)

---

## 📸 Captures d'Écran Attendues

### Boxes - Liste
```
+------------------------------------------+
| Toutes les boxes                         |
| [+ Nouvelle Box]                         |
+------------------------------------------+
| [ICON]  Box Test Plateau    [Badge Actif]|
|         BOX-20260121-XXXXXX              |
|         Entreprise: XXX                  |
|         📍 Boulevard Lagunaire...        |
|                                          |
|  Total: 30  |  Libres: 30  |  Taux: 0%  |
|                                          |
|  [Voir Détails] [Modifier] [+ Casiers]  |
+------------------------------------------+
```

### Boxes - QR Code Modal
```
+---------------------------+
|   QR Code du Casier       |
|---------------------------|
|   BOX-20260121-XXX-C001   |
|                           |
|   [QR CODE IMAGE 256px]   |
|                           |
|   QR-65A7F3B2C9...        |
|                           |
|      [Fermer]             |
+---------------------------+
```

### Abonnements - Tableau
```
+---------------------------------------------------------------+
| Entreprise | Représentant | Contact | Employés | Subvention  |
|------------|--------------|---------|----------|-------------|
| XXX SA     | Jean K.      | 0707... | 50       | 75% [Jaune] |
| [Ville]    | [Fonction]   |         |          | [Badge Actif]|
|            |              |         |          | 365j restants|
| [Icons: 👁 ⏸ ❌ ✏️]                                            |
+---------------------------------------------------------------+
```

---

✅ **Tous les tests passent ? Parfait ! Le système est prêt pour la production !**

🎉 **Félicitations, vous avez un système complet et professionnel de gestion de boxes et d'abonnements !**
