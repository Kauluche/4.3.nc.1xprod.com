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
    public function index(Request $request)
    {
        $user = auth()->user();
        $data = [];
        
        // Récupérer les paramètres de période ou utiliser les valeurs par défaut
        $periodType = $request->input('period_type', 'last6months');
        $startDate = null;
        $endDate = null;
        
        switch ($periodType) {
            case 'last30days':
                $startDate = Carbon::now()->subDays(30)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'last3months':
                $startDate = Carbon::now()->subMonths(3)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'last6months':
                $startDate = Carbon::now()->subMonths(5)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'lastyear':
                $startDate = Carbon::now()->subMonths(11)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                $customStartDate = $request->input('start_date');
                $customEndDate = $request->input('end_date');
                
                if ($customStartDate && $customEndDate) {
                    $startDate = Carbon::createFromFormat('Y-m-d', $customStartDate)->startOfDay();
                    $endDate = Carbon::createFromFormat('Y-m-d', $customEndDate)->endOfDay();
                } else {
                    $startDate = Carbon::now()->subMonths(5)->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                }
                break;
            default:
                $startDate = Carbon::now()->subMonths(5)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }

        if ($user->isAdmin()) {
            // Récupérer les notifications de suppression de pharmacie non lues
            $pharmacyDeletionRequests = $user->notifications()
                ->where('type', 'pharmacy_deletion_request')
                ->whereNull('read_at')
                ->latest()
                ->get();

            $data = [
                'total_pharmacies' => Pharmacy::count(),
                'total_orders' => Order::count(),
                'total_documents' => Document::count(),
                'recent_orders' => Order::with(['pharmacy', 'items'])->latest()->take(5)->get(),
                'recent_documents' => Document::latest()->take(5)->get(),
                'pharmacy_deletion_requests' => $pharmacyDeletionRequests,
            ];
        } elseif ($user->isCommercial()) {
            // Récupérer les pharmacies du commercial
            $pharmacies = $user->pharmacies();
            
            // Statistiques des pharmacies
            $totalPharmacies = $pharmacies->count();
            $clientPharmacies = $pharmacies->where('status', 'client')->count();
            $prospectPharmacies = $totalPharmacies - $clientPharmacies;
            
            // Statistiques des commandes avec les totaux calculés correctement
            $pharmacyIds = $pharmacies->pluck('id')->toArray();
            
            $orders = Order::whereIn('pharmacy_id', $pharmacyIds);
            $totalOrders = $orders->count();
            
            // Calculer le total des commandes en incluant les items
            $totalOrdersAmount = Order::whereIn('pharmacy_id', $pharmacyIds)
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->select(DB::raw('SUM(order_items.quantity * order_items.unit_price * (1 - order_items.discount_percentage / 100)) as total_amount'))
                ->first()
                ->total_amount ?? 0;
            
            // Statistiques mensuelles
            $currentMonth = Carbon::now()->month;
            $monthlyOrders = $orders->whereMonth('created_at', $currentMonth)->count();
            
            $monthlyAmount = Order::whereIn('pharmacy_id', $pharmacyIds)
                ->whereMonth('orders.created_at', $currentMonth)
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->select(DB::raw('SUM(order_items.quantity * order_items.unit_price * (1 - order_items.discount_percentage / 100)) as total_amount'))
                ->first()
                ->total_amount ?? 0;
            
            // Calculer le taux de conversion
            $conversionRate = $totalPharmacies > 0 ? round(($clientPharmacies / $totalPharmacies) * 100, 2) : 0;
            
            // Calculer le panier moyen
            $averageCart = $totalOrders > 0 ? round($totalOrdersAmount / $totalOrders, 2) : 0;
            
            // Récupérer les commandes récentes avec les détails de la pharmacie et les items
            $recentOrders = Order::with(['pharmacy', 'items'])
                ->whereIn('pharmacy_id', $pharmacyIds)
                ->latest()
                ->take(5)
                ->get();
            
            // Récupérer les documents récents
            $recentDocuments = $user->documents()
                ->latest()
                ->take(5)
                ->get();
            
            // Récupérer les pharmacies récentes
            $recentPharmacies = $pharmacies->latest()->take(5)->get();
            
            // Préparer les données pour le graphique des ventes
            $salesChartData = $this->prepareSalesChartData($user, $startDate, $endDate);
            
            // Préparer les données pour le graphique des clients rapportés
            $clientsChartData = $this->prepareClientsChartData($user, $startDate, $endDate);
            
            $data = [
                // Données de période pour les graphiques
                'period_type' => $periodType,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                
                // Statistiques générales
                'total_pharmacies' => $totalPharmacies,
                'client_pharmacies' => $clientPharmacies,
                'prospect_pharmacies' => $prospectPharmacies,
                'total_orders' => $totalOrders,
                'total_orders_amount' => $totalOrdersAmount,
                'monthly_orders' => $monthlyOrders,
                'monthly_amount' => $monthlyAmount,
                'conversion_rate' => $conversionRate,
                'average_cart' => $averageCart,
                'recent_orders' => $recentOrders,
                'recent_documents' => $recentDocuments,
                'recent_pharmacies' => $recentPharmacies,
                'sales_chart_data' => $salesChartData,
                'clients_chart_data' => $clientsChartData,
            ];
        }

        if (auth()->user()->role === 'admin') {
            $notifications = Notification::where('notifiable_id', auth()->id())
                ->where('notifiable_type', User::class)
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $notifications = collect();
        }

        $data['notifications'] = $notifications;

        return view('dashboard', $data);
    }

    private function calculateConversionRate($user)
    {
        $totalPharmacies = $user->pharmacies->count();
        $clientPharmacies = $user->pharmacies->where('status', 'client')->count();
        
        if ($totalPharmacies === 0) {
            return 0;
        }
        
        return round(($clientPharmacies / $totalPharmacies) * 100, 2);
    }

    private function calculateAverageCart($user)
    {
        $orders = Order::whereIn('pharmacy_id', $user->pharmacies->pluck('id'))->get();
        
        if ($orders->isEmpty()) {
            return 0;
        }
        
        return round($orders->avg('total'), 2);
    }

    private function countTotalDocuments()
    {
        return Document::count();
    }
    
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
        } else if ($diffInDays <= 90) {
            // Période moyenne (1-3 mois) : afficher par semaine
            $interval = 'week';
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
        } else if ($diffInDays <= 90) {
            // Période moyenne (1-3 mois) : afficher par semaine
            $interval = 'week';
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