<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Menampilkan Dashboard User (Halaman Utama)
     * Mengambil riwayat tiket user dan statistik.
     */
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->whereIn('status', ['open', 'in_progress', 'pending_sparepart'])
            ->latest()
            ->get();

        // Hitung stats (opsional)
        $stats = [
            'process' => Ticket::where('user_id', Auth::id())->where('status', 'in_progress')->count(),
            'done'    => Ticket::where('user_id', Auth::id())->where('status', 'resolved')->count(),
        ];

        // Jika scan aset (scan logic)
        $scannedAsset = null;

        return view('staff.dashboard', compact('tickets', 'stats', 'scannedAsset'));
    }

    /**
     * Menampilkan Form Lapor Kerusakan
     */
    public function create(Request $request)
    {
        $scannedAsset = null;

        if ($request->has('asset_uuid')) {
            $scannedAsset = Asset::with('category')
                ->where('uuid', $request->query('asset_uuid'))
                ->first();
        }

        if (!$scannedAsset && $request->has('asset_id')) {
            $scannedAsset = Asset::with('category')
                ->find($request->query('asset_id'));
        }

        $commonIssues = ['Lainnya'];

        if ($scannedAsset && $scannedAsset->category) {
            $code = $scannedAsset->category->code;

            $commonIssues = match ($code) {
                'CAT-HVAC' => ['AC Bocor', 'Tidak Dingin', 'Berisik', 'Mati Total'],
                'CAT-ELC'  => ['Layar Gelap', 'Tidak Ada Sinyal', 'Mati Total', 'Kabel Putus'],
                'CAT-PLB'  => ['Air Mampet', 'Kran Bocor', 'Air Keruh', 'Bau Saluran'],
                'CAT-FUR'  => ['Patah', 'Engsel Lepas', 'Robek'],
                default    => ['Rusak Fisik', 'Tidak Berfungsi', 'Lainnya']
            };
        }

        $allAssets = Asset::all();

        return view('staff.tickets.create', compact('scannedAsset', 'commonIssues', 'allAssets'));
    }

    /**
     * Menyimpan Data Laporan ke Database
     */
    public function store(Request $request)
    {
        // A. Validasi Input (Security Check)
        $request->validate([
            'asset_id'              => 'required|exists:assets,id',
            'title'                 => 'required|string|max:255',
            'priority'              => 'required|in:low,medium,high',
            'description'           => 'nullable|string',
            'photo_evidence_before' => 'required|image|mimes:jpeg,png,jpg|max:10240',
        ], [
            'asset_id.required' => 'Aset tidak valid.',
            'title.required' => 'Masalah harus dipilih.',
            'photo_evidence_before.required' => 'Bukti foto wajib diupload.',
            'photo_evidence_before.image' => 'File harus berupa gambar.',
            'photo_evidence_before.max' => 'Ukuran foto maksimal 10MB.',
        ]);

        // B. Proses Upload Foto
        $photoPath = null;
        if ($request->hasFile('photo_evidence_before')) {
            $photoPath = $request->file('photo_evidence_before')->store('evidence', 'public');
        }

        // C. GENERATE NOMOR TIKET (Format: TIK-TAHUNBULAN-URUTAN)
        $prefix = 'TIK-' . date('Ym') . '-';

        // Cari tiket terakhir yang memiliki prefix bulan ini
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

        // Gabungkan prefix dengan nomor urut (dipadding 0 di depan, misal: 0001)
        $generatedTicketNumber = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // D. Simpan ke Database
        Ticket::create([
            'ticket_number'         => $generatedTicketNumber,
            'user_id'               => Auth::id(),
            'asset_id'              => $request->asset_id,
            'title'                 => $request->title,
            'description'           => $request->description ?? '-',
            'priority'              => $request->priority,
            'photo_evidence_before' => $photoPath,
            'status'                => 'open',
        ]);

        // E. Redirect kembali ke Dashboard dengan Pesan Sukses
        return redirect()->route('staff.dashboard')
            ->with('success', 'Laporan berhasil dikirim! Nomor Tiket: ' . $generatedTicketNumber);
    }

    /**
     * Menampilkan Halaman Riwayat
     */
    public function history()
    {
        $user = Auth::user();

        // Ambil semua tiket milik user, urutkan dari yang terbaru
        $tickets = Ticket::where('user_id', $user->id)
            ->with('asset')
            ->latest()
            ->get();

        return view('staff.tickets.history', compact('tickets'));
    }

    /**
     * MENAMPILKAN DETAIL TIKET
     */
    public function show(Ticket $ticket)
    {
        // 1. Keamanan: Pastikan yang buka adalah Pemilik Tiket (atau Teknisi/Admin nanti)
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        // 2. Load relasi yang dibutuhkan
        $ticket->load(['asset.location', 'user', 'technician']);

        return view('staff.tickets.show', compact('ticket'));
    }

    public function verifyAsset($identifier)
    {
        $asset = Asset::where('uuid', $identifier)
            ->orWhere('id', $identifier)
            ->first();

        if ($asset) {
            return response()->json([
                'success' => true,
                'id'      => $asset->id,
                'name'    => $asset->name
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Aset tidak terdaftar di sistem hotel.'
        ], 404);
    }
}
