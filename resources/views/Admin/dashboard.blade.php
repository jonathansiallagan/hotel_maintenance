<x-admin-layout>

    {{-- AREA 1: STATISTIK CARD UTAMA (Sesuai kode lama tapi dengan style baru) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

        <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-gray-800 flex flex-col justify-between h-32 hover:shadow-md transition-shadow">
            <div class="text-gray-500 text-xs uppercase font-bold tracking-wider mb-2">Total Tiket</div>
            <div class="text-4xl font-bold text-gray-800">{{ $totalTickets }}</div>
            <div class="text-xs text-gray-400 mt-2">Semua Laporan Masuk</div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-red-500 flex flex-col justify-between h-32 hover:shadow-md transition-shadow">
            <div class="text-red-500 text-xs uppercase font-bold tracking-wider mb-2">Perlu Tindakan</div>
            <div class="text-4xl font-bold text-gray-800">{{ $openTickets }}</div>
            <div class="text-xs text-red-300 mt-2">Menunggu Respon</div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-blue-500 flex flex-col justify-between h-32 hover:shadow-md transition-shadow">
            <div class="text-blue-500 text-xs uppercase font-bold tracking-wider mb-2">Sedang Dikerjakan</div>
            <div class="text-4xl font-bold text-gray-800">{{ $processTickets }}</div>
            <div class="text-xs text-blue-300 mt-2">Dalam Proses Teknisi</div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-green-500 flex flex-col justify-between h-32 hover:shadow-md transition-shadow">
            <div class="text-green-500 text-xs uppercase font-bold tracking-wider mb-2">Selesai (Done)</div>
            <div class="text-4xl font-bold text-gray-800">{{ $doneTickets }}</div>

            {{-- Progress Bar Mini (Visualisasi Tambahan) --}}
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                @php
                $percentage = $totalTickets > 0 ? ($doneTickets / $totalTickets) * 100 : 0;
                @endphp
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
            </div>
        </div>

    </div>

    {{-- AREA 2: KONTEN UTAMA (TABEL & SUMMARY) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: TABEL AKTIVITAS TERBARU (Lebar: 2/3) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-lg">Tiket Masuk Terbaru</h3>
                <a href="{{ route('admin.tickets.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline transition">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Tiket</th>
                            <th class="px-6 py-4">Aset</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4">Teknisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentTickets as $ticket)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $ticket->ticket_number }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($ticket->title, 25) }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-medium">
                                {{ $ticket->asset->name ?? '-' }}
                                <div class="text-[10px] text-gray-400">{{ $ticket->asset->location->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                $color = match($ticket->status) {
                                'open' => 'bg-red-50 text-red-600 border border-red-100',
                                'in_progress' => 'bg-blue-50 text-blue-600 border border-blue-100',
                                'pending_sparepart' => 'bg-amber-50 text-amber-600 border border-amber-100',
                                'resolved' => 'bg-green-50 text-green-600 border border-green-100',
                                default => 'bg-gray-50 text-gray-600'
                                };
                                @endphp
                                <span class="{{ $color }} px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide inline-block min-w-[80px]">
                                    {{ str_replace('_', ' ', $ticket->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($ticket->technician)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-500">
                                        {{ substr($ticket->technician->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-700">{{ Str::limit($ticket->technician->name, 12) }}</span>
                                </div>
                                @else
                                <span class="text-gray-400 italic text-[10px]">Belum assign</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fa-regular fa-folder-open text-3xl mb-2 opacity-50"></i>
                                    <p class="text-sm">Belum ada tiket terbaru hari ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- KOLOM KANAN: SUMMARY MASTER DATA (Lebar: 1/3) --}}
        <div class="space-y-6">

            {{-- Box 1: Master Data Panel --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 p-6">
                <div class="border-b border-gray-100 pb-4 mb-4">
                    <h3 class="font-bold text-gray-800 text-lg">Master Data</h3>
                </div>

                <div class="space-y-4">
                    {{-- Aset --}}
                    <div onclick="window.location.href='{{ route('admin.assets.index') }}'" class="flex items-center justify-between p-4 bg-blue-50/50 rounded-xl border border-blue-100 hover:bg-blue-50 transition cursor-pointer group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 shadow-sm group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-boxes-stacked text-xl"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-gray-800 leading-none">{{ $totalAssets }}</div>
                                <div class="text-xs text-gray-500 font-bold uppercase mt-1">Total Aset</div>
                            </div>
                        </div>
                        <button class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:border-blue-200 transition">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    </div>

                    {{-- User --}}
                    <div onclick="window.location.href='{{ route('admin.users.index') }}'" class="flex items-center justify-between p-4 bg-purple-50/50 rounded-xl border border-purple-100 hover:bg-purple-50 transition cursor-pointer group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 shadow-sm group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-users text-xl"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-gray-800 leading-none">{{ $totalUsers }}</div>
                                <div class="text-xs text-gray-500 font-bold uppercase mt-1">Total User</div>
                            </div>
                        </div>
                        <button class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-purple-600 hover:border-purple-200 transition">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-admin-layout>