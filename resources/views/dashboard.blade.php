<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard - HMS</title>
    {{-- Tailwind & FontAwesome --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800 relative max-w-md mx-auto shadow-2xl min-h-screen flex flex-col">

    {{-- HEADER SECTION --}}
    <header class="bg-gradient-to-r from-blue-600 to-indigo-700 pt-10 pb-24 px-6 rounded-b-[2.5rem] shadow-lg relative z-10">
        <div class="flex justify-between items-start">
            <div>
                {{-- Greeting Logic PHP --}}
                @php
                $hour = date('H');
                $greeting = ($hour < 11) ? "Selamat Pagi" : (($hour < 15) ? "Selamat Siang" : (($hour < 19) ? "Selamat Sore" : "Selamat Malam" ));
                    @endphp
                    <p class="text-blue-100 text-sm font-medium mb-1">{{ $greeting }},</p>
                    <h1 class="text-2xl font-bold text-white mb-1">{{ Auth::user()->name }}</h1>
                    <div class="inline-flex items-center bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">
                        <span class="text-xs text-white font-medium tracking-wide">{{ Auth::user()->department ?? 'General' }}</span>
                    </div>
            </div>

            {{-- Logout Button (Optional) --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-white/20 w-10 h-10 rounded-full backdrop-blur-sm border border-white/10 flex items-center justify-center text-white hover:bg-white/30 transition">
                    <i class="fa-solid fa-power-off"></i>
                </button>
            </form>
        </div>

        {{-- Quick Stats --}}
        <div class="flex gap-4 mt-6">
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 flex-1 border border-white/10">
                <p class="text-xs text-blue-100">Diproses</p>
                <p class="text-xl font-bold text-white">{{ $stats['process'] }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 flex-1 border border-white/10">
                <p class="text-xs text-blue-100">Selesai</p>
                <p class="text-xl font-bold text-white">{{ $stats['done'] }}</p>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT (Report List) --}}
    <main class="flex-1 px-4 -mt-16 pb-28 overflow-y-auto z-20 scrollbar-hide">
        <div class="flex justify-between items-center mb-4 px-2">
            <h2 class="text-lg font-bold text-slate-800">Laporan Saya</h2>
            <span class="text-xs text-blue-600 font-semibold cursor-pointer">Lihat Semua</span>
        </div>

        <div class="space-y-4">
            @forelse($tickets as $ticket)
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow active:scale-[0.98] transition-transform cursor-pointer">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $ticket->ticket_number }}</span>

                    {{-- Status Badge Logic --}}
                    @php
                    $badgeColor = match($ticket->status) {
                    'open' => 'bg-orange-100 text-orange-700 border-orange-200',
                    'in_progress' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'resolved' => 'bg-green-100 text-green-700 border-green-200',
                    default => 'bg-gray-100 text-gray-700'
                    };
                    $icon = match($ticket->status) {
                    'open' => 'fa-clock',
                    'in_progress' => 'fa-spinner',
                    'resolved' => 'fa-check-circle',
                    default => 'fa-circle'
                    };
                    @endphp
                    <span class="flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $badgeColor }}">
                        <i class="fa-solid {{ $icon }} mr-1.5 text-[10px]"></i> {{ ucfirst($ticket->status) }}
                    </span>
                </div>

                <h3 class="font-bold text-slate-800 mb-1 line-clamp-1">{{ $ticket->title }}</h3>

                <div class="flex items-center text-slate-500 text-xs mb-3">
                    <i class="fa-solid fa-location-dot mr-1.5 text-slate-400"></i>
                    <span class="line-clamp-1">{{ $ticket->asset->location->name ?? 'Lokasi Manual' }}</span>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                    <div class="flex items-center text-slate-400 text-xs">
                        <i class="fa-regular fa-calendar mr-1.5"></i>
                        {{ $ticket->created_at->format('d M Y') }}
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                </div>
            </div>
            @empty
            {{-- Empty State --}}
            <div class="text-center py-10 opacity-60">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-regular fa-folder-open text-2xl text-slate-400"></i>
                </div>
                <p class="text-sm text-slate-500">Belum ada laporan kerusakan.</p>
            </div>
            @endforelse

            <div class="h-8"></div>
        </div>
    </main>

    {{-- FLOATING ACTION BUTTON (SCAN) --}}
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-40">
        {{-- Kita arahkan langsung ke halaman Create Ticket dulu (simulasi scan) --}}
        <a href="{{ route('tickets.create') }}"
            class="group relative flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full shadow-lg shadow-blue-600/40 hover:bg-blue-700 transition-all hover:scale-105 active:scale-95">
            <div class="absolute inset-0 rounded-full border border-white/20 animate-ping"></div>
            <div class="flex flex-col items-center justify-center">
                <i class="fa-solid fa-qrcode text-white text-xl"></i>
            </div>
        </a>
        <span class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 text-[10px] font-bold text-slate-400 tracking-wide whitespace-nowrap">SCAN ASET</span>
    </div>

    {{-- BOTTOM NAVIGATION BAR --}}
    <div class="bg-white border-t border-slate-200 h-20 px-6 pb-2 flex justify-between items-center relative z-30">
        <button class="flex flex-col items-center justify-center w-16 h-full text-blue-600">
            <i class="fa-solid fa-house text-lg mb-1"></i>
            <span class="text-[10px] font-medium">Beranda</span>
        </button>

        <button class="flex flex-col items-center justify-center w-16 h-full text-slate-400 hover:text-blue-600">
            <i class="fa-regular fa-file-lines text-lg mb-1"></i>
            <span class="text-[10px] font-medium">Riwayat</span>
        </button>
    </div>

</body>

</html>