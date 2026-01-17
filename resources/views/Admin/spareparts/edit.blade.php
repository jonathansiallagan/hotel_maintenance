<x-admin-layout>

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">Edit Sparepart</h1>
                <p class="text-sm text-gray-500">Perbarui data sparepart.</p>
            </div>
            <a href="{{ route('admin.spareparts.index') }}" class="text-sm font-bold text-gray-600 hover:text-gray-800">Kembali</a>
        </div>

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <ul class="text-sm">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('admin.spareparts.update', $sparepart->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-bold">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $sparepart->name) }}" required class="w-full mt-1 px-3 py-2 rounded border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]">
                    </div>

                    <div>
                        <label class="text-sm font-bold">Kategori</label>
                        <select name="sparepart_category_id" required class="w-full mt-1 px-3 py-2 rounded border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]">
                            <option value="" disabled {{ old('sparepart_category_id', $sparepart->sparepart_category_id) ? '' : 'selected' }}>Pilih kategori</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (old('sparepart_category_id', $sparepart->sparepart_category_id) == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-bold">Stok</label>
                        <input type="number" name="stock" value="{{ old('stock', $sparepart->stock) }}" min="0" required placeholder="contoh: 10" onfocus="if(this.value==='0') this.value=''" class="w-full mt-1 px-3 py-2 rounded border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]">
                    </div>

                    <div>
                        <label class="text-sm font-bold">Satuan</label>
                        <select name="unit" required class="w-full mt-1 px-3 py-2 rounded border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]">
                            <option value="" disabled {{ old('unit', $sparepart->unit) ? '' : 'selected' }}>Pilih satuan</option>
                            <option value="pcs" {{ old('unit', $sparepart->unit) === 'pcs' ? 'selected' : ($sparepart->unit === 'pcs' ? 'selected' : '') }}>pcs</option>
                            <option value="box" {{ old('unit', $sparepart->unit) === 'box' ? 'selected' : ($sparepart->unit === 'box' ? 'selected' : '') }}>box</option>
                            <option value="set" {{ old('unit', $sparepart->unit) === 'set' ? 'selected' : ($sparepart->unit === 'set' ? 'selected' : '') }}>set</option>
                            <option value="unit" {{ old('unit', $sparepart->unit) === 'unit' ? 'selected' : ($sparepart->unit === 'unit' ? 'selected' : '') }}>unit</option>
                            <option value="kg" {{ old('unit', $sparepart->unit) === 'kg' ? 'selected' : ($sparepart->unit === 'kg' ? 'selected' : '') }}>kg</option>
                            <option value="liter" {{ old('unit', $sparepart->unit) === 'liter' ? 'selected' : ($sparepart->unit === 'liter' ? 'selected' : '') }}>liter</option>
                            <option value="meter" {{ old('unit', $sparepart->unit) === 'meter' ? 'selected' : ($sparepart->unit === 'meter' ? 'selected' : '') }}>meter</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-bold">Harga per Satuan</label>
                        <input type="number" step="0.01" name="price_per_unit" value="{{ old('price_per_unit', $sparepart->price_per_unit) }}" min="0" required placeholder="contoh: 25000" onfocus="if(this.value==='0' || this.value==='0.00') this.value=''" class="w-full mt-1 px-3 py-2 rounded border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('admin.spareparts.index') }}" class="px-4 py-2 rounded bg-gray-100 font-bold">Batal</a>
                    <button type="submit" class="px-4 py-2 rounded bg-[#D0BBB8] text-white font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>