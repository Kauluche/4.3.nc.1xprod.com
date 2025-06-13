<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Order;
use App\Models\Pharmacy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Zone;
use App\Models\Notification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // ... autres méthodes inchangées ...

    /**
     * Prépare les données pour le graphique des ventes
     *
     * @param User $user
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function prepareSalesChartData($user, Carbon $startDate, Carbon $endDate)
    {
        $pharmacyIds = $user->pharmacies()->pluck('id')->toArray();
        
        $salesData = [];
        $labels = [];
        $interval = 'month'; // Par défaut, intervalle mensuel
        
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
        
        // Générer les données selon l'intervalle approprié
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $query = Order::whereIn('pharmacy_id', $pharmacyIds);
            
            if ($interval === 'day') {
                // Données quotidiennes
                $query->whereDate('orders.created_at', $currentDate->format('Y-m-d'));
                $label = $currentDate->format('d M');
                $nextDate = (clone $currentDate)->addDay();
            } else if ($interval === 'week') {
                // Données hebdomadaires
                $weekStart = clone $currentDate;
                $weekEnd = (clone $currentDate)->endOfWeek();
                if ($weekEnd > $endDate) $weekEnd = clone $endDate;
                
                $query->whereBetween('orders.created_at', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
                $label = 'S' . $currentDate->format('W') . ' ' . $currentDate->format('M');
                $nextDate = (clone $currentDate)->addWeek();
            } else if ($interval === 'biweekly') {
                // Données bi-hebdomadaires (toutes les 2 semaines)
                $biweekStart = clone $currentDate;
                $biweekEnd = (clone $currentDate)->addDays(13); // 14 jours (2 semaines) - 1
                if ($biweekEnd > $endDate) $biweekEnd = clone $endDate;
                
                $query->whereBetween('orders.created_at', [$biweekStart->format('Y-m-d'), $biweekEnd->format('Y-m-d')]);
                $label = $biweekStart->format('d') . '-' . $biweekEnd->format('d M');
                $nextDate = (clone $currentDate)->addDays(14); // Avancer de 2 semaines
            } else {
                // Données mensuelles
                $query->whereYear('orders.created_at', $currentDate->format('Y'))
                      ->whereMonth('orders.created_at', $currentDate->format('m'));
                $label = $currentDate->format('M Y');
                $nextDate = (clone $currentDate)->addMonth();
            }
            
            // Calculer le montant total des ventes pour cette période
            $periodSales = $query->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->select(DB::raw('SUM(order_items.quantity * order_items.unit_price * (1 - order_items.discount_percentage / 100)) as total_amount'))
                ->first()
                ->total_amount ?? 0;
            
            $labels[] = $label;
            $salesData[] = round($periodSales, 2);
            
            $currentDate = $nextDate;
        }
        
        return [
            'labels' => $labels,
            'data' => $salesData
        ];
    }
    
    /**
     * Prépare les données pour le graphique des clients rapportés
     *
     * @param User $user
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function prepareClientsChartData($user, Carbon $startDate, Carbon $endDate)
    {
        $clientsData = [];
        $labels = [];
        $interval = 'month'; // Par défaut, intervalle mensuel
        
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
        
        // Générer les données selon l'intervalle approprié
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $query = Pharmacy::where('commercial_id', $user->id);
            
            if ($interval === 'day') {
                // Données quotidiennes
                $query->whereDate('created_at', $currentDate->format('Y-m-d'));
                $label = $currentDate->format('d M');
                $nextDate = (clone $currentDate)->addDay();
            } else if ($interval === 'week') {
                // Données hebdomadaires
                $weekStart = clone $currentDate;
                $weekEnd = (clone $currentDate)->endOfWeek();
                if ($weekEnd > $endDate) $weekEnd = clone $endDate;
                
                $query->whereBetween('created_at', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
                $label = 'S' . $currentDate->format('W') . ' ' . $currentDate->format('M');
                $nextDate = (clone $currentDate)->addWeek();
            } else if ($interval === 'biweekly') {
                // Données bi-hebdomadaires (toutes les 2 semaines)
                $biweekStart = clone $currentDate;
                $biweekEnd = (clone $currentDate)->addDays(13); // 14 jours (2 semaines) - 1
                if ($biweekEnd > $endDate) $biweekEnd = clone $endDate;
                
                $query->whereBetween('created_at', [$biweekStart->format('Y-m-d'), $biweekEnd->format('Y-m-d')]);
                $label = $biweekStart->format('d') . '-' . $biweekEnd->format('d M');
                $nextDate = (clone $currentDate)->addDays(14); // Avancer de 2 semaines
            } else {
                // Données mensuelles
                $query->whereYear('created_at', $currentDate->format('Y'))
                      ->whereMonth('created_at', $currentDate->format('m'));
                $label = $currentDate->format('M Y');
                $nextDate = (clone $currentDate)->addMonth();
            }
            
            // Compter le nombre de clients rapportés pour cette période
            $periodClients = $query->count();
            
            $labels[] = $label;
            $clientsData[] = $periodClients;
            
            $currentDate = $nextDate;
        }
        
        return [
            'labels' => $labels,
            'data' => $clientsData
        ];
    }
}
