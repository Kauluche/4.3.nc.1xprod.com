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
        
        // Préparer les données pour le graphique des pharmacies par zone
        $zones_chart_data = [
            'labels' => [],
            'data' => [],
            'colors' => []
        ];
        
        foreach ($stats['pharmacies_by_zone'] as $zone) {
            $zones_chart_data['labels'][] = $zone->name;
            $zones_chart_data['data'][] = $zone->pharmacies_count;
            // Générer une couleur aléatoire pour chaque zone
            $zones_chart_data['colors'][] = 'rgba(' . rand(0, 200) . ', ' . rand(0, 200) . ', ' . rand(0, 200) . ', 0.7)';
        }
        
        // Préparer les données pour le graphique des performances des commerciaux
        $commercials_chart_data = [
            'labels' => [],
            'data' => [],
            'colors' => []
        ];
        
        foreach ($stats['commercial_performance'] as $commercial) {
            $commercials_chart_data['labels'][] = $commercial->first_name . ' ' . $commercial->last_name;
            $commercials_chart_data['data'][] = $commercial->pharmacies_count;
            // Générer une couleur aléatoire pour chaque commercial
            $commercials_chart_data['colors'][] = 'rgba(' . rand(0, 200) . ', ' . rand(0, 200) . ', ' . rand(0, 200) . ', 0.7)';
        }

        return view('admin.reports.index', compact('stats', 'zones_chart_data', 'commercials_chart_data'));
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