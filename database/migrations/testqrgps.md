### 📋 Processus d'Implémentation Phase 1 : Gestion de l'Entrepôt (Restaurant Central)
C'est la base. Pour que le livreur sache où aller chercher la commande, il faut une adresse de départ unique et centralisée.

1. Backend (Laravel)
   - Créer une migration create_warehouses_table (ou entrepot_configs ) pour stocker : Nom, Adresse, Ville, Pays, Latitude, Longitude.
   - Créer un Modèle Warehouse (ou Entrepot ).
   - Créer un Contrôleur Admin\WarehouseController pour gérer ces infos (CRUD : Afficher, Modifier).
   - Ajouter une vue dans le Dashboard Admin "Info Entrepôt".
   - API : Créer un endpoint GET /api/config/warehouse pour que l'app livreur récupère ces coordonnées. Phase 2 : Navigation GPS "Turn-by-Turn" (Livreur)
L'objectif est de guider le livreur en deux étapes claires : 1. Vers l'Entrepôt (Récupération) -> 2. Vers le Client (Livraison).

1. Logique Mobile (Flutter)
   - Modifier l'écran de détail de livraison pour avoir deux états distincts :
     - État A (Avant Récupération) : Le bouton "Navigation" lance le GPS vers l' Entrepôt .
     - État B (Après Récupération / En cours de livraison) : Le bouton "Navigation" lance le GPS vers le Client .
   - Utiliser le package url_launcher pour ouvrir Google Maps / Waze en mode "Navigation" (plus fiable et moins cher que d'intégrer une carte de navigation complète in-app pour l'instant).
     - Note : Intégrer une "vraie" navigation turn-by-turn DANS l'app (comme Uber) demande des services payants (Mapbox) et est complexe. Lancer Google Maps en mode "Trajet" est la norme pour une V1/V2. Phase 3 : Validation par QR Code (Sécurité Box Entreprise)
Pour éviter qu'un livreur ne valide une livraison sans être sur place.

1. Backend (Laravel)
   
   - Ajouter un champ qr_code_token unique dans la table commandes ou livraisons lors de la création d'une commande "Entreprise" (Box).
   - Générer un QR Code contenant ce token (ou l'ID de la commande + token) qui sera collé sur le Casier ou affiché sur le bon de commande.
   - Créer un endpoint API POST /api/livreur/verify-qr qui prend le code scanné et valide la livraison.
2. Mobile (Flutter)
   
   - Ajouter le package mobile_scanner ou qr_code_scanner .
   - Dans l'écran de livraison, si le type est "Entreprise", remplacer le bouton "Livré" par un bouton "Scanner QR Code".
   - Ouvrir la caméra, scanner le code, envoyer à l'API.
   - Si succès : Marquer la commande comme livrée automatiquement.