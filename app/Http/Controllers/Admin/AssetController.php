<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        // Fitur Pencarian Sederhana
        $query = Asset::with(['category', 'location']);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('serial_number', 'like', '%' . $request->search . '%');
        }

        $assets = $query->latest()->paginate(10);

        return view('Admin.assets.index', compact('assets'));
    }

    // DETAIL ASSET DENGAN RIWAYAT TIKET
    public function show($id)
    {
        $asset = Asset::with(['category', 'location', 'tickets' => function ($query) {
            $query->with(['user', 'technician'])->latest();
        }])->findOrFail($id);

        // Statistik tiket
        $ticketStats = [
            'total' => $asset->tickets->count(),
            'open' => $asset->tickets->where('status', 'open')->count(),
            'in_progress' => $asset->tickets->where('status', 'in_progress')->count(),
            'resolved' => $asset->tickets->where('status', 'resolved')->count(),
            'closed' => $asset->tickets->where('status', 'closed')->count(),
        ];

        return view('Admin.assets.show', compact('asset', 'ticketStats'));
    }

    // Kita buat method create, store, edit, update nanti setelah index jadi.
    public function create()
    {
        $categories = AssetCategory::all();
        $locations = Location::all();
        return view('Admin.assets.create', compact('categories', 'locations'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:asset_categories,id',
            'location_id'   => 'required|exists:locations,id',
            'serial_number' => 'nullable|string|max:100',
            'image'         => 'nullable|image|max:5000',
            'description'   => 'nullable|string',
        ]);

        // 2. Handle Upload Gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('assets', 'public');
        }

        // 3. Simpan ke Database
        Asset::create([
            'name'          => $request->name,
            'category_id'   => $request->category_id,
            'location_id'   => $request->location_id,
            'serial_number' => $request->serial_number,
            'description'   => $request->description,
            'image'         => $imagePath,
            'status'        => 'active',
        ]);

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->route('admin.assets.index')
            ->with('success', 'Aset berhasil ditambahkan!');
    }

    // Fitur Cetak QR Code
    public function printQr($id)
    {
        $asset = \App\Models\Asset::findOrFail($id);

        $url = route('staff.tickets.create', ['asset_uuid' => $asset->uuid]);

        $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->errorCorrection('M')
            ->generate($url);

        return view('Admin.assets.print-qr', compact('asset', 'qrCode'));
    }

    // --- EDIT DATA ---
    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $categories = AssetCategory::all();
        $locations = Location::all();

        return view('Admin.assets.edit', compact('asset', 'categories', 'locations'));
    }

    // --- UPDATE DATA ---
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:asset_categories,id',
            'location_id'   => 'required|exists:locations,id',
            'serial_number' => 'nullable|string|max:100',
            'image'         => 'nullable|image|max:2048',
            'description'   => 'nullable|string',
            'status'        => 'required|in:active,inactive,maintenance',
        ]);

        // Cek jika ada upload gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($asset->image && \Illuminate\Support\Facades\Storage::exists('public/' . $asset->image)) {
                \Illuminate\Support\Facades\Storage::delete('public/' . $asset->image);
            }
            // Simpan gambar baru
            $imagePath = $request->file('image')->store('assets', 'public');
        } else {
            // Pakai gambar lama
            $imagePath = $asset->image;
        }

        $asset->update([
            'name'          => $request->name,
            'category_id'   => $request->category_id,
            'location_id'   => $request->location_id,
            'serial_number' => $request->serial_number,
            'description'   => $request->description,
            'image'         => $imagePath,
            'status'        => $request->status,
        ]);

        return redirect()->route('Admin.assets.index')->with('success', 'Data aset berhasil diperbarui!');
    }

    // --- DELETE DATA ---
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);

        // Hapus gambar dari storage biar tidak nyampah
        if ($asset->image && \Illuminate\Support\Facades\Storage::exists('public/' . $asset->image)) {
            \Illuminate\Support\Facades\Storage::delete('public/' . $asset->image);
        }

        $asset->delete();

        return redirect()->route('Admin.assets.index')->with('success', 'Aset berhasil dihapus!');
    }

    public function scanAssetJson($identifier)
    {
        // 1. Cari Aset berdasarkan UUID (karena QR Code biasanya menyimpan UUID)
        $asset = Asset::where('uuid', $identifier)->first();

        // 2. Jika tidak ketemu via UUID, coba cari pakai ID biasa (backup plan)
        if (!$asset) {
            $asset = Asset::find($identifier);
        }

        // 3. Respon Hasil
        if ($asset) {
            return response()->json([
                'success' => true,
                'id' => $asset->id,
                'name' => $asset->name,
                'location' => $asset->location->name ?? '-',
                'message' => 'Aset ditemukan'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data aset tidak ditemukan dalam sistem.'
            ], 404);
        }
    }
}
