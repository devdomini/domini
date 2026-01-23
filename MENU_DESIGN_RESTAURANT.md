# 🍽️ Design Menu Restaurant - Domini

Ce document détaille le nouveau design élégant de la page menu, inspiré des **menus de restaurant papier haut de gamme**.

---

## 🎨 Concept de Design

Le design de la page menu a été entièrement repensé pour évoquer l'**expérience d'un menu de restaurant physique élégant**, avec :

### Caractéristiques Principales

1. **🎭 Typographie Sophistiquée**
   - `Playfair Display` : Titres et prix (serif élégant)
   - `Cormorant Garamond` : Descriptions et contenu (serif raffiné)
   - `Inter` : Navigation et boutons (sans-serif moderne)

2. **🎨 Palette de Couleurs Domini**
   - **Orange Domini** : `#D9542A` (couleur principale)
   - **Jaune/Or Domini** : `#F7B801` (couleur secondaire)
   - **Blanc/Crème** : `#FFFFFF`, `#FFF8F0`, `#FFFAF5`
   - **Gris Foncé** : `#2C2C2C`, `#1A1A1A`, `#666666`

3. **📄 Effet Papier & Texture**
   - Fond dégradé sombre chaleureux
   - Texture de papier subtile
   - Motifs répétitifs discrets
   - Bordures dorées et doubles lignes

4. **✨ Ornements Décoratifs**
   - Symboles décoratifs (❦, ✦)
   - Lignes pointillées entre nom et prix
   - Séparateurs dorés élégants
   - Bordures doubles

---

## 🏗️ Structure de la Page

### 1. Navigation (Fixed)
```css
- Fond : Gris foncé semi-transparent (#2C2C2C)
- Bordure inférieure : Orange Domini (#D9542A)
- Logo : Playfair Display avec effet d'ombre orange
- Liens : Blanc (#FFFFFF) avec soulignement orange/jaune au survol
- Bouton CTA : Dégradé orange/jaune avec transformation au survol
```

### 2. Hero Section
```css
- Hauteur : 300px
- Fond : Dégradé gris foncé (#2C2C2C → #1A1A1A)
- Pattern géométrique : Lignes diagonales orange (opacité 20%)
- Radial gradients : Orange et jaune (opacité 15%)
- Image : Opacité 40% avec effet sépia et masque dégradé
- Bordure inférieure : Double ligne orange (#D9542A)
```

### 3. Section Titre
```css
- Fond : Dégradé blanc → crème (#FFFFFF → #FFF8F0)
- Titre : 5rem, Playfair Display, lettres espacées, couleur gris foncé
- Ornements : Symbole ✦ orange en haut
- Divider : Lignes jaunes + points orange + symboles ❦ jaune
- Description : Italic, Cormorant Garamond, couleur gris (#666)
- Bordure inférieure : Double ligne orange (#D9542A)
```

### 4. Catégories (Tabs Élégants)
```css
- Container : Bordure orange (#D9542A), fond blanc-crème
- Boutons : Séparés par lignes orange verticales
- Style : Playfair Display, lettres espacées, texte gris foncé
- Hover/Active : Fond dégradé orange→jaune, texte blanc avec ombre
- Effet : Overlay orange/jaune avec transition fluide
```

### 5. Grille de Plats (2 colonnes)
```css
- Layout : 2 colonnes sur desktop, 1 sur mobile
- Gap : 3rem (vertical), 4rem (horizontal)
- Max-width : 1100px centré
```

---

## 🎴 Cartes de Plats (Style Menu Restaurant)

### Structure de Carte

```
┌─────────────────────────────────┐
│                                 │
│         IMAGE (280px)           │ ← Bordure dorée en bas
│         + Badge "Populaire"     │
│                                 │
├─────────────────────────────────┤
│  Nom du Plat .......... Prix    │ ← Pointillés dorés
│                                 │
│  Description italique           │ ← Cormorant Garamond
│                                 │
├─────────────────────────────────┤
│  Catégorie    [Commander]       │ ← Footer avec actions
└─────────────────────────────────┘
```

### Styles Détaillés

