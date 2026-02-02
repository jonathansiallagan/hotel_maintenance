<x-admin-layout>

    <div x-data="{ showDeleteModal: false, deleteUrl: '' }">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Master Data Sparepart</h2>
                <p class="text-sm text-gray-500">Kelola sparepart dan stok inventaris.</p>
            </div>
            <a href="{{ route('admin.spareparts.create') }}" class="bg-[#D0BBB8] hover:bg-[#bda3a0] text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Sparepart
            </a>
        </div>

        {{-- Success alert --}}
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2" role="alert">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Toolbar / Search --}}
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <form action="{{ route('admin.spareparts.index') }}" method="GET" class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / SKU..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#D0BBB8] focus:border-transparent">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Info Sparepart</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4 text-center">Stok</th>
                            <th class="px-6 py-4 text-right">Harga per Unit</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($spareparts as $sparepart)
                        <tr id="part-{{ $sparepart->id }}" class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0">
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fa-solid fa-box-archive"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $sparepart->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $sparepart->sku_code ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-[10px] font-bold">{{ $sparepart->category->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold {{ $sparepart->stock <= 0 ? 'text-red-600' : ($sparepart->stock <= 5 ? 'text-orange-600' : 'text-gray-800') }}">
                                    {{ $sparepart->stock ?? 0 }} {{ $sparepart->unit }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-green-600">
                                Rp {{ number_format($sparepart->price_per_unit, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <a href="{{ route('admin.spareparts.edit', $sparepart->id) }}" class="text-yellow-500 hover:text-yellow-600 font-bold text-xs border border-yellow-200 bg-yellow-50 px-3 py-1 rounded transition">Edit</a>

                                <button @click="showDeleteModal = true; deleteUrl = '{{ route('admin.spareparts.destroy', $sparepart->id) }}'" class="text-red-500 hover:text-red-600 font-bold text-xs border border-red-200 bg-red-50 px-3 py-1 rounded transition">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fa-regular fa-folder-open text-4xl mb-2 opacity-50"></i>
                                    <p>Belum ada data sparepart.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $spareparts->links() }}
            </div>
        </div>

        {{-- Delete Modal --}}
        <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">
            <div @click.away="showDeleteModal = false" x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-center">
                <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-500"></i>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Sparepart Ini?</h3>
                <p class="text-gray-500 text-sm mb-6">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>

                <div class="flex justify-center gap-3">
                    <button @click="showDeleteModal = false" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-bold text-sm hover:bg-gray-50 transition">Batal</button>

                    <form :action="deleteUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-500 text-white font-bold text-sm hover:bg-red-600 shadow-md transition transform hover:scale-105">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</x-admin-layout>
