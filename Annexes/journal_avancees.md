# Journal des avancées – Projet NaturaCorp

Ce fichier sert de carnet de bord pour retracer toutes les étapes, décisions, difficultés et solutions rencontrées durant l’épreuve.

## Mode d’emploi
- Note chaque étape importante (début/fin d’une tâche, choix technique, problème rencontré, solution, etc.).
- Date chaque entrée pour faciliter le suivi chronologique.
- Utilise ce journal pour te repérer ou pour justifier ta démarche dans le rapport final.

---

## Besoins et fonctionnalités à suivre

**Tableau de bord commercial**

- [X] Afficher sous forme de graphique le nombre de ventes du commercial sur une période donnée
- [X] Afficher sous forme de graphique la liste des clients rapportés par le commercial sur une période donnée
- [ ] Afficher un tableau listant les dernières ventes (en attente et terminées) du commercial sur une période donnée
- [ ] Permettre le téléchargement en CSV séparé de chaque graphique ou tableau de données ci-dessus

**Page "Rapports"**

- [ ] (Tableau 1) Pharmacies par zone : permettre le téléchargement de toutes les infos de toutes les zones au format CSV
- [ ] (Tableau 1) Pharmacies par zone : permettre le téléchargement des infos d'une zone en particulier au format CSV
- [ ] (Tableau 2) Performances des commerciaux : permettre le téléchargement de toutes les infos de tous les commerciaux (ventes, zones affectées, nombre de pharmacies, total rapporté en €) au format CSV
- [ ] (Tableau 2) Performances des commerciaux : permettre le téléchargement des infos d'un commercial en particulier au format CSV

---

## Journal

**[12/06/2025 - 14:27]** Initialisation d’un dépôt Git pour versionner l’ensemble du projet et tracer toutes les modifications.

**1.0 Début** : Création d’une branche de développement pour débuter le travail sur les nouvelles fonctionnalités du dashboard commercial.

**[12/06/2025 - 15:20]** Création du contrôleur `ExportController.php` pour gérer les exports CSV des données. Ce contrôleur contient les méthodes suivantes :
- `exportCommercialSales` : Export des ventes d'un commercial sur une période donnée
- `exportCommercialClients` : Export des clients rapportés par un commercial sur une période donnée
- `exportCommercialRecentSales` : Export des dernières ventes d'un commercial
- `exportAllPharmaciesByZone` : Export de toutes les pharmacies par zone
- `exportPharmaciesByZone` : Export des pharmacies d'une zone spécifique
- `exportAllCommercialsPerformance` : Export des performances de tous les commerciaux
- `exportCommercialPerformance` : Export des performances d'un commercial spécifique

**[12/06/2025 - 15:25]** Ajout des routes dans `routes/web.php` pour les exports CSV :
- Routes pour les commerciaux :
  - `/export/sales` : Export des ventes du commercial
  - `/export/clients` : Export des clients rapportés par le commercial
  - `/export/recent-sales` : Export des dernières ventes du commercial
- Routes pour les administrateurs :
  - `/admin/export/pharmacies-by-zone` : Export de toutes les pharmacies par zone
  - `/admin/export/pharmacies-by-zone/{zone}` : Export des pharmacies d'une zone spécifique
  - `/admin/export/commercials-performance` : Export des performances de tous les commerciaux
  - `/admin/export/commercials-performance/{commercial}` : Export des performances d'un commercial spécifique

**[12/06/2025 - 15:30]** Modification du contrôleur `DashboardController.php` pour ajouter les méthodes de préparation des données pour les graphiques :
- Ajout de la méthode `prepareSalesChartData` pour générer les données des ventes sur 6 mois
- Ajout de la méthode `prepareClientsChartData` pour générer les données des clients rapportés sur 6 mois
- Ajout des données des graphiques dans la variable `$data` retournée à la vue

**[12/06/2025 - 15:35]** Modification de la vue `dashboard.blade.php` pour ajouter les graphiques et boutons d'export CSV :
- Ajout d'une section avec deux graphiques (ventes et clients rapportés par mois)
- Ajout des boutons d'export CSV pour chaque graphique
- Utilisation de Canvas pour l'affichage des graphiques

**[12/06/2025 - 15:40]** Ajout du bouton d'export CSV pour les commandes récentes dans `dashboard.blade.php` :
- Modification de la section des commandes récentes pour ajouter un bouton d'export CSV
- Mise en page améliorée avec flexbox pour aligner les boutons

**[12/06/2025 - 15:45]** Intégration de Chart.js et initialisation des graphiques :
- Ajout de la bibliothèque Chart.js via CDN dans le head du document
- Ajout du code JavaScript pour initialiser les graphiques des ventes et des clients
- Configuration des options des graphiques pour un affichage optimal (formatage des valeurs, couleurs, etc.)

**[12/06/2025 - 16:00]** Amélioration de la vue des rapports administrateur (`admin/reports/index.blade.php`) :
- Ajout des boutons d'export CSV pour le tableau des pharmacies par zone
- Ajout d'un bouton d'export global pour toutes les zones
- Ajout des boutons d'export CSV pour le tableau des performances des commerciaux
- Ajout d'un bouton d'export global pour tous les commerciaux
- Mise en page améliorée avec flexbox pour aligner les boutons et titres

**[12/06/2025 - 16:15]** Ajout de graphiques dans la vue des rapports administrateur :
- Modification du `ReportController.php` pour préparer les données des graphiques
- Ajout d'un graphique circulaire (pie chart) pour visualiser la répartition des pharmacies par zone
- Ajout d'un graphique à barres pour visualiser les performances des commerciaux
- Configuration des options des graphiques pour un affichage optimal (légendes, tooltips, etc.)

**[12/06/2025 - 16:20]** Correction d'un bug SQL dans le `DashboardController.php` :
- Problème identifié : erreur "Integrity constraint violation: 1052 Column 'created_at' in where clause is ambiguous"
- Cause : ambigüité de la colonne `created_at` présente dans les tables `orders` et `order_items`
- Solution : spécification explicite de la table dans les clauses WHERE (`orders.created_at` au lieu de `created_at`)
- Impact : résolution de l'erreur empêchant l'accès à l'espace commercial

**[12/06/2025 - 16:25]** Amélioration du style des graphiques et boutons dans le tableau de bord commercial :
- Modification de la disposition des graphiques pour qu'ils soient les uns en dessous des autres
- Ajustement de la largeur des graphiques à 90% de la page et centrage
- Augmentation de la hauteur des graphiques pour une meilleure lisibilité
- Modification du style des boutons d'export CSV pour une meilleure visibilité sur fond blanc
- Harmonisation des styles des boutons sur l'ensemble du tableau de bord

**[12/06/2025 - 16:40]** Correction du problème d'affichage des graphiques comprimés :
- Modification des conteneurs de graphiques pour qu'ils prennent 100% de la largeur (`w-full` au lieu de `w-11/12`)
- Ajout de classes CSS personnalisées pour les conteneurs de graphiques (`chart-container`) et les éléments canvas (`chart-canvas`)
- Implémentation d'un script JavaScript pour ajuster dynamiquement la taille des graphiques au chargement et au redimensionnement
- Ajout de styles CSS pour forcer les éléments canvas à prendre toute la largeur disponible
- Optimisation du rendu des graphiques avec des options de mise en page et de légende améliorées

