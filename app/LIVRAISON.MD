# Livraison entreprise (is_lunch) — Règles métier validées

Le **rayon fixe (ex. 10 km)** ne convient **pas** au métier : il ne faut **aucun** regroupement automatique par distance entre entreprises. À **chaque ajout d’entreprise**, celle-ci est **intégrée manuellement** dans le ou les **trajets** concernés (position dans l’ordre, commune, entrepôt). La vérité opérationnelle est donc : **trajets + communes + entrepôt + livreurs liés à l’entrepôt**, le tout piloté **à la main** dans l’admin.

---

## 1) Principes fondamentaux


| Élément                                 | Règle                                                                                                                                                                                                                                                                |
| --------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Trajet**                              | Défini **manuellement** dans l’admin : ordre des entreprises à visiter, basé sur les **communes** (et la proximité **par rapport à l’entrepôt**).                                                                                                                    |
| **Ordre des entreprises sur le trajet** | De l’entreprise **la plus proche de l’entrepôt** jusqu’à la **dernière** du trajet (séquence imposée, pas un regroupement auto par distance entre entreprises).                                                                                                      |
| **Entrepôt**                            | Chaque entrepôt est **lié à une ou plusieurs communes** (zone de couverture / rattachement géographique).                                                                                                                                                            |
| **Livreur**                             | Chaque livreur est **lié à un entrepôt** (il ne travaille que dans le périmètre logique de cet entrepôt pour les trajets entreprise).                                                                                                                                |
| **Partage des colis**                   | **Séquentiel** : on remplit **livreur 1** avec les colis des entreprises **dans l’ordre du trajet**, jusqu’à **200 colis max**, puis **livreur 2** reprend au point suivant, et ainsi de suite jusqu’au **dernier livreur** et la **dernière entreprise** du trajet. |


---

## 2) Ce qu’on **n’utilise pas** (et pourquoi le “10 km” ne marche pas ici)

- **Pas** de zone automatique “toutes les entreprises à moins de X km” : à chaque nouvelle entreprise, **on ne calcule pas** un rayon pour l’inclure ; **on l’ajoute** dans les trajets **manuellement** (ordre, trajet, entrepôt).
- **Pas** de pool “zone” sans trajet défini.
- Le **10 km** (ou tout autre seuil fixe) **ne remplace pas** la décision humaine : les trajets évoluent quand le réseau d’entreprises s’agrandit, **point par point**, dans l’admin.

---

## 3) Définition des **trajets** (admin)

Dans l’interface : menu **Trajets** (ou onglet **Trajet de livraison** sur la fiche entrepôt) — pour chaque entrepôt, on définit l’**ordre des entreprises** éligibles (entreprises actives dont la **commune** est liée à cet entrepôt). Un bouton **« Générer auto (plus proche → plus loin) »** propose un ordre par **distance à vol d’oiseau** depuis l’entrepôt (GPS entrepôt + GPS entreprise ou commune) : c’est une **aide**, l’admin peut toujours réordonner à la main ensuite.

1. Choisir un **entrepôt** (point de départ logique des tournées).
2. Pour **chaque** entreprise cliente à livrer (jour J), connaître sa **commune** (et la rattacher au bon entrepôt si besoin).
3. **Construire manuellement** la liste ordonnée des entreprises sur le trajet :
  - **1re** entreprise = celle **la plus proche de l’entrepôt** (selon les critères retenus : commune, ordre métier, carte).
  - puis la suivante, … jusqu’à la **dernière** entreprise du trajet.
4. Ce trajet est **figé** pour la planification du jour (ou versionnée par période).

> Les **communes** servent à **structurer** le trajet et à rattacher entreprises / entrepôt ; le choix reste **manuel** côté admin (pas un algorithme seul qui “décide”).

### Ajout d’une **nouvelle entreprise**

- Dès qu’une entreprise est **créée ou activée** pour la livraison entreprise, l’équipe **l’ajoute explicitement** au bon **trajet** (ou en crée un nouveau segment) : **position dans la liste**, ordre par rapport à l’entrepôt, commune.
- **Aucune** règle du type “si elle est à moins de 10 km” ne décide à la place de l’admin ; la **mise à jour des trajets** est un **geste manuel** récurrent.

