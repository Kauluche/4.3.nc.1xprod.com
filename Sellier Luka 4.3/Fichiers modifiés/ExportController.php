<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Zone;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ExportController extends Controller
{
    /**
     * Export les ventes d'un commercial sur une période donnée
     */
    public function exportCommercialSales(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Handle period_type if provided
        if ($request->has('period_type') && !$request->has('start_date')) {
            $this->setPeriodDates($request->input('period_type'), $startDate, $endDate);
        }

        $pharmacyIds = $user->pharmacies()->pluck('id')->toArray();
        
        $sales = Order::whereIn('pharmacy_id', $pharmacyIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['pharmacy', 'items'])
            ->get();
        
        $filename = 'ventes_' . $user->first_name . '_' . $user->last_name . '_' . Carbon::now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $columns = ['ID', 'Date', 'Pharmacie', 'Statut', 'Montant Total', 'Produits'];
        
        $callback = function() use ($sales, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($sales as $sale) {
                $products = [];
                foreach ($sale->items as $item) {
                    $products[] = $item->quantity . 'x ' . $item->product_name;
                }
                
                $row = [
                    $sale->id,
                    $sale->created_at->format('d/m/Y'),
                    $sale->pharmacy->name,
                    $sale->status,
                    number_format($sale->total, 2, ',', ' ') . ' €',
                    implode(', ', $products)
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
    
    /**
     * Export les clients rapportés par un commercial sur une période donnée
     */
    public function exportCommercialClients(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Handle period_type if provided
        if ($request->has('period_type') && !$request->has('start_date')) {
            $this->setPeriodDates($request->input('period_type'), $startDate, $endDate);
        }

        $clients = Pharmacy::where('commercial_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('zone')
            ->get();
        
        $filename = 'clients_' . $user->first_name . '_' . $user->last_name . '_' . Carbon::now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $columns = ['ID', 'Nom', 'Adresse', 'Ville', 'Code Postal', 'Zone', 'Statut', 'Date d\'ajout'];
        
        $callback = function() use ($clients, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($clients as $client) {
                $row = [
                    $client->id,
                    $client->name,
                    $client->address,
                    $client->city,
                    $client->postal_code,
                    $client->zone ? $client->zone->name : 'Non assigné',
                    $client->status,
                    $client->created_at->format('d/m/Y')
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
    
    /**
     * Export les dernières ventes d'un commercial sur une période donnée
     */
    public function exportCommercialRecentSales(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Handle period_type if provided
        if ($request->has('period_type') && !$request->has('start_date')) {
            $this->setPeriodDates($request->input('period_type'), $startDate, $endDate);
        }

        $pharmacyIds = $user->pharmacies()->pluck('id')->toArray();
        
        $sales = Order::whereIn('pharmacy_id', $pharmacyIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['pharmacy', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $filename = 'dernieres_ventes_' . $user->first_name . '_' . $user->last_name . '_' . Carbon::now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $columns = ['ID', 'Date', 'Pharmacie', 'Statut', 'Montant Total', 'Produits'];
        
        $callback = function() use ($sales, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($sales as $sale) {
                $products = [];
                foreach ($sale->items as $item) {
                    $products[] = $item->quantity . 'x ' . $item->product_name;
                }
                
                $row = [
                    $sale->id,
                    $sale->created_at->format('d/m/Y'),
                    $sale->pharmacy->name,
                    $sale->status,
                    number_format($sale->total, 2, ',', ' ') . ' €',
                    implode(', ', $products)
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
    
    /**
     * Export toutes les pharmacies par zone
     */
    public function exportAllPharmaciesByZone()
    {
        $zones = Zone::with('pharmacies')->get();
        
        $filename = 'pharmacies_par_zone_' . Carbon::now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $columns = ['ID Zone', 'Nom Zone', 'Description Zone', 'Nombre Total de Pharmacies', 'ID Pharmacie', 'Nom Pharmacie', 'Email', 'Téléphone', 'Adresse', 'Ville', 'Code Postal', 'Pays', 'Statut', 'Objectif Mensuel (€)', 'Commercial Assigné', 'Nombre de Commandes', 'Montant Total des Commandes (€)'];
        
        $callback = function() use ($zones, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($zones as $zone) {
                // Calculer le nombre total de pharmacies dans la zone
                $totalPharmacies = $zone->pharmacies->count();
                
                foreach ($zone->pharmacies as $pharmacy) {
                    // Calculer le nombre de commandes et le montant total pour cette pharmacie
                    $orderCount = $pharmacy->orders()->count();
                    $orderTotal = $pharmacy->orders()
                        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                        ->sum(\DB::raw('order_items.quantity * order_items.unit_price * (1 - order_items.discount_percentage / 100)'));
                    
                    // Récupérer le nom du commercial assigné
                    $commercialName = $pharmacy->commercial ? $pharmacy->commercial->first_name . ' ' . $pharmacy->commercial->last_name : 'Non assigné';
                    
                    $row = [
                        $zone->id,
                        $zone->name,
                        $zone->description ?? 'Aucune description',
                        $totalPharmacies,
                        $pharmacy->id,
                        $pharmacy->name,
                        $pharmacy->email,
                        $pharmacy->phone,
                        $pharmacy->address,
                        $pharmacy->city,
                        $pharmacy->postal_code,
                        $pharmacy->country,
                        $pharmacy->status,
                        number_format($pharmacy->monthly_goal, 2, ',', ' '),
                        $commercialName,
                        $orderCount,
                        number_format($orderTotal, 2, ',', ' ')
                    ];
                    
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
    
    /**
     * Export les pharmacies d'une zone spécifique
     */
    public function exportPharmaciesByZone($zoneId)
    {
        $zone = Zone::with('pharmacies')->findOrFail($zoneId);
        
        $filename = 'pharmacies_zone_' . $zone->name . '_' . Carbon::now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $columns = ['ID Zone', 'Nom Zone', 'Description Zone', 'Nombre Total de Pharmacies', 'ID Pharmacie', 'Nom Pharmacie', 'Email', 'Téléphone', 'Adresse', 'Ville', 'Code Postal', 'Pays', 'Statut', 'Objectif Mensuel (€)', 'Commercial Assigné', 'Nombre de Commandes', 'Montant Total des Commandes (€)'];
        
        $callback = function() use ($zone, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Calculer le nombre total de pharmacies dans la zone
            $totalPharmacies = $zone->pharmacies->count();
            
            foreach ($zone->pharmacies as $pharmacy) {
                // Calculer le nombre de commandes et le montant total pour cette pharmacie
                $orderCount = $pharmacy->orders()->count();
                $orderTotal = $pharmacy->orders()
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->sum(\DB::raw('order_items.quantity * order_items.unit_price * (1 - order_items.discount_percentage / 100)'));
                
                // Récupérer le nom du commercial assigné
                $commercialName = $pharmacy->commercial ? $pharmacy->commercial->first_name . ' ' . $pharmacy->commercial->last_name : 'Non assigné';
                
                $row = [
                    $zone->id,
                    $zone->name,
                    $zone->description ?? 'Aucune description',
                    $totalPharmacies,
                    $pharmacy->id,
                    $pharmacy->name,
                    $pharmacy->email,
                    $pharmacy->phone,
                    $pharmacy->address,
                    $pharmacy->city,
                    $pharmacy->postal_code,
                    $pharmacy->country,
                    $pharmacy->status,
                    number_format($pharmacy->monthly_goal, 2, ',', ' '),
                    $commercialName,
                    $orderCount,
                    number_format($orderTotal, 2, ',', ' ')
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
    
    /**
     * Export les performances de tous les commerciaux
     */
    public function exportAllCommercialsPerformance()
    {
        $commercials = User::where('role', 'commercial')
            ->withCount('pharmacies')
            ->with('zone')
            ->get();
        
        $filename = 'performances_commerciaux_' . Carbon::now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $columns = ['ID', 'Nom', 'Prénom', 'Email', 'Téléphone', 'Date d\'embauche', 'Zone affectée', 'Nombre de pharmacies', 'Nombre de commandes', 'Total rapporté (€)', 'Moyenne par commande (€)', 'Objectif mensuel total (€)', 'Performance (%)', 'Nombre de clients actifs'];
        
        $callback = function() use ($commercials, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($commercials as $commercial) {
                // Calculer le total rapporté par le commercial
                $pharmacyIds = $commercial->pharmacies()->pluck('id')->toArray();
                
                // Obtenir toutes les commandes pour calculer différentes métriques
                $orders = Order::whereIn('pharmacy_id', $pharmacyIds)
                    ->orWhere('commercial_id', $commercial->id)
                    ->get();
                
                $orderCount = $orders->count();
                
                // Calculer le montant total des commandes
                $totalAmount = 0;
                foreach ($orders as $order) {
                    $orderTotal = $order->items->sum(function($item) {
                        return $item->quantity * $item->unit_price * (1 - $item->discount_percentage / 100);
                    });
                    $totalAmount += $orderTotal;
                }
                
                // Calculer la moyenne par commande
                $averageOrderAmount = $orderCount > 0 ? $totalAmount / $orderCount : 0;
                
                // Calculer l'objectif mensuel total (somme des objectifs de toutes les pharmacies assignées)
                $totalMonthlyGoal = $commercial->pharmacies()->sum('monthly_goal');
                
                // Calculer la performance (total rapporté / objectif mensuel * 100)
                $performance = $totalMonthlyGoal > 0 ? ($totalAmount / $totalMonthlyGoal) * 100 : 0;
                
                // Nombre de clients actifs (pharmacies avec au moins une commande)
                $activeClients = $commercial->pharmacies()
                    ->whereHas('orders')
                    ->count();
                
                // Récupérer la zone affectée au commercial
                $zoneName = $commercial->zone ? $commercial->zone->name : 'Non assigné';
                
                $row = [
                    $commercial->id,
                    $commercial->last_name,
                    $commercial->first_name,
                    $commercial->email,
                    $commercial->phone ?? 'Non renseigné',
                    $commercial->hire_date ? $commercial->hire_date->format('d/m/Y') : 'Non renseigné',
                    $zoneName,
                    $commercial->pharmacies_count,
                    $orderCount,
                    number_format($totalAmount, 2, ',', ' '),
                    number_format($averageOrderAmount, 2, ',', ' '),
                    number_format($totalMonthlyGoal, 2, ',', ' '),
                    number_format($performance, 2, ',', ' ') . '%',
                    $activeClients
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
    
    /**
     * Export les performances d'un commercial spécifique
     */
    public function exportCommercialPerformance($commercialId)
    {
        $commercial = User::where('role', 'commercial')
            ->withCount('pharmacies')
            ->with('zone')
            ->findOrFail($commercialId);
        
        $filename = 'performance_' . $commercial->first_name . '_' . $commercial->last_name . '_' . Carbon::now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $columns = ['Nom', 'Prénom', 'Email', 'Téléphone', 'Date d\'embauche', 'Zone affectée', 'Nombre de pharmacies', 'Nombre de commandes', 'Total rapporté (€)', 'Moyenne par commande (€)', 'Objectif mensuel total (€)', 'Performance (%)', 'Nombre de clients actifs', 'Liste des pharmacies'];
        
        $callback = function() use ($commercial, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Calculer le total rapporté par le commercial
            $pharmacyIds = $commercial->pharmacies()->pluck('id')->toArray();
            
            // Obtenir toutes les commandes pour calculer différentes métriques
            $orders = Order::whereIn('pharmacy_id', $pharmacyIds)
                ->orWhere('commercial_id', $commercial->id)
                ->get();
            
            $orderCount = $orders->count();
            
            // Calculer le montant total des commandes
            $totalAmount = 0;
            foreach ($orders as $order) {
                $orderTotal = $order->items->sum(function($item) {
                    return $item->quantity * $item->unit_price * (1 - $item->discount_percentage / 100);
                });
                $totalAmount += $orderTotal;
            }
            
            // Calculer la moyenne par commande
            $averageOrderAmount = $orderCount > 0 ? $totalAmount / $orderCount : 0;
            
            // Calculer l'objectif mensuel total (somme des objectifs de toutes les pharmacies assignées)
            $totalMonthlyGoal = $commercial->pharmacies()->sum('monthly_goal');
            
            // Calculer la performance (total rapporté / objectif mensuel * 100)
            $performance = $totalMonthlyGoal > 0 ? ($totalAmount / $totalMonthlyGoal) * 100 : 0;
            
            // Nombre de clients actifs (pharmacies avec au moins une commande)
            $activeClients = $commercial->pharmacies()
                ->whereHas('orders')
                ->count();
            
            // Récupérer la zone affectée au commercial
            $zoneName = $commercial->zone ? $commercial->zone->name : 'Non assigné';
            
            // Liste des pharmacies assignées au commercial
            $pharmaciesList = $commercial->pharmacies()->pluck('name')->implode(', ');
            
            $row = [
                $commercial->last_name,
                $commercial->first_name,
                $commercial->email,
                $commercial->phone ?? 'Non renseigné',
                $commercial->hire_date ? $commercial->hire_date->format('d/m/Y') : 'Non renseigné',
                $zoneName,
                $commercial->pharmacies_count,
                $orderCount,
                number_format($totalAmount, 2, ',', ' '),
                number_format($averageOrderAmount, 2, ',', ' '),
                number_format($totalMonthlyGoal, 2, ',', ' '),
                number_format($performance, 2, ',', ' ') . '%',
                $activeClients,
                $pharmaciesList
            ];
            
            fputcsv($file, $row);
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
    
    /**
     * Helper method to set start and end dates based on period type
     *
     * @param string $periodType
     * @param string &$startDate
     * @param string &$endDate
     * @return void
     */
    private function setPeriodDates($periodType, &$startDate, &$endDate)
    {
        switch ($periodType) {
            case 'last30days':
                $startDate = Carbon::now()->subDays(30)->startOfDay()->format('Y-m-d');
                $endDate = Carbon::now()->endOfDay()->format('Y-m-d');
                break;
            case 'last3months':
                $startDate = Carbon::now()->subMonths(2)->startOfMonth()->format('Y-m-d');
                $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last6months':
                $startDate = Carbon::now()->subMonths(5)->startOfMonth()->format('Y-m-d');
                $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'lastyear':
                $startDate = Carbon::now()->subMonths(11)->startOfMonth()->format('Y-m-d');
                $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            default:
                $startDate = Carbon::now()->subMonth()->format('Y-m-d');
                $endDate = Carbon::now()->format('Y-m-d');
        }
    }
}
