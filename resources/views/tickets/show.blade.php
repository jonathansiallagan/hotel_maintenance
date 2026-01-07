<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Detail Tiket - HMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 relative max-w-md mx-auto shadow-2xl min-h-screen flex flex-col pb-6">

    {{-- 1. NAVBAR (Sticky Top) --}}
    <div class="bg-white shadow-sm sticky top-0 z-50 px-4 py-4 flex items-center gap-4">
        <a href="{{ str_contains(url()->previous(), 'riwayat') ? route('tickets.history') : route('dashboard') }}"
            class="text-slate-500 hover:text-blue-600 transition">
            <i class="fa-solid fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-lg font-bold text-slate-800 leading-none">Detail Tiket</h1>
            <p class="text-[10px] text-slate-500 font-mono mt-1">{{ $ticket->ticket_number }}</p>
        </div>

        {{-- Status Badge di Kanan Atas --}}
        @php
        $statusColor = match($ticket->status) {
        'open' => 'bg-orange-100 text-orange-700',
        'in_progress' => 'bg-blue-100 text-blue-700',
        'resolved' => 'bg-green-100 text-green-700',
        default => 'bg-gray-100 text-gray-600'
        };
        $statusLabel = match($ticket->status) {
        'open' => 'Menunggu',
        'in_progress' => 'Diproses',
        'resolved' => 'Selesai',
        default => ucfirst($ticket->status)
        };
        @endphp
        <div class="ml-auto {{ $statusColor }} px-3 py-1 rounded-full text-[10px] font-bold">
            {{ $statusLabel }}
        </div>
    </div>

    {{-- 2. FOTO BUKTI (Hero Image) --}}
    <div class="w-full h-64 bg-slate-200 relative group overflow-hidden">
        @if($ticket->photo_evidence_before)
        <img src="{{ asset('storage/' . $ticket->photo_evidence_before) }}"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 cursor-pointer"
            onclick="window.open(this.src, '_blank')"
            alt="Bukti Kerusakan">

        {{-- Label Overlay --}}
        <div class="absolute bottom-3 left-3 bg-black/60 text-white px-3 py-1 rounded-md text-xs font-medium backdrop-blur-sm">
            <i class="fa-solid fa-camera mr-1"></i> Foto Kerusakan
        </div>
        @else
        <div class="flex flex-col items-center justify-center h-full text-slate-400">
            <i class="fa-regular fa-image text-4xl mb-2"></i>
            <span class="text-xs">Tidak ada foto</span>
        </div>
        @endif
    </div>

    {{-- 3. INFORMASI UTAMA --}}
    <div class="px-5 -mt-6 relative z-10">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <h2 class="text-xl font-bold text-slate-800 mb-1">{{ $ticket->title }}</h2>
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
                    <p class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-blue-500"></i>
                        {{ $ticket->asset->location->name ?? '-' }}
                    </p>
                    <p class="text-xs text-slate-500 pl-5">{{ $ticket->asset->name }}</p>
                </div>

                {{-- Prioritas --}}
                <div>
                    <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Prioritas</p>
                    @if($ticket->priority == 'high')
                    <p class="text-sm font-bold text-red-600 flex items-center gap-2">
                        <i class="fa-solid fa-fire"></i> Urgent
                    </p>
                    @elseif($ticket->priority == 'medium')
                    <p class="text-sm font-bold text-blue-600 flex items-center gap-2">
                        <i class="fa-solid fa-square text-xs"></i> Normal
                    </p>
                    @else
                    <p class="text-sm font-bold text-green-600 flex items-center gap-2">
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

    {{-- 4. TIMELINE PROGRES (Tracking) --}}
    <div class="px-5 mt-6">
        <h3 class="text-sm font-bold text-slate-800 mb-3 ml-1">Riwayat Status</h3>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <div class="relative pl-4 border-l-2 border-slate-100 space-y-6">

                {{-- Step 1: Dilaporkan (Selalu Ada) --}}
                <div class="relative">
                    <div class="absolute -left-[21px] bg-blue-500 h-3 w-3 rounded-full border-2 border-white shadow-sm"></div>
                    <p class="text-xs font-bold text-slate-700">Laporan Diterima</p>
                    <p class="text-[10px] text-slate-400">{{ $ticket->created_at->format('d M Y, H:i') }}</p>
                </div>

                {{-- Step 2: Diproses (Logic Check) --}}
                @if($ticket->status == 'in_progress' || $ticket->status == 'resolved')
                <div class="relative">
                    <div class="absolute -left-[21px] bg-blue-500 h-3 w-3 rounded-full border-2 border-white shadow-sm"></div>
                    <p class="text-xs font-bold text-slate-700">Sedang Dikerjakan Teknisi</p>
                    <p class="text-[10px] text-slate-400">Teknisi sedang memperbaiki aset.</p>
                </div>
                @elseif($ticket->status == 'open')
                {{-- State Belum Diproses --}}
                <div class="relative opacity-50">
                    <div class="absolute -left-[21px] bg-slate-200 h-3 w-3 rounded-full border-2 border-white"></div>
                    <p class="text-xs font-medium text-slate-500">Menunggu Teknisi</p>
                </div>
                @endif

                {{-- Step 3: Selesai (Logic Check) --}}
                @if($ticket->status == 'resolved')
                <div class="relative">
                    <div class="absolute -left-[21px] bg-green-500 h-3 w-3 rounded-full border-2 border-white shadow-sm"></div>
                    <p class="text-xs font-bold text-slate-700">Perbaikan Selesai</p>
                    <p class="text-[10px] text-slate-400">Masalah telah teratasi.</p>
                </div>
                @else
                {{-- State Belum Selesai --}}
                <div class="relative opacity-50">
                    <div class="absolute -left-[21px] bg-slate-200 h-3 w-3 rounded-full border-2 border-white"></div>
                    <p class="text-xs font-medium text-slate-500">Selesai</p>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Footer Spacer --}}
    <div class="h-8"></div>

</body>

</html>