#### 📸 Image
- **Hauteur** : 280px
- **Effet hover** : Zoom 1.08x + overlay sombre
- **Filtre** : Brightness + saturation augmentées
- **Bordure** : 2px dorée en bas (#E8D4B0)

#### 🏷️ Badge "Populaire"
- **Position** : Top-right avec padding
- **Style** : Dégradé or, texte uppercase, lettrage espacé
- **Ombre** : Box-shadow dorée
- **Bordure** : 1px blanc semi-transparent

#### 📝 Contenu

**Header (Nom + Prix)**
- Display : Flex avec séparateur pointillé doré
- **Nom** : 1.75rem, Playfair Display, marron foncé
- **Prix** : 1.5rem, Playfair Display, or (#A88B5C)
- **Séparateur** : Ligne pointillée dorée automatique

**Description**
- Font : Cormorant Garamond italic
- Taille : 1.05rem
- Couleur : Marron moyen (#5A4A3E)
- Line-height : 1.9 (aéré)

#### 🎯 Footer
- **Séparateur** : 1px ligne dorée
- **Catégorie** : Texte gris-marron, Inter, 0.9rem
- **Bouton** : Dégradé or, uppercase, lettrage espacé
  - Hover : Dégradé inversé + lift + ombre

### Effets de Carte

```css
/* État normal */
- Background : Dégradé blanc → crème
- Border : 2px #E8D4B0
- Box-shadow : Ombre douce marron
- Inset shadow : Highlight blanc en haut

/* Hover */
- Transform : translateY(-4px) + scale(1.02)
- Border-color : #C4A572 (or plus prononcé)
- Box-shadow : Ombre plus forte
- Pseudo ::before : Bordure dorée animée (opacity 0.3)
```

---

## 🎭 Animations

### 1. Apparition des Cartes
```css
@keyframes fadeInUp {
  0% : opacity 0, translateY(40px)
  100% : opacity 1, translateY(0)
}

- Duration : 0.8s
- Timing : cubic-bezier(0.4, 0, 0.2, 1)
- Stagger : 0.1s par carte
```

### 2. Navigation Scroll
```javascript
window.scrollY > 50 :
  - Box-shadow augmentée
  - Background opacity 100%
```

### 3. Filtrage Catégories
```javascript
Clic sur catégorie :
  - Fade out cartes non-correspondantes (300ms)
  - Fade in cartes correspondantes avec stagger (50ms)
  - Animation cascade
```

### 4. Hover Effects
- **Navigation links** : translateY(-2px) + text-shadow doré
- **Boutons** : Shimmer effect (shine animation)
- **Cartes** : Lift + scale + border glow
- **Images** : Zoom + overlay sombre

---

## 📱 Responsive Design

### Desktop (> 1200px)
- Grille : 2 colonnes
- Navigation : Tous les liens visibles
- Catégories : Horizontales avec bordures verticales
- Images : 280px de hauteur

### Tablette (769px - 1200px)
- Grille : 1 colonne (max-width 700px)
- Titre : 3.5rem
- Catégories : Toujours horizontales mais moins d'espacement

### Mobile (< 768px)
- Navigation : Logo + Burger + CTA uniquement
- Hero : 250px de hauteur
- Titre : 2.5rem, espacement réduit
- Catégories : **Verticales** avec bordures horizontales
- Grille : 1 colonne, gap 2rem
- Images : 220px de hauteur
- Footer carte : Colonne (bouton pleine largeur)
- Texture fond : Opacity réduite (20%)

---

## 🎨 Placeholder pour Images Manquantes

### Design
```css
- Background : Dégradé crème (#F9F6F1 → #EAD5BA)
- Pattern : Lignes diagonales dorées
- Icon : SVG assiette + couverts (100px)
  - Color : #C4A572
  - Opacity : 0.6
  - Drop-shadow : Ombre dorée
- Texte : Nom du plat (uppercase, Playfair, or)
```

---

## 🎯 Messages Vides

### Aucun plat dans catégorie
```css
- Background : Dégradé blanc → crème
- Border : 2px dashed doré
- Border-radius : 12px
- Padding : 5rem 2rem
- Titre : 2rem, Playfair Display, marron
- Texte : 1.1rem, Cormorant italic, gris-marron
```

---

## 🔧 Optimisations

### 1. Performance
- Lazy loading des images
- Animations avec `will-change` implicit
- Transitions CSS hardware-accelerated
- Opacity pour fade effects (performant)

### 2. Accessibilité
- Contraste suffisant (AAA)
- Focus states sur tous les éléments interactifs
- Texte lisible (min 1rem)
- Espacement généreux

### 3. UX
- Feedback visuel clair (hover, active)
- Transitions fluides (0.3s - 0.4s)
- Loading states élégants
- Messages d'erreur stylisés

---

## 💡 Inspirations Design

Ce design s'inspire de :

1. **Menus de Restaurants Gastronomiques**
   - Typographie serif élégante
   - Ornements discrets
   - Palette or/crème/marron

2. **Menus Papier Vintage**
   - Texture papier
   - Bordures décoratives
   - Séparateurs pointillés

3. **Design Material Premium**
   - Ombres subtiles et élégantes
   - Élévations au hover
   - Micro-interactions fluides

4. **Art Déco**
   - Lignes géométriques
   - Ornements stylisés
   - Symétrie et équilibre

---

## 📚 Polices Utilisées

### Playfair Display (Serif)
```
Usage : Titres, noms de plats, prix
Weights : 400, 500, 600, 700, 800, 900
Caractère : Élégant, raffiné, haut de gamme
```

### Cormorant Garamond (Serif)
```
Usage : Descriptions, textes principaux
Weights : 300, 400, 500, 600, 700
Caractère : Classique, lisible, italien
```

### Inter (Sans-serif)
```
Usage : Navigation, boutons, labels
Weights : 400, 500, 600, 700, 800, 900
Caractère : Moderne, net, professionnel
```

---

## 🎨 Codes Couleurs

### Couleurs Principales

| Couleur | Hex | Usage |
|---------|-----|-------|
| Or Principal | `#C4A572` | Accents, bordures, titres |
| Or Foncé | `#A88B5C` | Hover, prix, détails |
| Or Clair | `#E8D4B0` | Bordures subtiles, fond |
| Crème | `#F4E9D8` | Fond principal, texte clair |
| Beige | `#EAD5BA` | Dégradés, séparations |
| Papier | `#F9F6F1` | Fond cartes, zones claires |
| Marron Foncé | `#2C1810` | Texte principal, navigation |
| Marron Moyen | `#4A3428` | Texte secondaire |
| Marron Clair | `#5A4A3E` | Descriptions |
| Gris-Marron | `#8A7A6E` | Détails, métadonnées |

### Transparences
- Navigation scrolled : `rgba(44, 24, 16, 1)`
- Overlay images : `rgba(44, 24, 16, 0.3)`
- Patterns : `rgba(196, 165, 114, 0.08)`
- Bordures actives : `rgba(196, 165, 114, 0.3)`

---

## ✅ Checklist de Qualité

- [x] Design élégant et raffiné
- [x] Typographie harmonieuse
- [x] Palette de couleurs cohérente
- [x] Animations fluides
- [x] Responsive complet
- [x] Placeholder élégant pour images
- [x] Messages d'erreur stylisés
- [x] Hover effects subtils
- [x] Performance optimisée
- [x] Accessibilité respectée
- [x] Code propre et commenté
- [x] Navigation intuitive
- [x] Filtrage catégories fonctionnel
- [x] Smartsupp intégré

---

## 🚀 Résultat Final

La page menu ressemble désormais à un **menu de restaurant papier haut de gamme**, avec :

- ✨ Design élégant et sophistiqué
- 📄 Sensation de menu physique
- 🎨 Palette chaude et accueillante
- 🔄 Animations fluides et naturelles
- 📱 Parfaitement responsive
- 🍽️ Focus sur les plats et la gastronomie

L'utilisateur a l'impression de **feuilleter un vrai menu de restaurant** !

---

Créé le : 21 janvier 2026  
Version : 2.0 - Restaurant Premium Design
