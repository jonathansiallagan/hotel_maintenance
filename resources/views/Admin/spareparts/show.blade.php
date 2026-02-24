<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-2xl font-bold text-gray-800">Kartu Stok: {{ $sparepart->name }}</h1>
                    <span
                        class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold border border-blue-100">
                        {{ $sparepart->category->name ?? 'Tanpa Kategori' }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 font-mono">SKU: {{ $sparepart->sku_code ?? '-' }}</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.spareparts.edit', $sparepart->id) }}"
                    class="bg-yellow-50 hover:bg-yellow-100 text-yellow-600 border border-yellow-200 px-4 py-2 rounded-lg text-sm font-bold transition">
                    <i class="fa-solid fa-edit mr-1"></i> Edit Barang
                </a>
                <a href="{{ route('admin.spareparts.index') }}"
                    class="bg-[#D0BBB8] hover:bg-[#bda3a0] text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>

        {{-- RINGKASAN INFO (SUMMARY CARDS) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 text-2xl">
                    <i class="fa-solid fa-cubes"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-500 uppercase">Sisa Stok Saat Ini</div>
                    <div class="text-3xl font-bold {{ $sparepart->stock <= 5 ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $sparepart->stock }} <span
                            class="text-base font-medium text-gray-500">{{ $sparepart->unit }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center text-green-500 text-2xl">
                    <i class="fa-solid fa-tag"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-500 uppercase">Harga Per Satuan</div>
                    <div class="text-2xl font-bold text-gray-800">
                        Rp {{ number_format($sparepart->price_per_unit, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-full bg-purple-50 flex items-center justify-center text-purple-500 text-2xl">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-500 uppercase">Total Nilai Aset</div>
                    <div class="text-2xl font-bold text-gray-800">
                        Rp {{ number_format($sparepart->stock * $sparepart->price_per_unit, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL RIWAYAT / KARTU STOK --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-blue-500"></i> Riwayat Mutasi Barang (Log)
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Tipe Transaksi</th>
                            <th class="px-6 py-4 text-center">Perubahan</th>
                            <th class="px-6 py-4 text-center">Sisa Stok</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4">Pelaku</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($sparepart->logs as $log)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-800">{{ $log->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i') }} WIB</div>
                                </td>

                                <td class="px-6 py-4">
                                    @if ($log->transaction_type === 'in')
                                        <span
                                            class="bg-green-50 text-green-600 px-2 py-1 rounded text-[10px] font-bold border border-green-200"><i
                                                class="fa-solid fa-arrow-down mr-1"></i> BARANG MASUK</span>
                                    @elseif($log->transaction_type === 'out')
                                        <span
                                            class="bg-red-50 text-red-600 px-2 py-1 rounded text-[10px] font-bold border border-red-200"><i
                                                class="fa-solid fa-arrow-up mr-1"></i> BARANG KELUAR</span>
                                    @else
                                        <span
                                            class="bg-yellow-50 text-yellow-600 px-2 py-1 rounded text-[10px] font-bold border border-yellow-200"><i
                                                class="fa-solid fa-pen mr-1"></i> PENYESUAIAN</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center font-bold text-lg">
                                    @if ($log->transaction_type === 'in' || ($log->transaction_type === 'adjustment' && $log->quantity > 0))
                                        <span class="text-green-600">+{{ $log->quantity }}</span>
                                    @else
                                        <span class="text-red-600">-{{ abs($log->quantity) }}</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center font-bold text-gray-800 bg-gray-50/50">
                                    {{ $log->balance }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $log->description }}
                                    @if ($log->ticket_id)
                                        <a href="{{ route('admin.tickets.show', $log->ticket_id) }}"
                                            class="text-blue-500 hover:underline font-bold ml-1" target="_blank">Lihat
                                            Tiket <i class="fa-solid fa-external-link-alt text-[10px]"></i></a>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-600">
                                            {{ substr($log->user->name ?? 'Sys', 0, 1) }}
                                        </div>
                                        <span
                                            class="text-xs font-bold text-gray-700">{{ $log->user->name ?? 'Sistem' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-clipboard-list text-4xl mb-3 opacity-50 block"></i>
                                    Belum ada riwayat mutasi untuk barang ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-admin-layout>
