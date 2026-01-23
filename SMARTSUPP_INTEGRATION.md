# 💬 Smartsupp Live Chat - Intégration

Ce document détaille l'intégration de Smartsupp Live Chat sur le site vitrine Domini.

---

## 📋 Pages Intégrées

Le script Smartsupp a été ajouté sur **toutes les pages publiques** du site :

1. ✅ **Page d'accueil** (`welcome.blade.php`) - `/`
2. ✅ **Page Menu** (`menu.blade.php`) - `/notre-menu`
3. ✅ **Page Support** (`support.blade.php`) - `/support`
4. ✅ **Page Inscription** (`inscription.blade.php`) - `/inscription`

---

## 🔑 Clé API

```javascript
_smartsupp.key = '73234f96a43e1e6223c9bc16cc051c9a054376c2';
```

---

## 📍 Emplacement du Script

Le script est placé **juste avant la balise de fermeture `</body>`** dans chaque fichier :

```html
    <!-- Autres scripts de la page -->
    </script>

    <!-- Smartsupp Live Chat script -->
    <script type="text/javascript">
    var _smartsupp = _smartsupp || {};
    _smartsupp.key = '73234f96a43e1e6223c9bc16cc051c9a054376c2';
    window.smartsupp||(function(d) {
      var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
      s=d.getElementsByTagName('script')[0];c=d.createElement('script');
      c.type='text/javascript';c.charset='utf-8';c.async=true;
      c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
    })(document);
    </script>
    <noscript>Powered by <a href="https://www.smartsupp.com" target="_blank">Smartsupp</a></noscript>
</body>
</html>
```

---

## 🎯 Fonctionnalités

### Widget de Chat
- **Position** : Coin inférieur droit de la page (par défaut)
- **Visible** : Sur toutes les pages du site vitrine
- **Responsive** : S'adapte aux mobiles et tablettes
- **Async** : Chargement asynchrone pour ne pas bloquer la page

### Fallback
```html
<noscript>Powered by <a href="https://www.smartsupp.com" target="_blank">Smartsupp</a></noscript>
```
- Affiche un message si JavaScript est désactivé

---

## 🎨 Personnalisation

Pour personnaliser le widget, vous pouvez ajouter des options dans le dashboard Smartsupp ou via le script :

### Exemples de Personnalisation

```javascript
var _smartsupp = _smartsupp || {};
_smartsupp.key = '73234f96a43e1e6223c9bc16cc051c9a054376c2';

// Personnalisation optionnelle
_smartsupp.language = 'fr';              // Langue (français)
_smartsupp.name = 'Visiteur Domini';     // Nom par défaut
_smartsupp.email = '';                   // Email par défaut

// Couleur personnalisée
_smartsupp.color = '#D9542A';            // Couleur Domini (orange)

window.smartsupp||(function(d) {
  // ... reste du code
})(document);
```

---

## 📊 Statistiques et Dashboard

### Accès au Dashboard
- **URL** : [https://www.smartsupp.com/app/](https://www.smartsupp.com/app/)
- **Connexion** : Utilisez vos identifiants Smartsupp

### Métriques Disponibles
- Nombre de conversations
- Temps de réponse moyen
- Taux de satisfaction client
- Historique des chats
- Visiteurs en ligne

---

## 🔧 Configuration Avancée

### Masquer le Widget sur Certaines Pages

Si vous souhaitez masquer le widget sur une page spécifique :

```javascript
// Masquer le widget
smartsupp('chat:hide');

// Afficher le widget
smartsupp('chat:show');
```

### Événements Personnalisés

```javascript
// Déclencher l'ouverture du chat
smartsupp('chat:open');

// Fermer le chat
smartsupp('chat:close');

// Envoyer un message automatique
smartsupp('chat:message', 'Bonjour ! Comment puis-je vous aider ?');
```

### Variables Visiteur

```javascript
// Envoyer des informations sur le visiteur
smartsupp('name', 'Jean Dupont');
smartsupp('email', 'jean@example.com');
smartsupp('phone', '+225 01 02 03 04 05');
```

---

## 📱 Responsive

Le widget Smartsupp est **entièrement responsive** :

### Mobile
- Widget réduit en icône flottante
- Chat en plein écran au clic
- Optimisé pour le tactile

### Tablette
- Widget de taille moyenne
- Chat en overlay
- Navigation tactile

### Desktop
- Widget complet
- Chat dans une fenêtre
- Notifications desktop possibles

---

## 🚀 Performance

### Chargement Asynchrone
```javascript
c.async=true;
```
- Le script ne bloque pas le chargement de la page
- Meilleure performance globale

### Mise en Cache
- Le script est mis en cache par le navigateur
- Chargement plus rapide lors des visites suivantes

---

## 🛠️ Dépannage

### Le widget ne s'affiche pas

1. **Vérifier la clé API**
   - Assurez-vous que la clé est correcte : `73234f96a43e1e6223c9bc16cc051c9a054376c2`

2. **Console du navigateur**
   - Ouvrez les DevTools (F12)
   - Vérifiez les erreurs JavaScript

3. **Bloqueur de publicités**
   - Certains bloqueurs peuvent bloquer les widgets de chat
   - Testez en mode navigation privée

4. **Connexion Internet**
   - Le script doit pouvoir charger depuis `smartsuppchat.com`

### Le widget est décalé

1. Vérifiez les CSS de votre site
2. Assurez-vous qu'il n'y a pas de `z-index` conflictuel
3. Ajustez la position dans le dashboard Smartsupp

---

## 📞 Support Smartsupp

- **Site** : [https://www.smartsupp.com](https://www.smartsupp.com)
- **Documentation** : [https://docs.smartsupp.com](https://docs.smartsupp.com)
- **Support** : [support@smartsupp.com](mailto:support@smartsupp.com)

---

## ✅ Checklist d'Installation

- [x] Script ajouté sur `welcome.blade.php`
- [x] Script ajouté sur `menu.blade.php`
- [x] Script ajouté sur `support.blade.php`
- [x] Script ajouté sur `inscription.blade.php`
- [x] Clé API configurée
- [x] Chargement asynchrone activé
- [x] Fallback `<noscript>` ajouté

---

## 🎉 Résultat

Le chat Smartsupp est maintenant **actif sur tout le site vitrine** !

Les visiteurs peuvent désormais :
- 💬 Discuter en temps réel avec votre équipe
- ❓ Poser des questions sur les produits/services
- 📧 Laisser des messages hors ligne
- 📱 Utiliser le chat sur mobile

---

Créé le : 21 janvier 2026
Dernière mise à jour : 21 janvier 2026
