<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Sparepart;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'queue'); // Default ke 'queue' (Antrean)
        $user = Auth::user();

        if ($tab == 'queue') {
            // Tampilkan tiket yang BELUM diambil siapapun (Status Open)
            $tickets = Ticket::where('status', 'open')
                ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                ->latest()
                ->get();
        } else {
            // Tampilkan tiket milik teknisi ini yang sedang dikerjakan
            $tickets = Ticket::where('technician_id', $user->id)
                ->where('status', 'in_progress')
                ->latest()
                ->get();
        }

        // Stats Sederhana
        $stats = [
            'queue' => Ticket::where('status', 'open')->count(),
            'my_task' => Ticket::where('technician_id', $user->id)->where('status', 'in_progress')->count(),
        ];

        return view('technician.dashboard', compact('tickets', 'stats', 'tab'));
    }

    public function show($id)
    {
        $ticket = Ticket::with(['asset', 'reporter'])->findOrFail($id);
        $spareparts = Sparepart::all(); // Untuk dropdown
        return view('technician.jobs.show', compact('ticket', 'spareparts'));
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // Logic Ambil Tiket
        if ($request->action == 'take') {
            $ticket->update([
                'status' => 'in_progress',
                'technician_id' => Auth::id(),
                'started_at' => Carbon::now(), // Mulai Timer
            ]);
            return redirect()->route('technician.job.show', $id);
        }

        // Logic Selesai Tiket (Nanti diisi validasi upload foto dll)
        if ($request->action == 'finish') {
            // Handle upload foto after & sparepart disini...
        }

        return back();
    }
}