---

## 4) Rattachements obligatoires

### Entrepôt ↔ communes

- Un **entrepôt** couvre **une ou plusieurs communes** (liste configurable).
- Les entreprises **dans ces communes** (ou rattachées à+ cette zone) sont éligibles aux trajets partant de cet entrepôt.

### Livreur ↔ entrepôt

- Un **livreur** est **lié à un entrepôt** (un seul entrepôt principal par livreur pour la logique métier décrite).
- Les **livreurs 1, 2, … N** utilisés pour un jour J sont donc **ceux de cet entrepôt** (ou une liste définie dans l’admin).

---

## 5) Répartition des colis (200 max par livreur) — ordre strict

**Entrées** : pour le jour J, toutes les commandes `is_lunch` (colis) par entreprise, **dans l’ordre du trajet** déjà défini (entreprise la plus proche de l’entrepôt → … → dernière).

**Algorithme métier (résumé)** :

1. Initialiser **livreur courant = livreur 1** (du premier entrepôt concerné), **compteur = 0**.
2. Parcourir les **entreprises dans l’ordre du trajet** (de la première à la dernière).
3. Pour chaque entreprise, prendre les colis (commandes) dans l’ordre défini (ex. ordre de préparation, ordre de box, etc. — à préciser en interne).
4. Tant que **compteur < 200** :
  - affecter le colis au **livreur courant** ;
  - incrémenter le compteur.
5. Dès que **compteur atteint 200** pour ce livreur :
  - passer au **livreur suivant** (livreur 2, puis 3, …) ;
  - remettre **compteur = 0** ;
  - **continuer** à la **même entreprise** si elle n’est pas encore vide, ou à l’entreprise suivante selon le trajet.
6. Répéter jusqu’à ce que **tous les colis** de **toutes les entreprises** du trajet soient assignés, ou jusqu’au **dernier livreur** disponible.

**En résumé** : ce n’est pas “un pool mélangé” puis découpe ; c’est **chaîne ordonnée trajet + remplissage séquentiel 200 par livreur** jusqu’au dernier livreur et la dernière entreprise.

---

## 6) Livreur affilié et autres entreprises

- Un livreur **affilié** peut livrer pour **plusieurs entreprises** sur le même trajet (dès lors qu’elles sont **dans l’ordre du trajet** et sous le cap 200 colis).
- La règle **manuelle du trajet** prime sur toute fusion automatique “par proximité entre entreprises” sans validation admin.

---

## 7) Après la tournée entreprise (mode livreur)

- Tant qu’un livreur a des **livraisons entreprise** (`is_lunch`) **en cours / non terminées** pour la journée : il reste en **mode mission entreprise** (ou équivalent selon votre app).
- Quand **toutes** ses livraisons entreprise du jour sont **terminées** : il peut repasser en **mode classique** (disponible pour les livraisons hors trajet entreprise).

---

## 8) Écrans / actions admin à prévoir (checklist)

- Gérer **entrepôts** et **communes** liées (une ou plusieurs par entrepôt).
- Gérer **entreprises** avec **commune** (et rattachement logique à l’entrepôt).
- Gérer **livreurs** avec **entrepôt** lié.
- **Définir / éditer les trajets** : ordre manuel des entreprises (de la plus proche de l’entrepôt à la dernière) ; **à jour à chaque nouvelle entreprise** (ajout manuel dans le trajet).
- Lancer la **répartition des colis** (J‑1 après clôture, ex. 15h) : application des règles §5 sur les commandes `is_lunch` du jour J.
- Visualiser les **lots par livreur** (jusqu’à 200 colis) et la **suite du trajet** pour chaque livreur.

---

## 9) Résumé en une phrase

**Pas de distance magique (10 km ou autre)** : le réseau **grandit par ajouts manuels** d’entreprises dans les trajets. On **définit manuellement** le **trajet** (communes, ordre de la plus proche de l’**entrepôt** à la dernière entreprise), puis on **répartit les colis** en enchaînant **livreur 1 → 200 colis → livreur 2 → …** jusqu’au **dernier livreur** et la **dernière entreprise** du trajet.