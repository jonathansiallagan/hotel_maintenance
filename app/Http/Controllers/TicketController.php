<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Menampilkan Dashboard User (Halaman Utama)
     * Mengambil riwayat tiket user dan statistik.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil Tiket milik user yang sedang login
        // Kita gunakan 'with' agar data aset & lokasi ikut terbawa (Eager Loading)
        $tickets = Ticket::where('reporter_id', $user->id)
            ->with(['asset.location'])
            ->latest() // Urutkan dari yang paling baru
            ->get();

        // 2. Hitung Statistik Sederhana untuk Header Dashboard
        $stats = [
            'process' => $tickets->whereIn('status', ['open', 'in_progress', 'pending_sparepart'])->count(),
            'done' => $tickets->whereIn('status', ['resolved', 'closed'])->count(),
        ];

        // 3. Kirim data ke View 'dashboard.blade.php'
        return view('dashboard', compact('tickets', 'user', 'stats'));
    }

    /**
     * Menampilkan Form Lapor Kerusakan
     */
    public function create(Request $request)
    {
        // Ambil semua aset untuk dropdown (backup jika scan gagal)
        $assets = Asset::with('location')->where('status', 'active')->get();

        // Jika ada parameter 'asset_id' dari hasil scan
        $scannedAsset = null;
        if ($request->has('asset_id')) {
            $scannedAsset = Asset::with('location')->find($request->asset_id);
        }

        return view('tickets.create', compact('assets', 'scannedAsset'));
    }

    /**
     * Menyimpan Data Laporan ke Database
     */
    public function store(Request $request)
    {
        // A. Validasi Input (Security Check)
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'photo_evidence_before' => 'required|image|mimes:jpeg,png,jpg|max:10240', // Max 10MB
        ]);

        // B. Proses Upload Foto
        $photoPath = null;
        if ($request->hasFile('photo_evidence_before')) {
            // Simpan file di folder: storage/app/public/evidence
            $photoPath = $request->file('photo_evidence_before')->store('evidence', 'public');
        }

        // C. GENERATE NOMOR TIKET (MANUAL DI CONTROLLER)
        // Format: TIK-TAHUNBULAN-URUTAN (Contoh: TIK-202601-0001)
        $prefix = 'TIK-' . date('Ym') . '-';

        // Cari tiket terakhir bulan ini
        $lastTicket = Ticket::where('ticket_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        // Hitung nomor urut
        if ($lastTicket) {
            $lastNumber = intval(substr($lastTicket->ticket_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Gabungkan jadi string (Misal: TIK-202601-0001)
        $generatedTicketNumber = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // D. Simpan ke Database
        // Catatan: ticket_number otomatis dibuat oleh Model Ticket (boot method)
        Ticket::create([
            'ticket_number' => $generatedTicketNumber,
            'reporter_id' => Auth::id(),
            'asset_id' => $request->asset_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'photo_evidence_before' => $photoPath,
            'status' => 'open',
            'reported_at' => now(),
        ]);

        // E. Redirect kembali ke Dashboard dengan Pesan Sukses
        // Kita arahkan ke dashboard agar user bisa lihat tiketnya muncul di list
        return redirect()->route('dashboard')->with('success', 'Laporan berhasil dikirim! Teknisi akan segera mengecek.');
    }
}
