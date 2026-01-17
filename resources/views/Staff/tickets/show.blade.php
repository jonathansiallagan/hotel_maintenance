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

            {{-- UPDATE: Status Badge Dinamis --}}
            @php
            $statusConfig = match($ticket->status) {
            'open' => ['class' => 'bg-gray-100 text-gray-600 border-gray-200', 'label' => 'Menunggu'],
            'in_progress' => ['class' => 'bg-blue-100 text-blue-700 border-blue-200', 'label' => 'Dikerjakan'],
            'pending_sparepart' => ['class' => 'bg-amber-100 text-amber-700 border-amber-200', 'label' => 'Pending Alat'],
            'resolved' => ['class' => 'bg-green-100 text-green-700 border-green-200', 'label' => 'Selesai'],
            default => ['class' => 'bg-gray-100', 'label' => $ticket->status]
            };
            @endphp
            <div class="{{ $statusConfig['class'] }} border px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-sm">
                {{ $statusConfig['label'] }}
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

                        {{-- UPDATE: Menampilkan Nama Teknisi --}}
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Teknisi</p>
                            @if($ticket->technician)
                            <p class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-user-gear text-blue-600"></i>
                                {{ Str::limit($ticket->technician->name, 15) }}
                            </p>
                            @else
                            <p class="text-sm text-slate-400 italic flex items-center gap-2">
                                <i class="fa-solid fa-user-clock"></i> Belum ada
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

            {{-- TIMELINE PROGRES (LOGIC DIPERBARUI) --}}
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

                        {{-- Step 2: Logic Diproses / Pending / Active --}}
                        @if($ticket->status != 'open')
                        <div class="relative animate-fade-in-up">
                            @if($ticket->status == 'pending_sparepart')
                            {{-- Tampilan PENDING --}}
                            <div class="absolute -left-[21px] bg-amber-500 h-3 w-3 rounded-full border-2 border-white shadow-sm ring-2 ring-amber-100"></div>
                            <p class="text-xs font-bold text-amber-600">Pending Sparepart</p>
                            <p class="text-[10px] text-slate-500 mt-1">
                                Teknisi <span class="font-bold">{{ $ticket->technician->name ?? '...' }}</span> sedang menunggu material.
                            </p>
                            @if($ticket->technician_note)
                            <p class="text-[10px] bg-amber-50 p-2 rounded mt-1 border border-amber-100 italic">"{{ $ticket->technician_note }}"</p>
                            @endif
                            @else
                            {{-- Tampilan IN PROGRESS / DONE --}}
                            <div class="absolute -left-[21px] bg-blue-500 h-3 w-3 rounded-full border-2 border-white shadow-sm ring-2 ring-blue-100"></div>
                            <p class="text-xs font-bold text-slate-700">Sedang Dikerjakan</p>
                            <p class="text-[10px] text-slate-400">
                                Ditangani oleh:
                                <span class="font-bold text-blue-600">{{ $ticket->technician->name ?? 'Teknisi' }}</span>
                            </p>
                            @endif
                        </div>
                        @else
                        {{-- Tampilan MASIH MENUNGGU (Belum diambil) --}}
                        <div class="relative opacity-40 grayscale">
                            <div class="absolute -left-[21px] bg-slate-300 h-3 w-3 rounded-full border-2 border-white"></div>
                            <p class="text-xs font-medium text-slate-500">Menunggu Teknisi</p>
                        </div>
                        @endif

                        {{-- Step 3: Selesai --}}
                        @if($ticket->status == 'resolved')
                        <div class="relative animate-fade-in-up">
                            <div class="absolute -left-[21px] bg-green-500 h-3 w-3 rounded-full border-2 border-white shadow-sm ring-2 ring-green-100"></div>
                            <p class="text-xs font-bold text-slate-700">Perbaikan Selesai</p>
                            <p class="text-[10px] text-slate-400">
                                {{ $ticket->completed_at ? \Carbon\Carbon::parse($ticket->completed_at)->format('d M, H:i') : '-' }}
                            </p>

                            {{-- Menampilkan Catatan Penyelesaian --}}
                            @if($ticket->technician_note)
                            <div class="mt-2 text-[10px] text-slate-600 bg-green-50 border border-green-100 p-2 rounded">
                                <span class="font-bold">Catatan Teknisi:</span><br>
                                {{ $ticket->technician_note }}
                            </div>
                            @endif

                            {{-- Tombol Lihat Foto Hasil (Optional) --}}
                            @if($ticket->photo_evidence_after)
                            <a href="{{ asset('storage/'.$ticket->photo_evidence_after) }}"
                                target="_blank"
                                class="mt-2 text-[10px] flex items-center gap-1 text-green-600 hover:underline cursor-pointer">
                                <i class="fa-solid fa-image"></i> Lihat Bukti Selesai
                            </a>
                            @endif
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