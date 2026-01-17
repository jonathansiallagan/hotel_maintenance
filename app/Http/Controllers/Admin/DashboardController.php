<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Asset;
use App\Models\User;
use App\Models\Sparepart;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Statistik Ringkas
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'open')->count();
        $processTickets = Ticket::whereIn('status', ['in_progress', 'pending_sparepart'])->count();
        $doneTickets = Ticket::where('status', 'resolved')->count();

        // 2. Ambil Tiket Terbaru (5 teratas)
        $recentTickets = Ticket::with(['user', 'technician', 'asset'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Statistik Aset & Teknisi
        $totalAssets = Asset::count();
        $totalTechnicians = User::where('role', 'technician')->count();
        $totalUsers = User::count();

        return view('admin.dashboard', compact(
            'totalTickets',
            'openTickets',
            'processTickets',
            'doneTickets',
            'recentTickets',
            'totalAssets',
            'totalTechnicians',
            'totalUsers'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // Search Tickets
        $tickets = Ticket::with(['user', 'asset'])
            ->where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('id', 'like', "%{$query}%")
            ->take(5)
            ->get();

        foreach ($tickets as $ticket) {
            $results[] = [
                'type' => 'ticket',
                'id' => $ticket->id,
                'title' => $ticket->title,
                'description' => substr($ticket->description, 0, 100) . '...',
                'url' => route('admin.tickets.show', $ticket->id),
                'status' => $ticket->status,
            ];
        }

        // Search Assets
        $assets = Asset::with(['location', 'category'])
            ->where('name', 'like', "%{$query}%")
            ->orWhere('serial_number', 'like', "%{$query}%")
            ->take(5)
            ->get();

        foreach ($assets as $asset) {
            $results[] = [
                'type' => 'asset',
                'id' => $asset->id,
                'title' => $asset->name,
                'description' => 'SN: ' . ($asset->serial_number ?? 'N/A') . ' | Lokasi: ' . ($asset->location->name ?? 'N/A'),
                'url' => route('admin.assets.show', $asset->id),
                'status' => $asset->status,
            ];
        }

        // Search Spareparts
        $spareparts = Sparepart::with('category')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('sku_code', 'like', "%{$query}%")
            ->take(5)
            ->get();

        foreach ($spareparts as $sparepart) {
            $results[] = [
                'type' => 'sparepart',
                'id' => $sparepart->id,
                'title' => $sparepart->name,
                'description' => 'SKU: ' . $sparepart->sku_code . ' | Stock: ' . $sparepart->stock,
                'url' => route('admin.spareparts.show', $sparepart->id),
                'status' => $sparepart->stock > 0 ? 'available' : 'out_of_stock',
            ];
        }

        return response()->json(['results' => $results]);
    }

    public function notifications()
    {
        // Get recent notifications - tickets that need attention
        $urgentTickets = Ticket::where('status', 'open')
            ->where('priority', 'high')
            ->with(['user', 'asset'])
            ->latest()
            ->take(10)
            ->get();

        $pendingTickets = Ticket::where('status', 'pending_sparepart')
            ->with(['user', 'asset'])
            ->latest()
            ->take(10)
            ->get();

        $notifications = [];

        foreach ($urgentTickets as $ticket) {
            $notifications[] = [
                'id' => $ticket->id,
                'type' => 'urgent_ticket',
                'title' => 'Tiket Urgent: ' . $ticket->title,
                'message' => 'Prioritas tinggi membutuhkan perhatian segera',
                'url' => route('admin.tickets.show', $ticket->id),
                'time' => $ticket->created_at->diffForHumans(),
                'icon' => 'fa-exclamation-triangle',
                'color' => 'text-red-600',
            ];
        }

        foreach ($pendingTickets as $ticket) {
            $notifications[] = [
                'id' => $ticket->id,
                'type' => 'pending_ticket',
                'title' => 'Menunggu Sparepart: ' . $ticket->title,
                'message' => 'Tiket menunggu ketersediaan sparepart',
                'url' => route('admin.tickets.show', $ticket->id),
                'time' => $ticket->created_at->diffForHumans(),
                'icon' => 'fa-clock',
                'color' => 'text-yellow-600',
            ];
        }

        // Sort by created_at descending
        usort($notifications, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return response()->json(['notifications' => array_slice($notifications, 0, 10)]);
    }
}
