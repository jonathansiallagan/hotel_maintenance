<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        // 1. Ambil HANYA tiket yang sedang AKTIF (Tugas Staff saat ini)
        $tickets = Ticket::where('user_id', Auth::id())
            ->whereIn('status', ['open', 'in_progress', 'pending_sparepart'])
            ->latest()
            ->get();

        $stats = [
            'process' => Ticket::where('user_id', Auth::id())->where('status', 'in_progress')->count(),
            'done'    => Ticket::where('user_id', Auth::id())->where('status', 'resolved')->count(),
        ];

        $scannedAsset = null;

        return view('Staff.dashboard', compact('tickets', 'stats', 'scannedAsset'));
    }

    /**
     * Menampilkan Form Lapor Kerusakan
     */
    public function create(Request $request)
    {
        // Logika Scan
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

        //logika kategori lainnya
        $commonIssues = ['Lainnya'];

        // 1. Default common issues (Kategori Umum)
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

        // 2. Ambil Histori Masalah "Lainnya" dari Database Aset untuk Dropdown
        $historyIssues = [];
        if (isset($scannedAsset) && !empty($scannedAsset->problem_history)) {
            $historyIssues = array_diff($scannedAsset->problem_history, $commonIssues);
        }

        $allAssets = Asset::all();

        return view('Staff.tickets.create', compact('scannedAsset', 'commonIssues', 'historyIssues', 'allAssets'));
    }

    /**
     * Menyimpan Data Laporan ke Database
     */
    public function store(Request $request)
    {
        // A. Validasi Input (Security Check & Filter Kata Kotor)
        $request->validate([
            'asset_id'              => 'required|exists:assets,id',
            'priority'              => 'required|in:low,medium,high',
            'photo_evidence_before' => 'required|image|mimes:jpeg,png,jpg|max:10240',
            'title'                 => [
                'required',
                'string',
                'max:255',
                // --- CUSTOM RULE: FILTER KATA KOTOR (Judul) ---
                function ($attribute, $value, $fail) {
                    $blackList = [
                        'anjing',
                        'babi',
                        'bangsat',
                        'tolol',
                        'goblok',
                        'bodoh',
                        'sialan',
                        'kontol',
                        'memek',
                        'jembut',
                        'ngentot',
                        'bego',
                        'pantek',
                        'asu',
                        'bajingan',
                        'tai',
                        'kunyuk'
                    ];
                    $inputLower = strtolower($value);
                    foreach ($blackList as $badWord) {
                        if (str_contains($inputLower, $badWord)) {
                            $fail('Judul masalah mengandung kata yang tidak pantas.');
                            return;
                        }
                    }
                },
            ],
            'description' => [
                'nullable',
                'string',
                // --- CUSTOM RULE: FILTER KATA KOTOR (Deskripsi) ---
                function ($attribute, $value, $fail) {
                    if (!$value) return;
                    $blackList = [
                        'anjing',
                        'babi',
                        'bangsat',
                        'tolol',
                        'goblok',
                        'bodoh',
                        'sialan',
                        'kontol',
                        'memek',
                        'jembut',
                        'ngentot',
                        'bego',
                        'pantek',
                        'asu',
                        'bajingan'
                    ];
                    $inputLower = strtolower($value);
                    foreach ($blackList as $badWord) {
                        if (str_contains($inputLower, $badWord)) {
                            $fail('Deskripsi mengandung kata yang tidak pantas.');
                            return;
                        }
                    }
                },
            ],
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
        $generatedTicketNumber = DB::transaction(function () {
            $prefix = 'TIK-' . date('Ym') . '-';

            // Cari tiket terakhir yang memiliki prefix bulan ini
            $lastTicket = Ticket::where('ticket_number', 'like', $prefix . '%')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            // Hitung nomor urut
            if ($lastTicket) {
                $lastNumber = intval(substr($lastTicket->ticket_number, -4));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            // Gabungkan prefix dengan nomor urut
            return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        });

        // --- TAMBAHAN: LOGIKA HYBRID LEARNING (Menyimpan Kategori Baru) ---
        $asset = Asset::findOrFail($request->asset_id);

        // Ambil histori lama (array)
        $currentHistory = $asset->problem_history;
        if (!is_array($currentHistory)) {
            $currentHistory = [];
        }

        // Cek duplikat (Case Insensitive)
        $inputTitle = $request->title;
        $inputLower = strtolower($inputTitle);
        $historyLower = array_map('strtolower', $currentHistory);

        // Jika tidak ada di history DAN jumlah history masih di bawah 10
        if (!in_array($inputLower, $historyLower) && count($currentHistory) < 10) {
            $currentHistory[] = $inputTitle;
            $asset->update(['problem_history' => $currentHistory]);
            $asset->save();
        }
        // ------------------------------------------------------------------

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
    public function history(Request $request)
    {
        // 1. Mulai Query
        $query = Ticket::where('user_id', Auth::id())
            ->with('asset');

        // 2. LOGIKA FILTER
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // 3. Eksekusi Query
        $tickets = $query->latest()->get();

        // 4. Return View History
        return view('Staff.tickets.history', compact('tickets'));
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

        return view('Staff.tickets.show', compact('ticket'));
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
