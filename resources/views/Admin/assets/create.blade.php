<x-admin-layout>
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tambah Aset Baru</h2>
        <div class="text-sm breadcrumbs text-gray-500">
            <a href="{{ route('admin.assets.index') }}" class="hover:text-[#D0BBB8]">Master Aset</a>
            <span class="mx-2">/</span>
            <span>Tambah Baru</span>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-4xl">

        {{-- Alert Error Global --}}
        @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-600 p-4 rounded-xl text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.assets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Aset --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Aset <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: AC Daikin 1PK - Lobby"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8] focus:border-transparent transition" required>
                </div>

                {{-- Serial Number --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Serial Number (Opsional)</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="SN-12345678"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8] focus:border-transparent transition">
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8] bg-white" required>
                        <option value="">- Pilih Kategori -</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Lokasi --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                    <select name="location_id" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8] bg-white" required>
                        <option value="">- Pilih Lokasi -</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Foto Aset --}}
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Foto Aset</label>
                    <input type="file" name="image" class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-gray-100 file:text-gray-700
                        hover:file:bg-gray-200 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG. Maks 5MB.</p>
                </div>

                {{-- Deskripsi --}}
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Tambahan</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8] focus:border-transparent">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                <button type="submit" class="bg-[#D0BBB8] hover:bg-[#bda3a0] text-white px-6 py-2.5 rounded-lg font-bold shadow-md transition transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Aset
                </button>
                <a href="{{ route('admin.assets.index') }}" class="text-gray-500 hover:text-gray-700 font-bold text-sm px-4">
                    Batal
                </a>
            </div>

        </form>
    </div>
</x-admin-layout>