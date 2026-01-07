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
    public function create()
    {
        // Ambil data aset untuk Dropdown (Hanya yang statusnya active)
        $assets = Asset::with('location')->where('status', 'active')->get();

        return view('tickets.create', compact('assets'));
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

        // C. Simpan ke Database
        // Catatan: ticket_number otomatis dibuat oleh Model Ticket (boot method)
        Ticket::create([
            'reporter_id' => Auth::id(),
            'asset_id' => $request->asset_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'photo_evidence_before' => $photoPath,
            'status' => 'open', // Status awal selalu open
            'reported_at' => now(),
        ]);

        // D. Redirect kembali ke Dashboard dengan Pesan Sukses
        // Kita arahkan ke dashboard agar user bisa lihat tiketnya muncul di list
        return redirect()->route('dashboard')->with('success', 'Laporan berhasil dikirim! Teknisi akan segera mengecek.');
    }
}
