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
        $tab = $request->get('tab', 'queue');
        $user = Auth::user();

        if ($tab == 'queue') {
            $tickets = Ticket::where('status', 'open')
                ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                ->latest()
                ->get();
        } else {
            $tickets = Ticket::where('technician_id', $user->id)
                ->whereIn('status', ['in_progress', 'pending_sparepart'])
                ->latest()
                ->get();
        }

        $stats = [
            'queue' => Ticket::where('status', 'open')->count(),
            'my_task' => Ticket::where('technician_id', $user->id)
                ->where('status', ['in_progress', 'pending_sparepart'])
                ->count(),
        ];

        return view('Technician.dashboard', compact('tickets', 'stats', 'tab'));
    }

    public function show($id)
    {
        $ticket = \App\Models\Ticket::with(['asset.location', 'spareparts'])->findOrFail($id);

        if ($ticket->status == 'resolved') {
            return redirect()->route('Technician.dashboard')
                ->with('error', 'Tiket ini sudah diselesaikan dan terkunci.');
        }

        $spareparts = \App\Models\Sparepart::where('stock', '>', 0)->get();

        return view('Technician.jobs.show', compact('ticket', 'spareparts'));
    }

    public function update(Request $request, $id)
    {
        $ticket = \App\Models\Ticket::findOrFail($id);
        $action = $request->input('action');

        // Cek jika tiket yang mau diambil ternyata sudah selesai (safety check)
        if ($ticket->status == 'resolved') {
            return redirect()->route('technician.dashboard')->with('error', 'Tiket sudah selesai.');
        }

        // --- BAGIAN LOGIKA PENGAMBILAN TIKET (TAKE) ---
        if ($action == 'take') {

            // 1. Cek jumlah tiket yang sedang dipegang teknisi saat ini
            $myTasks = \App\Models\Ticket::where('technician_id', Auth::id())
                ->whereIn('status', ['in_progress', 'pending_sparepart'])
                ->get();

            // ATURAN 1: Jika sudah memegang 2 tiket, tolak mutlak.
            if ($myTasks->count() >= 2) {
                return redirect()->back()->with('error', 'Gagal! Anda sudah memegang batas maksimal 2 tiket.');
            }

            // ATURAN 2: Jika sudah memegang 1 tiket, cek statusnya.
            if ($myTasks->count() == 1) {
                $existingTicket = $myTasks->first();

                // Jika tiket pertama statusnya MASIH 'in_progress' (bukan pending), tolak tiket kedua.
                if ($existingTicket->status !== 'pending_sparepart') {
                    return redirect()->back()->with('error', 'Tiket pertama harus dipending dulu sebelum mengambil tiket kedua.');
                }
            }

            // Jika lolos validasi di atas, baru jalankan update
            $ticket->update([
                'status' => 'in_progress',
                'technician_id' => Auth::id(),
                'started_at' => now(),
            ]);

            // Redirect ke tab 'mytask' agar teknisi langsung melihat tiket yang baru diambil
            return redirect()->route('technician.dashboard', ['tab' => 'mytask'])
                ->with('success', 'Tiket berhasil diambil!');
        }
        // --- AKHIR LOGIKA TAKE ---


        // --- BAGIAN LOGIKA PENYELESAIAN (FINISH / PENDING) ---
        if ($action == 'finish') {
            $status = $request->input('status');

            // A. JIKA MEMILIH PENDING
            if ($status == 'pending_sparepart') {
                $request->validate(['technician_note' => 'required']);

                $ticket->update([
                    'status' => 'pending_sparepart',
                    'technician_note' => $request->technician_note
                ]);

                return redirect()->route('technician.dashboard', ['tab' => 'mytask'])
                    ->with('success', 'Status pending disimpan.');
            }

            // B. JIKA MEMILIH SELESAI (RESOLVED)
            if ($status == 'resolved') {
                $request->validate([
                    'photo_after' => 'required|image|max:10240', // Saya naikkan jadi 10MB biar aman
                    'technician_note' => 'required',
                ], [
                    'photo_after.required' => 'Foto bukti selesai wajib diupload!',
                    'technician_note.required' => 'Catatan pengerjaan wajib diisi!'
                ]);

                // Logika pengurangan stok sparepart
                if ($request->has('spareparts')) {
                    foreach ($request->spareparts as $part) {
                        if (!empty($part['id']) && !empty($part['qty'])) {
                            $sparepartDB = \App\Models\Sparepart::find($part['id']);
                            // Cek stok cukup atau tidak
                            if ($sparepartDB && $sparepartDB->stock >= $part['qty']) {
                                $sparepartDB->decrement('stock', $part['qty']);
                                $ticket->spareparts()->attach($part['id'], ['quantity' => $part['qty']]);
                            }
                        }
                    }
                }

                $path = $request->file('photo_after')->store('evidence', 'public');

                $ticket->update([
                    'status' => 'resolved',
                    'photo_evidence_after' => $path,
                    'technician_note' => $request->technician_note,
                    'completed_at' => now(),
                ]);

                return redirect()->route('technician.dashboard', ['tab' => 'queue'])
                    ->with('success', 'Pekerjaan Selesai! Kerja bagus.');
            }
        }
    }
}
