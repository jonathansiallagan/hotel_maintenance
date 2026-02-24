<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\SparepartCategory;
use App\Models\AssetCategory;
use App\Models\SparepartLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SparepartController extends Controller
{
    public function index(Request $request)
    {
        if (SparepartCategory::count() === 0) {
            $assetCategories = AssetCategory::all();
            if ($assetCategories->isNotEmpty()) {
                foreach ($assetCategories as $c) {
                    SparepartCategory::create([
                        'name' => $c->name,
                        'code' => $this->generateCategoryCode($c->name),
                    ]);
                }
            } else {
                $defaults = ['Elektrikal', 'Mekanik', 'Plumbing', 'Umum', 'HVAC', 'Cleaning Supplies', 'Furniture', 'Kitchen Equipment', 'Bathroom Fixtures', 'Security Systems', 'Lighting', 'Carpentry', 'Painting'];
                foreach ($defaults as $name) {
                    SparepartCategory::create(['name' => $name, 'code' => $this->generateCategoryCode($name)]);
                }
            }
        }

        $default = SparepartCategory::firstOrCreate(
            ['name' => 'Umum'],
            ['code' => $this->generateCategoryCode('Umum')]
        );
        Sparepart::whereNull('sparepart_category_id')->update(['sparepart_category_id' => $default->id]);

        $query = Sparepart::with('category');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku_code', 'like', "%{$search}%");
            });
        }

        $spareparts = $query->latest()->paginate(10)->withQueryString();

        return view('Admin.spareparts.index', compact('spareparts'));
    }

    public function create()
    {
        $categories = SparepartCategory::all();
        if ($categories->isEmpty()) {
            $defaults = ['Elektrikal', 'Mekanik', 'Plumbing', 'Umum', 'HVAC', 'Cleaning Supplies', 'Furniture', 'Kitchen Equipment', 'Bathroom Fixtures', 'Security Systems', 'Lighting', 'Carpentry', 'Painting'];
            foreach ($defaults as $name) {
                SparepartCategory::create(['name' => $name, 'code' => $this->generateCategoryCode($name)]);
            }
            $categories = SparepartCategory::all();
        }

        return view('Admin.spareparts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku_code' => 'nullable|string|max:100|unique:spareparts,sku_code',
            'sparepart_category_id' => 'required|exists:sparepart_categories,id',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'price_per_unit' => 'required|numeric|min:0',
        ]);

        if (empty($data['sku_code'])) {
            $data['sku_code'] = $this->generateSku($data['name']);
        }

        $sparepart = Sparepart::create($data);

        if ($sparepart->stock > 0) {
            SparepartLog::create([
                'sparepart_id' => $sparepart->id,
                'user_id' => Auth::id(),
                'transaction_type' => 'in',
                'quantity' => $sparepart->stock,
                'balance' => $sparepart->stock,
                'description' => 'Stok awal saat barang didaftarkan'
            ]);
        }

        return redirect()->route('admin.spareparts.index')
            ->with('success', 'Sparepart berhasil ditambahkan!');
    }

    public function show($id)
    {
        $sparepart = Sparepart::with(['category', 'logs' => function ($query) {
            $query->latest();
        }, 'logs.user', 'logs.ticket'])->findOrFail($id);

        return view('Admin.spareparts.show', compact('sparepart'));
    }

    public function edit($id)
    {
        $sparepart = Sparepart::findOrFail($id);
        $categories = SparepartCategory::all();
        if ($categories->isEmpty()) {
            $defaults = ['Elektrikal', 'Mekanik', 'Plumbing', 'Umum', 'HVAC', 'Cleaning Supplies', 'Furniture', 'Kitchen Equipment', 'Bathroom Fixtures', 'Security Systems', 'Lighting', 'Carpentry', 'Painting'];
            foreach ($defaults as $name) {
                SparepartCategory::create(['name' => $name, 'code' => $this->generateCategoryCode($name)]);
            }
            $categories = SparepartCategory::all();
        }

        return view('Admin.spareparts.edit', compact('sparepart', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku_code' => 'nullable|string|max:100|unique:spareparts,sku_code,' . $id,
            'sparepart_category_id' => 'required|exists:sparepart_categories,id',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'price_per_unit' => 'required|numeric|min:0',
        ]);

        if (empty($data['sku_code'])) {
            $data['sku_code'] = $this->generateSku($data['name']);
        }

        $oldStock = $sparepart->stock;
        $sparepart->update($data);

        if ($oldStock != $sparepart->stock) {
            $diff = $sparepart->stock - $oldStock;
            SparepartLog::create([
                'sparepart_id' => $sparepart->id,
                'user_id' => Auth::id(),
                'transaction_type' => $diff > 0 ? 'in' : 'out',
                'quantity' => abs($diff),
                'balance' => $sparepart->stock,
                'description' => 'Penyesuaian stok manual oleh Admin'
            ]);
        }

        return redirect()->route('admin.spareparts.index')
            ->with('success', 'Sparepart berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $sparepart = Sparepart::findOrFail($id);

        // Cek apakah sparepart sedang digunakan di ticket yang belum selesai
        if ($sparepart->tickets()->where('status', '!=', 'resolved')->exists()) {
            return redirect()->route('admin.spareparts.index')
                ->with('error', 'Sparepart tidak dapat dihapus karena sedang digunakan dalam ticket yang belum selesai.');
        }

        $sparepart->delete();

        return redirect()->route('admin.spareparts.index')
            ->with('success', 'Sparepart berhasil dihapus!');
    }

    /**
     * Generate a unique SKU code based on name and a random suffix.
     */
    protected function generateSku(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $abbr = '';
        foreach ($words as $w) {
            $abbr .= strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $w), 0, 1));
            if (strlen($abbr) >= 3) break;
        }
        if ($abbr === '') {
            $abbr = 'SP';
        }

        $attempt = 0;
        do {
            $suffix = strtoupper(substr(md5(uniqid((string) rand(), true)), 0, 4));
            $sku = sprintf('%s-%s-%s', $abbr, date('ym'), $suffix);
            $exists = Sparepart::where('sku_code', $sku)->exists();
            $attempt++;
        } while ($exists && $attempt < 10);

        if ($exists) {
            $sku = $abbr . '-' . time();
        }

        return $sku;
    }

    /**
     * Generate a short code for category names (uppercase slug).
     */
    protected function generateCategoryCode(string $name): string
    {
        $base = Str::upper(Str::slug($name, '_'));
        $code = $base;
        $i = 1;
        while (SparepartCategory::where('code', $code)->exists()) {
            $code = $base . '_' . $i;
            $i++;
        }
        return $code;
    }
}
