<x-admin-layout>

    <div x-data="{ showDeleteModal: false, deleteUrl: '' }">

        {{-- HEADER HALAMAN --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Master Data Aset</h2>
                <p class="text-sm text-gray-500">Kelola seluruh aset hotel dan mesin.</p>
            </div>
            <a href="{{ route('admin.assets.create') }}"
                class="bg-[#D0BBB8] hover:bg-[#bda3a0] text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Aset
            </a>
        </div>

        {{-- ALERT SUKSES --}}
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2" role="alert">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
        @endif

        {{-- KONTEN TABEL --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Toolbar Pencarian --}}
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <form action="{{ route('admin.assets.index') }}" method="GET" class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama / serial number..."
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#D0BBB8] focus:border-transparent">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Info Aset</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Lokasi</th>
                            <th class="px-6 py-4 text-center">QR Code</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    {{-- Gambar Thumbnail --}}
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0">
                                        @if($asset->image)
                                        <img src="{{ asset('storage/' . $asset->image) }}" class="w-full h-full object-cover">
                                        @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fa-solid fa-cube"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $asset->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $asset->serial_number ?? 'No S/N' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-[10px] font-bold">
                                    {{ $asset->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <i class="fa-solid fa-location-dot text-red-400 mr-1"></i>
                                {{ $asset->location->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{-- Tombol Generate QR (Placeholder dulu) --}}
                                <a href="{{ route('admin.assets.print_qr', $asset->id) }}"
                                    target="_blank"
                                    class="text-gray-400 hover:text-[#D0BBB8] transition transform hover:scale-110"
                                    title="Cetak QR Code">
                                    <i class="fa-solid fa-qrcode text-xl"></i>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                {{-- Tombol Detail --}}
                                <a href="{{ route('admin.assets.show', $asset->id) }}"
                                    class="text-blue-500 hover:text-blue-600 font-bold text-xs border border-blue-200 bg-blue-50 px-3 py-1 rounded transition">
                                    Detail
                                </a>

                                {{-- Tombol Hapus --}}
                                <button @click="showDeleteModal = true; deleteUrl = '{{ route('admin.assets.destroy', $asset->id) }}'"
                                    class="text-red-500 hover:text-red-600 font-bold text-xs border border-red-200 bg-red-50 px-3 py-1 rounded transition">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fa-regular fa-folder-open text-4xl mb-2 opacity-50"></i>
                                    <p>Belum ada data aset.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 border-t border-gray-100">
                {{ $assets->links() }}
            </div>
        </div>

        {{-- Overlay Background --}}
        <div x-show="showDeleteModal"
            x-transition.opacity
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            style="display: none;"> {{-- style display none agar tidak kedip saat load --}}

            {{-- Modal Content --}}
            <div @click.away="showDeleteModal = false"
                x-show="showDeleteModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-center">

                {{-- Ikon Peringatan --}}
                <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-500"></i>
                </div>

                {{-- Judul & Pesan --}}
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Aset Ini?</h3>
                <p class="text-gray-500 text-sm mb-6">
                    Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan dan foto aset akan hilang permanen.
                </p>

                {{-- Tombol Aksi --}}
                <div class="flex justify-center gap-3">
                    {{-- Tombol Batal --}}
                    <button @click="showDeleteModal = false"
                        class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-bold text-sm hover:bg-gray-50 transition">
                        Batal
                    </button>

                    {{-- Tombol Konfirmasi (Form Submit) --}}
                    {{-- Action URL dinamis berdasarkan tombol yg diklik --}}
                    <form :action="deleteUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-red-500 text-white font-bold text-sm hover:bg-red-600 shadow-md transition transform hover:scale-105">
                            Ya, Hapus
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</x-admin-layout>