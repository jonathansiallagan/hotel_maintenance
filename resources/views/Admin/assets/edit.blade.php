<x-admin-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Aset</h2>
        <div class="text-sm breadcrumbs text-gray-500">
            <a href="{{ route('admin.assets.index') }}" class="hover:text-[#D0BBB8]">Master Aset</a>
            <span class="mx-2">/</span>
            <span>Edit Data</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-4xl">
        <form action="{{ route('admin.assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT') {{-- PENTING: Method PUT untuk Update --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Aset --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Aset</label>
                    <input type="text" name="name" value="{{ old('name', $asset->name) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]" required>
                </div>

                {{-- Serial Number --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Serial Number</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]">
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                    <select name="category_id" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $asset->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Lokasi --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi</label>
                    <select name="location_id" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]" required>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ $asset->location_id == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Aset</label>
                    <select name="status" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]">
                        <option value="active" {{ $asset->status == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $asset->status == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                        <option value="maintenance" {{ $asset->status == 'maintenance' ? 'selected' : '' }}>Sedang Perbaikan</option>
                    </select>
                </div>

                {{-- Foto Aset (Opsional) --}}
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Update Foto (Kosongkan jika tidak ingin ubah)</label>
                    @if($asset->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $asset->image) }}" class="h-20 w-20 object-cover rounded border">
                        <span class="text-xs text-gray-400 block mt-1">Foto saat ini</span>
                    </div>
                    @endif
                    <input type="file" name="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                </div>

                {{-- Deskripsi --}}
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Tambahan</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]">{{ old('description', $asset->description) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                <button type="submit" class="bg-[#D0BBB8] hover:bg-[#bda3a0] text-white px-6 py-2.5 rounded-lg font-bold shadow-md transition">
                    Update Data
                </button>
                <a href="{{ route('admin.assets.index') }}" class="text-gray-500 hover:text-gray-700 font-bold text-sm px-4">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>