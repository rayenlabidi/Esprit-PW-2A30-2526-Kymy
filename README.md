# Workify — Système de gestion d'événements (v2)

## Architecture MVC PHP

```
workify/
├── admin/                     ← Interface admin (index.php)
├── public/                    ← Interface utilisateur (index.php)
├── config/
│   └── database.php           ← Singleton PDO
├── Model/
│   ├── Event.php
│   └── EventCategory.php
├── Controller/
│   ├── AdminEventController.php      ← CRUD + tri + filtre catégorie
│   ├── AdminCategoryController.php   ← CRUD + tri
│   ├── AdminDashboardController.php  ← Statistiques avancées ★ NOUVEAU
│   └── UserEventController.php       ← CRUD + tri + filtre catégorie
└── View/
    ├── shared/
    │   ├── _sidebar.php         ← Sidebar admin réutilisable ★ NOUVEAU
    │   ├── _nav.php             ← Nav public réutilisable ★ NOUVEAU
    │   └── _styles_admin.php    ← CSS partagé admin ★ NOUVEAU
    ├── admin/
    │   ├── dashboard.php        ← Tableau de bord + 5 graphiques ★ NOUVEAU
    │   ├── listEvent.php        ← Liste + tri + export PDF ★ AMÉLIORÉ
    │   ├── listCategories.php   ← Liste + tri + barre % ★ AMÉLIORÉ
    │   ├── eventForm.php        ★ AMÉLIORÉ
    │   └── categoryForm.php     ★ AMÉLIORÉ
    └── public/
        ├── listEvents.php       ← Cartes + tri + filtre catégorie ★ AMÉLIORÉ
        ├── eventDetail.php      ★ AMÉLIORÉ
        └── eventForm.php        ★ AMÉLIORÉ

## Fonctionnalités ajoutées (v2)

### 🔗 Intégration User/Admin
- Templates partagés : sidebar admin et nav public (partiels PHP)
- Lien « Vue utilisateur » dans le panneau admin
- Lien « ⚙️ Admin » dans la nav publique
- Design tokens CSS unifiés

### 🔽 Tri (Sorting)
- Admin & Public : tri par titre, date, statut, participants
- En-têtes de colonnes cliquables avec indicateurs ↑ ↓ ↕
- Paramètres GET : ?sort=title&order=asc

### 📊 Statistiques (Dashboard Admin)
- KPIs : total, par statut, en ligne, capacité totale, catégories
- Graphique donut : répartition par statut
- Graphique bar : événements par catégorie
- Graphique ligne : timeline mensuelle
- Graphique donut : en ligne vs présentiel
- Graphique bar horizontal : capacité par catégorie
- Tableau des 5 derniers événements
- Bibliothèque : Chart.js 4.4 (CDN)

### 📄 Export PDF (Admin)
- Export jsPDF + autoTable via CDN
- En-tête colorée Workify, pied de page numéroté
- Toutes les données visibles dans la liste courante
- Respecte les filtres actifs

### 🔍 Filtres avancés
- Filtre par statut (pills) + filtre par catégorie (select)
- Combinables entre eux et avec la recherche
- Indicateur « X résultats trouvés »
