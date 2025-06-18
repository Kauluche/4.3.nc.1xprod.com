<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class ReportController extends Controller
{
    public function index()
    {
        // Statistiques de base
        $stats = [
            'total_orders' => Order::count(),
            'total_pharmacies' => Pharmacy::count(),
            'total_commercials' => User::where('role', 'commercial')->count(),
            'total_zones' => Zone::count(),
            'pharmacies_by_zone' => Zone::withCount('pharmacies')->get(),
            'commercial_performance' => User::where('role', 'commercial')
                ->withCount('pharmacies')
                ->get(),
        ];
        
        // Statistiques des zones
        // 1. Zone qui rapporte le plus
        $topRevenueZone = Zone::select('zones.id', 'zones.name', DB::raw('SUM(order_items.quantity * order_items.unit_price * (1 - order_items.discount_percentage / 100)) as total_revenue'))
            ->join('pharmacies', 'zones.id', '=', 'pharmacies.zone_id')
            ->join('orders', 'pharmacies.id', '=', 'orders.pharmacy_id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->groupBy('zones.id', 'zones.name')
            ->orderBy('total_revenue', 'desc')
            ->first();
        
        // 2. Zone avec le plus de commandes
        $topOrdersZone = Zone::select('zones.id', 'zones.name', DB::raw('COUNT(orders.id) as orders_count'))
            ->join('pharmacies', 'zones.id', '=', 'pharmacies.zone_id')
            ->join('orders', 'pharmacies.id', '=', 'orders.pharmacy_id')
            ->groupBy('zones.id', 'zones.name')
            ->orderBy('orders_count', 'desc')
            ->first();
        
        // 3. Zone avec le plus de pharmacies (déjà disponible dans pharmacies_by_zone)
        $topPharmaciesZone = Zone::withCount('pharmacies')
            ->orderBy('pharmacies_count', 'desc')
            ->first();
        
        $stats['top_zones'] = [
            'revenue' => $topRevenueZone ? [
                'name' => $topRevenueZone->name,
                'value' => $topRevenueZone->total_revenue,
                'formatted_value' => number_format($topRevenueZone->total_revenue, 2, ',', ' ') . ' €'
            ] : null,
            'orders' => $topOrdersZone ? [
                'name' => $topOrdersZone->name,
                'value' => $topOrdersZone->orders_count,
                'formatted_value' => $topOrdersZone->orders_count
            ] : null,
            'pharmacies' => $topPharmaciesZone ? [
                'name' => $topPharmaciesZone->name,
                'value' => $topPharmaciesZone->pharmacies_count,
                'formatted_value' => $topPharmaciesZone->pharmacies_count
            ] : null
        ];
        
        // Statistiques des commerciaux
        // 1. Commercial qui rapporte le plus
        $topRevenueCommercial = User::select('users.id', 'users.first_name', 'users.last_name', DB::raw('SUM(order_items.quantity * order_items.unit_price * (1 - order_items.discount_percentage / 100)) as total_revenue'))
            ->where('users.role', 'commercial')
            ->join('orders', 'users.id', '=', 'orders.commercial_id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderBy('total_revenue', 'desc')
            ->first();
        
        // 2. Commercial avec le plus de commandes
        $topOrdersCommercial = User::select('users.id', 'users.first_name', 'users.last_name', DB::raw('COUNT(orders.id) as orders_count'))
            ->where('users.role', 'commercial')
            ->join('orders', 'users.id', '=', 'orders.commercial_id')
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderBy('orders_count', 'desc')
            ->first();
        
        // 3. Commercial avec le plus de clients (pharmacies avec statut 'client')
        $topClientsCommercial = User::select('users.id', 'users.first_name', 'users.last_name', DB::raw('COUNT(pharmacies.id) as clients_count'))
            ->where('users.role', 'commercial')
            ->join('pharmacies', 'users.id', '=', 'pharmacies.commercial_id')
            ->where('pharmacies.status', 'client') // Uniquement les clients, pas les prospects
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderBy('clients_count', 'desc')
            ->first();
        
        $stats['top_commercials'] = [
            'revenue' => $topRevenueCommercial ? [
                'name' => $topRevenueCommercial->first_name . ' ' . $topRevenueCommercial->last_name,
                'value' => $topRevenueCommercial->total_revenue,
                'formatted_value' => number_format($topRevenueCommercial->total_revenue, 2, ',', ' ') . ' €'
            ] : null,
            'orders' => $topOrdersCommercial ? [
                'name' => $topOrdersCommercial->first_name . ' ' . $topOrdersCommercial->last_name,
                'value' => $topOrdersCommercial->orders_count,
                'formatted_value' => $topOrdersCommercial->orders_count
            ] : null,
            'clients' => $topClientsCommercial ? [
                'name' => $topClientsCommercial->first_name . ' ' . $topClientsCommercial->last_name,
                'value' => $topClientsCommercial->clients_count,
                'formatted_value' => $topClientsCommercial->clients_count
            ] : null
        ];
        
        // Préparer les données pour le graphique des pharmacies par zone
        $zones_chart_data = [
            'labels' => [],
            'data' => [],
            'colors' => []
        ];
        
        // Préparer les données pour le graphique des commandes par zone
        $zones_orders_chart_data = [
            'labels' => [],
            'data' => [],
            'colors' => []
        ];
        
        // Récupérer le nombre de commandes par zone
        $zonesWithOrderCounts = Zone::select('zones.id', 'zones.name', DB::raw('COUNT(orders.id) as orders_count'))
            ->leftJoin('pharmacies', 'zones.id', '=', 'pharmacies.zone_id')
            ->leftJoin('orders', 'pharmacies.id', '=', 'orders.pharmacy_id')
            ->groupBy('zones.id', 'zones.name')
            ->orderBy('zones.name')
            ->get();
            
        foreach ($stats['pharmacies_by_zone'] as $zone) {
            $zones_chart_data['labels'][] = $zone->name;
            $zones_chart_data['data'][] = $zone->pharmacies_count;
            // Générer une couleur aléatoire pour chaque zone
            $color = 'rgba(' . rand(0, 200) . ', ' . rand(0, 200) . ', ' . rand(0, 200) . ', 0.7)';
            $zones_chart_data['colors'][] = $color;
        }
        
        foreach ($zonesWithOrderCounts as $zone) {
            $zones_orders_chart_data['labels'][] = $zone->name;
            $zones_orders_chart_data['data'][] = $zone->orders_count;
            // Générer une couleur aléatoire pour chaque zone
            $zones_orders_chart_data['colors'][] = 'rgba(' . rand(0, 200) . ', ' . rand(0, 200) . ', ' . rand(0, 200) . ', 0.7)';
        }
        
        // Préparer les données pour le graphique des performances des commerciaux
        $commercials_chart_data = [
            'labels' => [],
            'data' => [],
            'colors' => []
        ];
        
        // Préparer les données pour le graphique des commandes par commercial
        $commercials_orders_chart_data = [
            'labels' => [],
            'data' => [],
            'colors' => []
        ];
        
        // Récupérer le nombre de commandes par commercial
        $commercialsWithOrderCounts = User::select('users.id', DB::raw("CONCAT(users.first_name, ' ', users.last_name) as name"), DB::raw('COUNT(orders.id) as orders_count'))
            ->where('users.role', 'commercial')
            ->leftJoin('orders', 'users.id', '=', 'orders.commercial_id')
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderBy('name')
            ->get();
        
        foreach ($stats['commercial_performance'] as $commercial) {
            $commercials_chart_data['labels'][] = $commercial->first_name . ' ' . $commercial->last_name;
            $commercials_chart_data['data'][] = $commercial->pharmacies_count;
            // Générer une couleur aléatoire pour chaque commercial
            $color = 'rgba(' . rand(0, 200) . ', ' . rand(0, 200) . ', ' . rand(0, 200) . ', 0.7)';
            $commercials_chart_data['colors'][] = $color;
        }
        
        foreach ($commercialsWithOrderCounts as $commercial) {
            $commercials_orders_chart_data['labels'][] = $commercial->name;
            $commercials_orders_chart_data['data'][] = $commercial->orders_count;
            // Générer une couleur aléatoire pour chaque commercial
            $commercials_orders_chart_data['colors'][] = 'rgba(' . rand(0, 200) . ', ' . rand(0, 200) . ', ' . rand(0, 200) . ', 0.7)';
        }

        return view('admin.reports.index', compact('stats', 'zones_chart_data', 'zones_orders_chart_data', 'commercials_chart_data', 'commercials_orders_chart_data'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:pharmacies,commercials,zones',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $report = $this->generateReport($validated);

        return view('admin.reports.show', compact('report'));
    }

    private function generateReport($data)
    {
        switch ($data['report_type']) {
            case 'pharmacies':
                return $this->generatePharmaciesReport($data);
            case 'commercials':
                return $this->generateCommercialsReport($data);
            case 'zones':
                return $this->generateZonesReport($data);
            default:
                return null;
        }
    }

    private function generatePharmaciesReport($data)
    {
        $query = Pharmacy::query();

        if ($data['start_date']) {
            $query->where('created_at', '>=', $data['start_date']);
        }

        if ($data['end_date']) {
            $query->where('created_at', '<=', $data['end_date']);
        }

        return $query->with(['zone', 'commercial'])
            ->get()
            ->groupBy('zone.name');
    }

    private function generateCommercialsReport($data)
    {
        $query = User::where('role', 'commercial')
            ->withCount('pharmacies');

        if ($data['start_date']) {
            $query->whereHas('pharmacies', function ($q) use ($data) {
                $q->where('created_at', '>=', $data['start_date']);
            });
        }

        if ($data['end_date']) {
            $query->whereHas('pharmacies', function ($q) use ($data) {
                $q->where('created_at', '<=', $data['end_date']);
            });
        }

        return $query->get();
    }

    private function generateZonesReport($data)
    {
        $query = Zone::withCount('pharmacies')
            ->with(['commercial', 'pharmacies']);

        if ($data['start_date']) {
            $query->whereHas('pharmacies', function ($q) use ($data) {
                $q->where('created_at', '>=', $data['start_date']);
            });
        }

        if ($data['end_date']) {
            $query->whereHas('pharmacies', function ($q) use ($data) {
                $q->where('created_at', '<=', $data['end_date']);
            });
        }

        return $query->get();
    }
} 