<x-admin-layout>

    {{-- AREA 1: STATISTIK CARD UTAMA --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div
            class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-gray-800 flex flex-col justify-between h-32 hover:shadow-md transition-shadow">
            <div class="text-gray-500 text-xs uppercase font-bold tracking-wider mb-2">Total Tiket</div>
            <div class="text-4xl font-bold text-gray-800">{{ $totalTickets }}</div>
            <div class="text-xs text-gray-400 mt-2">Semua Laporan Masuk</div>
        </div>
        <div
            class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-red-500 flex flex-col justify-between h-32 hover:shadow-md transition-shadow">
            <div class="text-red-500 text-xs uppercase font-bold tracking-wider mb-2">Perlu Tindakan</div>
            <div class="text-4xl font-bold text-gray-800">{{ $openTickets }}</div>
            <div class="text-xs text-red-300 mt-2">Menunggu Respon</div>
        </div>
        <div
            class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-blue-500 flex flex-col justify-between h-32 hover:shadow-md transition-shadow">
            <div class="text-blue-500 text-xs uppercase font-bold tracking-wider mb-2">Sedang Dikerjakan</div>
            <div class="text-4xl font-bold text-gray-800">{{ $processTickets }}</div>
            <div class="text-xs text-blue-300 mt-2">Dalam Proses Teknisi</div>
        </div>
        <div
            class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-green-500 flex flex-col justify-between h-32 hover:shadow-md transition-shadow">
            <div class="text-green-500 text-xs uppercase font-bold tracking-wider mb-2">Selesai (Done)</div>
            <div class="text-4xl font-bold text-gray-800">{{ $doneTickets }}</div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                @php $percentage = $totalTickets > 0 ? ($doneTickets / $totalTickets) * 100 : 0; @endphp
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
            </div>
        </div>
    </div>

    {{-- AREA 2: KONTEN UTAMA (TABEL & SUMMARY) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- KOLOM KIRI: TABEL AKTIVITAS TERBARU --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-lg">Tiket Masuk Terbaru</h3>
                <a href="{{ route('admin.tickets.index') }}"
                    class="text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline transition">Lihat
                    Semua</a>
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
                                    <div class="text-[10px] text-gray-400">{{ $ticket->asset->location->name ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $color = match ($ticket->status) {
                                            'open' => 'bg-red-50 text-red-600 border border-red-100',
                                            'in_progress' => 'bg-blue-50 text-blue-600 border border-blue-100',
                                            'pending_sparepart' => 'bg-amber-50 text-amber-600 border border-amber-100',
                                            'resolved' => 'bg-green-50 text-green-600 border border-green-100',
                                            default => 'bg-gray-50 text-gray-600',
                                        };
                                    @endphp
                                    <span
                                        class="{{ $color }} px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide inline-block min-w-[80px]">
                                        {{ str_replace('_', ' ', $ticket->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    @if ($ticket->technician)
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-500">
                                                {{ substr($ticket->technician->name, 0, 1) }}
                                            </div>
                                            <span
                                                class="font-medium text-gray-700">{{ Str::limit($ticket->technician->name, 12) }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic text-[10px]">Belum assign</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">Belum ada tiket terbaru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- KOLOM KANAN: SUMMARY MASTER DATA --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 p-6">
                <div class="border-b border-gray-100 pb-4 mb-4">
                    <h3 class="font-bold text-gray-800 text-lg">Master Data</h3>
                </div>
                <div class="space-y-4">
                    <div onclick="window.location.href='{{ route('admin.assets.index') }}'"
                        class="flex items-center justify-between p-4 bg-blue-50/50 rounded-xl border border-blue-100 hover:bg-blue-50 transition cursor-pointer group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 shadow-sm group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-boxes-stacked text-xl"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-gray-800 leading-none">{{ $totalAssets }}</div>
                                <div class="text-xs text-gray-500 font-bold uppercase mt-1">Total Aset</div>
                            </div>
                        </div>
                    </div>
                    <div onclick="window.location.href='{{ route('admin.users.index') }}'"
                        class="flex items-center justify-between p-4 bg-purple-50/50 rounded-xl border border-purple-100 hover:bg-purple-50 transition cursor-pointer group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 shadow-sm group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-users text-xl"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-gray-800 leading-none">{{ $totalUsers }}</div>
                                <div class="text-xs text-gray-500 font-bold uppercase mt-1">Total User</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- AREA 3: ANALISIS AKAR MASALAH (RCA DASHBOARD)                             --}}
    {{-- ========================================================================= --}}

    <div class="bg-[#0A2647] rounded-3xl p-8 shadow-xl text-white mb-8">

        {{-- Header & Filter Controller --}}
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 pb-6 border-b border-blue-800/50">
            <div>
                <h2 class="text-2xl font-bold flex items-center gap-3">
                    <i class="fa-solid fa-microscope text-blue-400"></i> RCA Analytics
                </h2>
                <p class="text-blue-200 text-sm mt-1">Pemetaan Akar Masalah Kerusakan Aset</p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <form action="{{ url()->current() }}" method="GET"
                    class="flex items-center gap-3 bg-white/5 p-2 rounded-xl backdrop-blur-sm border border-white/10">
                    <select name="month"
                        class="bg-transparent border-none text-sm text-white focus:ring-0 cursor-pointer [&>option]:text-gray-900">
                        <option value="all" {{ $filterMonth == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                {{ $filterMonth == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>

                    <div class="w-px h-6 bg-blue-700/50"></div>

                    <select name="location_id"
                        class="bg-transparent border-none text-sm text-white focus:ring-0 cursor-pointer [&>option]:text-gray-900">
                        <option value="all">Semua Lokasi</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}" {{ $filterLocation == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-xs font-bold transition">
                        Filter
                    </button>
                </form>

                {{-- Tombol Cetak PDF --}}
                @if (!empty($rcaData))
                    <a href="{{ route('admin.dashboard.exportRca', ['month' => $filterMonth, 'location_id' => $filterLocation]) }}"
                        target="_blank"
                        class="bg-red-500 hover:bg-red-400 text-white px-4 py-3 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-red-500/20">
                        <i class="fa-solid fa-file-pdf"></i> Ekspor PDF
                    </a>
                @endif
            </div>
        </div>

        @if (empty($rcaData))
            <div class="text-center py-12 text-blue-200/50">
                <i class="fa-solid fa-chart-pie text-5xl mb-4 opacity-50"></i>
                <p>Tidak ada data RCA untuk filter yang dipilih.</p>
            </div>
        @else
            {{-- The Big Picture (Stacked Bar Chart) --}}
            <div class="bg-white rounded-2xl p-6 mb-8 shadow-inner">
                <h3 class="text-gray-800 font-bold mb-4 text-sm uppercase tracking-wider">Komposisi Masalah Seluruh Aset
                </h3>
                <div class="relative h-80 w-full">
                    <canvas id="stackedRcaChart"></canvas>
                </div>
            </div>

            {{-- Top 3 Problematic Categories (Pie Charts) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($topRcaData as $categoryName => $causes)
                    <div class="bg-white/10 border border-white/10 rounded-2xl p-6 backdrop-blur-md">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <div class="text-red-400 text-[10px] font-bold uppercase tracking-widest mb-1">
                                    Peringkat {{ $loop->iteration }} Rusak</div>
                                <h4 class="text-lg font-bold">{{ $categoryName }}</h4>
                            </div>
                            <div
                                class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center font-bold text-xs">
                                {{ array_sum($causes) }}x
                            </div>
                        </div>
                        <div class="relative h-48 w-full">
                            <canvas id="pieChart{{ $loop->iteration }}"></canvas>
                        </div>
                    </div>
                @endforeach
            </div>

        @endif
    </div>

    {{-- Script Chart.js --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const rawData = @json($rcaData ?? []);
                const topData = @json($topRcaData ?? []);

                if (Object.keys(rawData).length === 0) return;

                // Palet Warna Pintar
                const colorPalette = {
                    'Usia Pakai / Aus Alami': '#F59E0B', // Kuning
                    'Kurang Perawatan / Kotor': '#EF4444', // Merah
                    'Human Error / Kelalaian Tamu': '#8B5CF6', // Ungu
                    'Faktor Eksternal (Listrik/Cuaca/Air)': '#3B82F6', // Biru
                    'Lainnya': '#6B7280' // Abu-abu
                };

                // Helper: Generate Random Color for Custom RCA
                const getDynamicColor = (label) => {
                    if (colorPalette[label]) return colorPalette[label];
                    let hash = 0;
                    for (let i = 0; i < label.length; i++) hash = label.charCodeAt(i) + ((hash << 5) - hash);
                    return `hsl(${hash % 360}, 70%, 60%)`;
                };

                // 1. SETUP STACKED BAR CHART
                const categories = Object.keys(rawData);
                let allCauses = new Set();
                Object.values(rawData).forEach(cat => Object.keys(cat).forEach(cause => allCauses.add(cause)));
                allCauses = Array.from(allCauses);

                const datasets = allCauses.map(cause => {
                    return {
                        label: cause,
                        data: categories.map(cat => rawData[cat][cause] || 0),
                        backgroundColor: getDynamicColor(cause),
                        borderRadius: 4,
                    };
                });

                new Chart(document.getElementById('stackedRcaChart'), {
                    type: 'bar',
                    data: {
                        labels: categories,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                border: {
                                    dash: [4, 4]
                                }
                            }
                        }
                    }
                });

                // 2. SETUP TOP 3 PIE CHARTS
                let i = 1;
                for (const [category, causes] of Object.entries(topData)) {
                    const labels = Object.keys(causes);
                    const data = Object.values(causes);
                    const bgColors = labels.map(label => getDynamicColor(label));

                    new Chart(document.getElementById(`pieChart${i}`), {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: bgColors,
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(ctx) {
                                            return ' ' + ctx.label + ': ' + ctx.raw + ' Tiket';
                                        }
                                    }
                                }
                            }
                        }
                    });
                    i++;
                }
            });
        </script>
    @endpush

</x-admin-layout>
