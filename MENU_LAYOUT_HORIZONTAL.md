# 🍽️ Menu Layout Horizontal - Style Restaurant

Ce document détaille le nouveau layout horizontal de la page menu, inspiré des menus de restaurants professionnels.

---

## 🎨 Concept de Design

Le menu a été redesigné avec un **layout horizontal** où chaque plat est présenté sur une ligne complète avec :
- **Contenu à gauche** : Nom, prix, description, catégorie
- **Image ronde à droite** : Photo du plat dans un cercle élégant

---

## 📐 Structure du Layout

### Vue Desktop

```
┌─────────────────────────────────────────────────────┐
│  NOM DU PLAT                 $12.99      [IMAGE]    │
│  Description délicieuse...                🍽️      │
│  [Catégorie]                                        │
├─────────────────────────────────────────────────────┤
│  AUTRE PLAT                  $15.99      [IMAGE]    │
│  Autre description...                     🍲      │
│  [Catégorie]                                        │
└─────────────────────────────────────────────────────┘
```

### Vue Mobile

```
┌──────────────────┐
│                  │
│    [IMAGE] 🍽️   │
│                  │
├──────────────────┤
│   NOM DU PLAT    │
│     $12.99       │
│                  │
│  Description...  │
│   [Catégorie]    │
└──────────────────┘
```

---

## 🎯 Éléments de Carte

### 1. **Contenu (Left Side)**

**Nom du Plat**
```css
- Font : Playfair Display (serif élégant)
- Taille : 2rem (32px)
- Poids : 700 (Bold)
- Couleur : #2C2C2C (gris foncé)
- Espacement lettres : 0.5px
```

**Prix**
```css
- Font : Playfair Display
- Taille : 2rem (32px)
- Poids : 900 (Black)
- Couleur : #F7B801 (jaune Domini)
- Format : "2500 FCFA"
```

**Description**
```css
- Font : Inter (sans-serif)
- Taille : 1rem (16px)
- Couleur : #666666 (gris moyen)
- Line-height : 1.6
- Style : Normal (pas italic)
```

**Badge Catégorie**
```css
- Background : #F7B801 (jaune)
- Couleur texte : #2C2C2C
- Padding : 0.25rem 0.75rem
- Border-radius : 4px
- Font : Inter, 0.85rem, 600
```

### 2. **Image (Right Side)**

**Container**
```css
- Dimensions : 200px × 200px
- Border-radius : 50% (cercle parfait)
- Border : 3px solid #F7B801
- Background : #F5F5F5 (si pas d'image)
```

**Badge "Populaire"** (sur les 2 premiers plats)
```css
- Position : Top-right de l'image
- Background : #D9542A (orange)
- Couleur : #FFFFFF
- Padding : 0.35rem 0.75rem
- Border-radius : 20px (pill shape)
- Font : Inter, 0.75rem, uppercase
- Z-index : 10
```

