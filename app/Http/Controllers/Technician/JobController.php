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
        $ticket = Ticket::with(['asset.location', 'spareparts'])->findOrFail($id);

        if ($ticket->status == 'resolved') {
            return redirect()->route('technician.dashboard')
                ->with('error', 'Tiket ini sudah diselesaikan dan terkunci.');
        }

        $spareparts = Sparepart::where('stock', '>', 0)->get();

        $commonRca = [
            'Usia Pakai / Aus Alami',
            'Kurang Perawatan / Kotor',
            'Human Error / Kelalaian Tamu',
            'Faktor Eksternal (Listrik/Cuaca/Air)'
        ];

        $historyRca = $ticket->asset->category->rca_history ?? [];
        $historyRca = array_diff($historyRca, $commonRca);
        // ----------------------------------------------

        return view('Technician.jobs.show', compact('ticket', 'spareparts', 'commonRca', 'historyRca'));
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $action = $request->input('action');

        // Cek jika tiket yang mau diambil ternyata sudah selesai (safety check)
        if ($ticket->status == 'resolved') {
            return redirect()->route('technician.dashboard')->with('error', 'Tiket sudah selesai.');
        }

        // --- BAGIAN LOGIKA PENGAMBILAN TIKET (TAKE) ---
        if ($action == 'take') {
            // 1. Cek jumlah tiket yang sedang dipegang teknisi saat ini
            $myTasks = Ticket::where('technician_id', Auth::id())
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

                // --- ATURAN VALIDASI DINAMIS UNTUK RCA ---
                $rules = [
                    'photo_after' => 'required|image|max:10240',
                    'technician_note' => 'required',
                ];

                // Wajibkan isi RCA hanya jika tiket HIGH/URGENT
                if ($ticket->priority == 'high') {
                    $rules['root_cause'] = 'required|string';
                }

                $request->validate($rules, [
                    'photo_after.required' => 'Foto bukti selesai wajib diupload!',
                    'technician_note.required' => 'Catatan pengerjaan wajib diisi!',
                    'root_cause.required' => 'Akar masalah (RCA) WAJIB disimpulkan untuk tiket prioritas Urgent!'
                ]);
                // -----------------------------------------

                // Logika pengurangan stok sparepart
                if ($request->has('spareparts')) {
                    foreach ($request->spareparts as $part) {
                        if (!empty($part['id']) && !empty($part['qty'])) {
                            $sparepartDB = Sparepart::find($part['id']);
                            if ($sparepartDB && $sparepartDB->stock >= $part['qty']) {
                                $sparepartDB->decrement('stock', $part['qty']);
                                $ticket->spareparts()->attach($part['id'], ['quantity' => $part['qty']]);
                            }
                        }
                    }
                }

                // Upload Foto
                $path = $request->file('photo_after')->store('evidence', 'public');

                // --- LOGIKA BELAJAR RCA (SIMPAN KE ASET) ---
                if ($ticket->priority == 'high' && $request->filled('root_cause')) {
                    $category = $ticket->asset->category;
                    $currentRca = $category->rca_history;
                    if (!is_array($currentRca)) $currentRca = [];

                    $inputLower = strtolower($request->root_cause);
                    $historyLower = array_map('strtolower', $currentRca);
                    $commonRcaLower = ['usia pakai / aus alami', 'kurang perawatan / kotor', 'human error / kelalaian tamu', 'faktor eksternal (listrik/cuaca/air)'];

                    // Simpan RCA unik (Max 10)
                    if (!in_array($inputLower, $historyLower) && !in_array($inputLower, $commonRcaLower) && count($currentRca) < 10) {
                        $currentRca[] = $request->root_cause;
                        // Pakai update langsung
                        $category->update(['rca_history' => $currentRca]);
                    }
                }
                // -------------------------------------------

                // Simpan semuanya ke Tiket
                $ticket->update([
                    'status' => 'resolved',
                    'photo_evidence_after' => $path,
                    'technician_note' => $request->technician_note,
                    'root_cause' => $request->root_cause ?? null, // Simpan RCA ke tiket
                    'completed_at' => now(),
                ]);

                return redirect()->route('technician.dashboard', ['tab' => 'queue'])
                    ->with('success', 'Pekerjaan Selesai! Kerja bagus.');
            }
        }
    }
}
