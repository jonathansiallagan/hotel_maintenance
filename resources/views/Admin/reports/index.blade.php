<x-admin-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Laporan Corrective Maintenance</h2>
        <p class="text-sm text-gray-500">Rekapitulasi data tiket berdasarkan periode.</p>
    </div>

    {{-- FILTER SECTION (Tetap Sama) --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="border-gray-300 rounded-lg text-sm w-full md:w-40">
            </div>
            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="border-gray-300 rounded-lg text-sm w-full md:w-40">
            </div>
            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                <select name="status" class="border-gray-300 rounded-lg text-sm w-full md:w-40">
                    <option value="all">Semua Status</option>
                    <option value="resolved" {{ $status == 'resolved' ? 'selected' : '' }}>Selesai</option>
                    <option value="open" {{ $status == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>Diproses</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 transition">
                    <i class="fa-solid fa-filter mr-1"></i> Tampilkan
                </button>
                <a href="{{ route('admin.reports.print', ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $status]) }}"
                    target="_blank"
                    class="bg-gray-800 text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-gray-900 transition">
                    <i class="fa-solid fa-print mr-1"></i> Cetak PDF
                </a>
            </div>
        </form>
    </div>

    {{-- TABEL HASIL (SESUAI GAMBAR EXCEL) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm text-left border-collapse">

            {{-- HEADER TABEL (Complex Header) --}}
            <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-xs border-b border-gray-200">
                <tr>
                    {{-- rowspan="2" agar sel ini tinggi ke bawah --}}
                    <th rowspan="2" class="px-4 py-3 border-r border-gray-100 align-middle">Tiket</th>
                    <th rowspan="2" class="px-4 py-3 border-r border-gray-100 align-middle">Aset</th>
                    <th rowspan="2" class="px-4 py-3 border-r border-gray-100 align-middle">Teknisi</th>

                    {{-- colspan="3" agar sel ini lebar ke samping --}}
                    <th colspan="3" class="px-4 py-2 border-b border-gray-200 text-center bg-gray-100">Time</th>

                    <th rowspan="2" class="px-4 py-3 align-middle text-center">Status</th>
                </tr>
                {{-- Baris kedua untuk sub-kolom Time --}}
                <tr class="text-[10px]">
                    <th class="px-2 py-2 text-center border-r border-gray-100">Respon</th>
                    <th class="px-2 py-2 text-center border-r border-gray-100">Pengerjaan</th>
                    <th class="px-2 py-2 text-center">Total Downtime</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($tickets as $t)
                    <tr class="hover:bg-gray-50 align-top">

                        {{-- KOLOM 1: TIKET (No Tiket & Tanggal) --}}
                        <td class="px-4 py-3 border-r border-gray-50">
                            <div class="font-bold text-gray-800">{{ $t->ticket_number }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fa-regular fa-calendar mr-1"></i>
                                {{ $t->created_at->format('d/m/Y') }}
                            </div>
                        </td>

                        {{-- KOLOM 2: ASET (Nama Aset & Masalah) --}}
                        <td class="px-4 py-3 border-r border-gray-50 max-w-xs">
                            <div class="font-bold text-blue-600">{{ $t->asset->name ?? '-' }}</div>
                            <div class="text-xs text-gray-600 mt-1 italic line-clamp-2">
                                "{{ $t->title }}"
                            </div>
                        </td>

                        {{-- KOLOM 3: TEKNISI --}}
                        <td class="px-4 py-3 border-r border-gray-50">
                            {{ $t->technician->name ?? '-' }}
                        </td>

                        {{-- KOLOM 4: TIME - RESPON --}}
                        <td class="px-2 py-3 text-center border-r border-gray-50 font-mono text-xs">
                            @if ($t->started_at)
                                {{ $t->created_at->diffForHumans($t->started_at, true) }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- KOLOM 5: TIME - PENGERJAAN --}}
                        <td class="px-2 py-3 text-center border-r border-gray-50 font-mono text-xs">
                            @if ($t->started_at && $t->completed_at)
                                {{ $t->started_at->diffForHumans($t->completed_at, true) }}
                            @elseif($t->started_at)
                                <span class="text-amber-500">Proses</span>
                            @else
                                -
                            @endif
                        </td>

                        {{-- KOLOM 6: TIME - TOTAL DOWNTIME --}}
                        <td class="px-2 py-3 text-center font-mono text-xs font-bold text-gray-700">
                            @if ($t->completed_at)
                                {{ $t->created_at->diffForHumans($t->completed_at, true) }}
                            @else
                                <span class="text-red-400">...</span>
                            @endif
                        </td>

                        {{-- KOLOM 7: STATUS --}}
                        <td class="px-4 py-3 text-center">
                            <span
                                class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-gray-100 border border-gray-200">
                                {{ str_replace('_', ' ', $t->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-400">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