**Placeholder** (si pas d'image)
```css
- Background : #FFF8F0 (crème clair)
- Icon : SVG assiette + couverts
- Taille icon : 80px
- Couleur icon : #D9542A (opacité 40%)
```

---

## 🎨 Palette de Couleurs

| Élément | Couleur | Usage |
|---------|---------|-------|
| Nom plat | `#2C2C2C` | Texte principal |
| Prix | `#F7B801` | Accent jaune |
| Description | `#666666` | Texte secondaire |
| Badge catégorie | `#F7B801` | Background badge |
| Bordure image | `#F7B801` | Cercle or |
| Badge Populaire | `#D9542A` | Background orange |
| Séparateur | `#E5E5E5` | Bordure entre plats |
| Hover background | `#FAFAFA` | Gris très clair |

---

## 📱 Responsive Design

### Desktop (> 768px)
```css
- Grid : 2 colonnes (contenu | image)
- Gap : 2rem
- Padding : 2.5rem 3rem
- Alignement : Vertical center
- Image : Droite, 200px
```

### Mobile (< 768px)
```css
- Grid : 1 colonne (image en haut, contenu en bas)
- Gap : 1.5rem
- Padding : 2rem 1.5rem
- Text-align : center
- Image : Centrée, 180px
- Header : Colonne (nom sur prix)
```

---

## ⚡ Animations

### Hover sur Carte
```css
- Background : #FFFFFF → #FAFAFA
- Transform : translateX(5px) (décalage léger à droite)
- Duration : 0.4s ease
```

### Filtrage Catégories
```css
- Apparition : translateX(-20px) → translateX(0)
- Opacity : 0 → 1
- Stagger delay : 50ms par carte
- Duration : 0.4s ease
```

### Image Placeholder
```css
- Icon : Static (pas d'animation)
- Transition : Smooth sur hover de la carte
```

---

## 🔧 CSS Grid Structure

```css
.menu-items-grid {
    display: flex;
    flex-direction: column;
    gap: 0; /* Pas de gap, bordures entre éléments */
}

.menu-item-card {
    display: grid;
    grid-template-columns: 1fr 200px;
    gap: 2rem;
    align-items: center;
    border-bottom: 1px solid #E5E5E5;
}
```

---

## 🎭 Typographie

### Polices Utilisées

**Playfair Display** (Serif)
- Nom du plat : 2rem, weight 600
- Prix : 2rem, weight 900
- Usage : Éléments principaux et élégants

**Inter** (Sans-serif)
- Description : 1rem, weight 400
- Badge catégorie : 0.85rem, weight 600
- Badge Populaire : 0.75rem, weight 700
- Usage : Texte secondaire et labels

---

## 📊 Hiérarchie Visuelle

1. **Nom du plat** (le plus important)
   - Taille grande (2rem)
   - Police serif élégante
   - Couleur foncée

2. **Prix** (très visible)
   - Même taille que le nom
   - Couleur jaune accent
   - Poids maximum (900)

3. **Image** (support visuel)
   - Grande et ronde
   - Bordure jaune
   - Positionnée à droite

4. **Description** (information)
   - Taille moyenne (1rem)
   - Couleur grise
   - Police sans-serif lisible

5. **Catégorie** (contexte)
   - Petit badge
   - Couleur accent
   - Discret mais visible

---

## ✨ Avantages du Layout

### ✅ Pour l'Utilisateur
- **Scan rapide** : Layout horizontal facile à parcourir
- **Info claire** : Nom + prix immédiatement visibles
- **Professionnel** : Style restaurant authentique
- **Images attractives** : Cercles élégants qui attirent l'œil

### ✅ Pour le Design
- **Espace optimisé** : Une colonne, pas de grille complexe
- **Cohérent** : Même structure pour tous les plats
- **Scalable** : S'adapte à différentes longueurs de texte
- **Clean** : Fond blanc, séparateurs subtils

### ✅ Pour la Performance
- **Flexbox simple** : Moins de calculs CSS
- **Images optimisées** : Taille fixe (200px)
- **Animations légères** : Transform simples
- **Responsive facile** : Un seul breakpoint

---

## 🎯 Différences vs Ancien Design

| Aspect | Ancien | Nouveau |
|--------|---------|---------|
| Layout | Grid 2 colonnes | Liste horizontale |
| Images | Rectangulaires | Rondes (200px) |
| Position image | Au-dessus | À droite |
| Prix | Séparateur pointillé | À côté du nom |
| Catégorie | Footer | Badge jaune |
| Background | Cartes blanches | Fond blanc continu |
| Séparateurs | Bordures cartes | Lignes entre plats |
| Hover | Lift + scale | Slide droite + bg |

---

## 📝 Notes d'Implémentation

### HTML Structure
```html
<div class="menu-item-card">
    <!-- Contenu d'abord (left) -->
    <div class="menu-item-content">
        <div class="menu-item-header">
            <h3>Nom</h3>
            <span>Prix</span>
        </div>
        <p>Description</p>
        <span class="menu-item-category">Catégorie</span>
    </div>
    
    <!-- Image ensuite (right) -->
    <div class="menu-item-image">
        <img src="..." alt="...">
        <span class="menu-item-badge">Populaire</span>
    </div>
</div>
```

### Ordre CSS Grid
- Grid définit `grid-template-columns: 1fr 200px`
- Le contenu occupe `1fr` (flexible)
- L'image occupe `200px` (fixe)
- Gap de `2rem` entre les deux

### Filtrage Catégories
- JavaScript change `display: none` → `display: grid`
- Animation avec `translateX(-20px)` → `translateX(0)`
- Stagger de 50ms pour effet cascade

---

## 🚀 Résultat Final

Le menu ressemble maintenant à un **vrai menu de restaurant** avec :

✅ **Layout professionnel** (horizontal)  
✅ **Images rondes élégantes**  
✅ **Prix bien visibles** (jaune)  
✅ **Badges catégories** (discrets mais utiles)  
✅ **Fond blanc épuré** (pas de texture)  
✅ **Responsive parfait** (mobile optimisé)  
✅ **Animations fluides** (slide, fade)  
✅ **Typographie mixte** (serif + sans-serif)

---

Créé le : 22 janvier 2026  
Version : 3.0 - Layout Horizontal Restaurant
