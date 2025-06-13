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
        
        $columns = ['ID Zone', 'Nom Zone', 'ID Pharmacie', 'Nom Pharmacie', 'Adresse', 'Ville', 'Code Postal', 'Statut'];
        
        $callback = function() use ($zones, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($zones as $zone) {
                foreach ($zone->pharmacies as $pharmacy) {
                    $row = [
                        $zone->id,
                        $zone->name,
                        $pharmacy->id,
                        $pharmacy->name,
                        $pharmacy->address,
                        $pharmacy->city,
                        $pharmacy->postal_code,
                        $pharmacy->status
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
        
        $columns = ['ID Pharmacie', 'Nom Pharmacie', 'Adresse', 'Ville', 'Code Postal', 'Statut'];
        
        $callback = function() use ($zone, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($zone->pharmacies as $pharmacy) {
                $row = [
                    $pharmacy->id,
                    $pharmacy->name,
                    $pharmacy->address,
                    $pharmacy->city,
                    $pharmacy->postal_code,
                    $pharmacy->status
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
            ->with('zones')
            ->get();
        
        $filename = 'performances_commerciaux_' . Carbon::now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $columns = ['ID', 'Nom', 'Prénom', 'Email', 'Zones affectées', 'Nombre de pharmacies', 'Total rapporté (€)'];
        
        $callback = function() use ($commercials, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($commercials as $commercial) {
                // Calculer le total rapporté par le commercial
                $pharmacyIds = $commercial->pharmacies()->pluck('id')->toArray();
                $totalAmount = Order::whereIn('pharmacy_id', $pharmacyIds)
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->sum(\DB::raw('order_items.quantity * order_items.unit_price * (1 - order_items.discount_percentage / 100)'));
                
                // Récupérer les zones affectées au commercial
                $zones = $commercial->zones->pluck('name')->implode(', ');
                
                $row = [
                    $commercial->id,
                    $commercial->last_name,
                    $commercial->first_name,
                    $commercial->email,
                    $zones,
                    $commercial->pharmacies_count,
                    number_format($totalAmount, 2, ',', ' ')
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
            ->with('zones')
            ->findOrFail($commercialId);
        
        $filename = 'performance_' . $commercial->first_name . '_' . $commercial->last_name . '_' . Carbon::now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $columns = ['Nom', 'Prénom', 'Email', 'Zones affectées', 'Nombre de pharmacies', 'Total rapporté (€)'];
        
        $callback = function() use ($commercial, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Calculer le total rapporté par le commercial
            $pharmacyIds = $commercial->pharmacies()->pluck('id')->toArray();
            $totalAmount = Order::whereIn('pharmacy_id', $pharmacyIds)
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->sum(\DB::raw('order_items.quantity * order_items.unit_price * (1 - order_items.discount_percentage / 100)'));
            
            // Récupérer les zones affectées au commercial
            $zones = $commercial->zones->pluck('name')->implode(', ');
            
            $row = [
                $commercial->last_name,
                $commercial->first_name,
                $commercial->email,
                $zones,
                $commercial->pharmacies_count,
                number_format($totalAmount, 2, ',', ' ')
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
