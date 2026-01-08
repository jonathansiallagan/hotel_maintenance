<x-app-layout :hideNav="true">

    {{-- 1. CONTAINER UTAMA --}}
    <div class="relative max-w-md mx-auto min-h-screen flex flex-col bg-slate-50 text-slate-800 shadow-2xl font-sans">

        {{-- 2. NAVBAR (Sticky Top) --}}
        <div class="bg-white shadow-sm sticky top-0 z-50 px-4 py-4 flex items-center gap-4">
            <a href="{{ str_contains(url()->previous(), 'riwayat') ? route('staff.tickets.history') : route('staff.dashboard') }}"
                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-500 hover:text-blue-600 transition">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-lg font-bold text-slate-800 leading-none">Detail Tiket</h1>
                <p class="text-[10px] text-slate-500 font-mono mt-0.5">{{ $ticket->ticket_number }}</p>
            </div>

            {{-- Status Badge di Kanan Atas --}}
            @php
            $statusColor = match($ticket->status) {
            'open' => 'bg-orange-100 text-orange-700 border border-orange-200',
            'in_progress' => 'bg-blue-100 text-blue-700 border border-blue-200',
            'resolved' => 'bg-green-100 text-green-700 border border-green-200',
            default => 'bg-gray-100 text-gray-600'
            };
            $statusLabel = match($ticket->status) {
            'open' => 'Menunggu',
            'in_progress' => 'Diproses',
            'resolved' => 'Selesai',
            default => ucfirst($ticket->status)
            };
            @endphp
            <div class="{{ $statusColor }} px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                {{ $statusLabel }}
            </div>
        </div>

        {{-- 3. KONTEN SCROLLABLE --}}
        <main class="flex-1 pb-10 overflow-y-auto">

            {{-- FOTO BUKTI (Hero Image) --}}
            <div class="w-full h-64 bg-slate-200 relative group overflow-hidden">
                @if($ticket->photo_evidence_before)
                <img src="{{ asset('storage/' . $ticket->photo_evidence_before) }}"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 cursor-pointer"
                    onclick="window.open(this.src, '_blank')"
                    alt="Bukti Kerusakan">

                {{-- Label Overlay --}}
                <div class="absolute bottom-3 left-3 bg-black/60 text-white px-3 py-1 rounded-md text-xs font-medium backdrop-blur-sm pointer-events-none">
                    <i class="fa-solid fa-camera mr-1"></i> Foto Kerusakan
                </div>
                @else
                <div class="flex flex-col items-center justify-center h-full text-slate-400">
                    <i class="fa-regular fa-image text-4xl mb-2"></i>
                    <span class="text-xs">Tidak ada foto</span>
                </div>
                @endif
            </div>

            {{-- INFORMASI UTAMA --}}
            <div class="px-5 -mt-6 relative z-10">
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                    <h2 class="text-xl font-bold text-slate-800 mb-1 leading-tight">{{ $ticket->title }}</h2>
                    <div class="flex items-center text-xs text-slate-500 mb-4">
                        <i class="fa-regular fa-clock mr-1.5"></i>
                        Dilaporkan {{ $ticket->created_at->diffForHumans() }}
                    </div>

                    <hr class="border-slate-100 mb-4">

                    {{-- Detail Grid --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Lokasi --}}
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Lokasi / Aset</p>
                            <p class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-1">
                                <i class="fa-solid fa-location-dot text-blue-500"></i>
                                {{ $ticket->asset->location->name ?? '-' }}
                            </p>
                            <p class="text-xs text-slate-500 pl-5 truncate">{{ $ticket->asset->name }}</p>
                        </div>

                        {{-- Prioritas --}}
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Prioritas</p>
                            @if($ticket->priority == 'high')
                            <p class="text-sm font-bold text-red-600 flex items-center gap-2 bg-red-50 px-2 py-1 rounded w-max">
                                <i class="fa-solid fa-fire"></i> Urgent
                            </p>
                            @elseif($ticket->priority == 'medium')
                            <p class="text-sm font-bold text-blue-600 flex items-center gap-2 bg-blue-50 px-2 py-1 rounded w-max">
                                <i class="fa-solid fa-square text-xs"></i> Normal
                            </p>
                            @else
                            <p class="text-sm font-bold text-green-600 flex items-center gap-2 bg-green-50 px-2 py-1 rounded w-max">
                                <i class="fa-solid fa-arrow-down"></i> Low
                            </p>
                            @endif
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mt-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Deskripsi Masalah</p>
                        <p class="text-sm text-slate-700 leading-relaxed">
                            {{ $ticket->description }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- TIMELINE PROGRES --}}
            <div class="px-5 mt-6 mb-8">
                <h3 class="text-sm font-bold text-slate-800 mb-4 ml-1">Riwayat Status</h3>

                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                    <div class="relative pl-4 border-l-2 border-slate-100 space-y-8">

                        {{-- Step 1: Dilaporkan --}}
                        <div class="relative">
                            <div class="absolute -left-[21px] bg-blue-500 h-3 w-3 rounded-full border-2 border-white shadow-sm ring-2 ring-blue-100"></div>
                            <p class="text-xs font-bold text-slate-700">Laporan Diterima</p>
                            <p class="text-[10px] text-slate-400">{{ $ticket->created_at->format('d M Y, H:i') }}</p>
                        </div>

                        {{-- Step 2: Diproses --}}
                        @if($ticket->status == 'in_progress' || $ticket->status == 'resolved')
                        <div class="relative">
                            <div class="absolute -left-[21px] bg-blue-500 h-3 w-3 rounded-full border-2 border-white shadow-sm ring-2 ring-blue-100"></div>
                            <p class="text-xs font-bold text-slate-700">Sedang Dikerjakan Teknisi</p>
                            <p class="text-[10px] text-slate-400">Teknisi sedang melakukan perbaikan.</p>
                        </div>
                        @else
                        <div class="relative opacity-40 grayscale">
                            <div class="absolute -left-[21px] bg-slate-300 h-3 w-3 rounded-full border-2 border-white"></div>
                            <p class="text-xs font-medium text-slate-500">Menunggu Teknisi</p>
                        </div>
                        @endif

                        {{-- Step 3: Selesai --}}
                        @if($ticket->status == 'resolved')
                        <div class="relative">
                            <div class="absolute -left-[21px] bg-green-500 h-3 w-3 rounded-full border-2 border-white shadow-sm ring-2 ring-green-100"></div>
                            <p class="text-xs font-bold text-slate-700">Perbaikan Selesai</p>
                            <p class="text-[10px] text-slate-400">Masalah telah teratasi.</p>
                        </div>
                        @else
                        <div class="relative opacity-40 grayscale">
                            <div class="absolute -left-[21px] bg-slate-300 h-3 w-3 rounded-full border-2 border-white"></div>
                            <p class="text-xs font-medium text-slate-500">Selesai</p>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

        </main>
    </div>

</x-app-layout>