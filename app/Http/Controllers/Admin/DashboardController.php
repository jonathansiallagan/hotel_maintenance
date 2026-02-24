<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Asset;
use App\Models\User;
use App\Models\Sparepart;
use App\Models\Location;
use \Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // =========================================================
        // 1. AMBIL STATISTIK RINGKAS (KODE LAMA)
        // =========================================================
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'open')->count();
        $processTickets = Ticket::whereIn('status', ['in_progress', 'pending_sparepart'])->count();
        $doneTickets = Ticket::where('status', 'resolved')->count();

        $recentTickets = Ticket::with(['user', 'technician', 'asset.location'])
            ->latest()
            ->take(5)
            ->get();

        $totalAssets = Asset::count();
        $totalTechnicians = User::where('role', 'technician')->count();
        $totalUsers = User::count();


        // =========================================================
        // 2. TAMBAHAN LOGIKA RCA ANALYTICS (DASHBOARD CERDAS)
        // =========================================================

        $filterMonth = $request->input('month', 'all');
        $filterLocation = $request->input('location_id', 'all');

        $rcaQuery = Ticket::where('status', 'resolved')->whereNotNull('root_cause');

        if ($filterMonth != 'all') {
            $rcaQuery->whereMonth('completed_at', $filterMonth)
                ->whereYear('completed_at', date('Y'));
        }

        if ($filterLocation != 'all') {
            $rcaQuery->whereHas('asset', function ($q) use ($filterLocation) {
                $q->where('location_id', $filterLocation);
            });
        }

        $rcaTickets = $rcaQuery->with('asset.category')->get();

        $rcaData = [];
        foreach ($rcaTickets as $ticket) {
            $catName = $ticket->asset->category->name ?? 'Tanpa Kategori';
            $cause = $ticket->root_cause;

            if (!isset($rcaData[$catName])) $rcaData[$catName] = [];
            if (!isset($rcaData[$catName][$cause])) $rcaData[$catName][$cause] = 0;

            $rcaData[$catName][$cause]++;
        }

        $topRcaData = collect($rcaData)->sortByDesc(function ($causes) {
            return array_sum($causes);
        })->take(3)->toArray();

        $locations = Location::all();


        // =========================================================
        // 3. KIRIM SEMUA DATA KE VIEW
        // =========================================================
        return view('Admin.dashboard', compact(
            // Data Lama
            'totalTickets',
            'openTickets',
            'processTickets',
            'doneTickets',
            'recentTickets',
            'totalAssets',
            'totalTechnicians',
            'totalUsers',
            // Data Baru (RCA)
            'rcaData',
            'topRcaData',
            'locations',
            'filterMonth',
            'filterLocation'
        ));
    }

    public function exportRca(Request $request)
    {
        $filterMonth = $request->input('month', 'all');
        $filterLocation = $request->input('location_id', 'all');

        $rcaQuery = Ticket::where('status', 'resolved')->whereNotNull('root_cause');

        if ($filterMonth != 'all') {
            $rcaQuery->whereMonth('completed_at', $filterMonth)
                ->whereYear('completed_at', date('Y'));
        }

        if ($filterLocation != 'all') {
            $rcaQuery->whereHas('asset', function ($q) use ($filterLocation) {
                $q->where('location_id', $filterLocation);
            });
        }

        $rcaTickets = $rcaQuery->with(['asset.category', 'asset.location'])->get();

        $rcaData = [];
        $totalRcaTickets = 0;
        foreach ($rcaTickets as $ticket) {
            $catName = $ticket->asset->category->name ?? 'Tanpa Kategori';
            $cause = $ticket->root_cause;

            if (!isset($rcaData[$catName])) $rcaData[$catName] = [];
            if (!isset($rcaData[$catName][$cause])) $rcaData[$catName][$cause] = 0;

            $rcaData[$catName][$cause]++;
            $totalRcaTickets++;
        }

        $monthLabel = 'Semua Bulan';
        if ($filterMonth != 'all') {
            $monthLabel = date('F Y', mktime(0, 0, 0, $filterMonth, 1));
        }

        $locationLabel = 'Semua Lokasi';
        if ($filterLocation != 'all') {
            $loc = Location::find($filterLocation);
            if ($loc) $locationLabel = $loc->name;
        }

        $pdf = Pdf::loadView('Admin.reports.rca_pdf', compact(
            'rcaData',
            'totalRcaTickets',
            'monthLabel',
            'locationLabel',
            'filterMonth',
            'filterLocation'
        ));

        $fileName = 'RCA_Report_' . str_replace(' ', '_', $monthLabel) . '.pdf';

        return $pdf->download($fileName);
    }

    public function notifications()
    {
        $urgentTickets = Ticket::where('status', 'open')
            ->where('priority', 'high')
            ->with(['user', 'asset'])
            ->latest()
            ->take(5)
            ->get();

        $pendingTickets = Ticket::where('status', 'pending_sparepart')
            ->with(['user', 'asset'])
            ->latest()
            ->take(5)
            ->get();

        $lowStockSpareparts = Sparepart::where('stock', '<=', 5)
            ->get();

        $notifications = [];

        foreach ($urgentTickets as $ticket) {
            $notifications[] = [
                'id' => $ticket->id,
                'type' => 'urgent_ticket',
                'title' => 'Urgent: ' . ($ticket->ticket_code ?? '#' . $ticket->id),
                'message' => 'Tiket prioritas tinggi: ' . substr($ticket->title, 0, 30) . '...',
                'url' => route('Admin.tickets.show', $ticket->id),
                'time' => $ticket->created_at->diffForHumans(),
                'icon' => 'fa-exclamation-triangle',
                'color' => 'text-red-600',
            ];
        }

        foreach ($pendingTickets as $ticket) {
            $notifications[] = [
                'id' => $ticket->id,
                'type' => 'pending_ticket',
                'title' => 'Pending Part: ' . ($ticket->ticket_code ?? '#' . $ticket->id),
                'message' => 'Menunggu sparepart: ' . substr($ticket->title, 0, 30) . '...',
                'url' => route('Admin.tickets.show', $ticket->id),
                'time' => $ticket->created_at->diffForHumans(),
                'icon' => 'fa-clock',
                'color' => 'text-yellow-600',
            ];
        }

        foreach ($lowStockSpareparts as $part) {
            $notifications[] = [
                'id' => 'sp_' . $part->id,
                'type' => 'low_stock',
                'title' => 'Stok Menipis: ' . $part->name,
                'message' => 'Sisa stok tinggal ' . $part->stock . ' ' . $part->unit . '. Segera restock!',
                'url' => route('admin.spareparts.index', ['search' => $part->sku_code]),
                'time' => 'Baru saja',
                'icon' => 'fa-box-open',
                'color' => 'text-orange-500',
            ];
        }

        return response()->json(['notifications' => $notifications]);
    }
}
