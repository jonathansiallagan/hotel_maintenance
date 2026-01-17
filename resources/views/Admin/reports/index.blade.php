<x-admin-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Laporan Kerusakan & Maintenance</h2>
        <p class="text-sm text-gray-500">Rekapitulasi data tiket berdasarkan periode.</p>
    </div>

    {{-- FILTER SECTION --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">

            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="border-gray-300 rounded-lg text-sm w-full md:w-40">
            </div>

            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="border-gray-300 rounded-lg text-sm w-full md:w-40">
            </div>

            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                <select name="status" class="border-gray-300 rounded-lg text-sm w-full md:w-40">
                    <option value="all">Semua Status</option>
                    <option value="resolved" {{ $status == 'resolved' ? 'selected' : '' }}>Selesai (Resolved)</option>
                    <option value="open" {{ $status == 'open' ? 'selected' : '' }}>Baru (Open)</option>
                    <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>Diproses</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 transition">
                    <i class="fa-solid fa-filter mr-1"></i> Tampilkan
                </button>

                {{-- Tombol Print (Membuka Tab Baru) --}}
                <a href="{{ route('admin.reports.print', ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $status]) }}"
                    target="_blank"
                    class="bg-gray-800 text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-gray-900 transition">
                    <i class="fa-solid fa-print mr-1"></i> Cetak PDF
                </a>
            </div>
        </form>
    </div>

    {{-- TABEL HASIL --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 text-sm">Hasil Pencarian: {{ $tickets->count() }} Data Ditemukan</h3>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="bg-white text-gray-500 font-bold uppercase text-xs border-b">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">No. Tiket</th>
                    <th class="px-4 py-3">Aset</th>
                    <th class="px-4 py-3">Masalah</th>
                    <th class="px-4 py-3">Teknisi</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tickets as $t)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $t->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $t->ticket_number ?? 'ID-'.$t->id }}</td>
                    <td class="px-4 py-3 font-bold">{{ $t->asset->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ Str::limit($t->description, 30) }}</td>
                    <td class="px-4 py-3">{{ $t->technician->name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-gray-100 border border-gray-200">
                            {{ str_replace('_', ' ', $t->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-400">Tidak ada data pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>