<?php
// Ce fichier contient uniquement les modifications à apporter au DashboardController.php

// Pour la méthode prepareSalesChartData, remplacer la section de détermination de l'intervalle par :

// Déterminer l'intervalle approprié en fonction de la durée
$diffInDays = $startDate->diffInDays($endDate);

if ($diffInDays <= 31) {
    // Période courte (moins d'un mois) : afficher par jour
    $interval = 'day';
} else if ($diffInDays <= 60) {
    // Période moyenne (1-2 mois) : afficher par semaine
    $interval = 'week';
} else if ($diffInDays <= 90) {
    // Période 2-3 mois : afficher par 2 semaines
    $interval = 'biweekly';
}

// Pour la méthode prepareClientsChartData, faire la même modification

// Ajouter la gestion de l'intervalle biweekly dans la section de génération des données :
} else if ($interval === 'biweekly') {
    // Données bi-hebdomadaires (toutes les 2 semaines)
    $biweekStart = clone $currentDate;
    $biweekEnd = (clone $currentDate)->addDays(13); // 14 jours (2 semaines) - 1
    if ($biweekEnd > $endDate) $biweekEnd = clone $endDate;
    
    $query->whereBetween('orders.created_at', [$biweekStart->format('Y-m-d'), $biweekEnd->format('Y-m-d')]);
    $label = $biweekStart->format('d') . '-' . $biweekEnd->format('d M');
    $nextDate = (clone $currentDate)->addDays(14); // Avancer de 2 semaines
}
