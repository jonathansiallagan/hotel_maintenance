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
        // Pastikan relasi 'user', 'technician', 'asset' ada di model Ticket
        $recentTickets = Ticket::with(['user', 'technician', 'asset'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Statistik Aset & Teknisi
        $totalAssets = Asset::count();
        $totalTechnicians = User::where('role', 'technician')->count();
        $totalUsers = User::count();

        return view('Admin.dashboard', compact(
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

    public function notifications()
    {
        // Get recent notifications - tickets that need attention
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

        // Sort by created_at descending (berdasarkan string time diff mungkin kurang akurat, 
        // idealnya sorting object collection sebelum loop, tapi untuk notif sederhana ini oke)

        return response()->json(['notifications' => $notifications]);
    }
}
