<x-app-layout :hideNav="true">

    {{-- 1. STYLE TAMBAHAN (OPSIONAL) --}}
    @push('styles')
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    @endpush

    {{-- 2. KONTEN UTAMA (BODY) --}}
    <div class="relative max-w-md mx-auto min-h-screen flex flex-col bg-gray-50 text-gray-800 shadow-2xl">

        {{-- HEADER SECTION (NAVY BLUE THEME) --}}
        <header class="bg-[#0A2647] pt-8 pb-4 px-6 rounded-b-[2rem] shadow-lg sticky top-0 z-20">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        {{-- Avatar Initials --}}
                        <div class="w-10 h-10 rounded-full bg-blue-500/20 border border-blue-400/30 text-white flex items-center justify-center font-bold text-lg backdrop-blur-sm">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        {{-- Online Dot --}}
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-[#0A2647] rounded-full"></div>
                    </div>
                    <div>
                        <h1 class="text-sm font-bold text-white">Halo, {{ Auth::user()->name }}</h1>
                        <p class="text-xs text-blue-200">Technician • On Duty</p>
                    </div>
                </div>

                {{-- Logout / Notif --}}
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-white/10 w-9 h-9 rounded-full backdrop-blur-sm flex items-center justify-center text-white hover:bg-white/20 transition">
                            <i class="fa-solid fa-power-off text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- TABS MENU (Antrean vs Tugasku) --}}
            <div class="flex p-1 bg-[#071e38] rounded-xl">
                <a href="{{ route('technician.dashboard', ['tab' => 'queue']) }}"
                    class="flex-1 text-center py-2 text-sm font-medium rounded-lg transition-all flex items-center justify-center gap-2
                   {{ $tab == 'queue' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-400 hover:text-white' }}">
                    Antrean
                    @if($stats['queue'] > 0)
                    <span class="bg-white/20 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $stats['queue'] }}</span>
                    @endif
                </a>
                <a href="{{ route('technician.dashboard', ['tab' => 'mytask']) }}"
                    class="flex-1 text-center py-2 text-sm font-medium rounded-lg transition-all flex items-center justify-center gap-2
                   {{ $tab == 'mytask' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-400 hover:text-white' }}">
                    Tugasku
                    @if($stats['my_task'] > 0)
                    <span class="bg-white/20 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $stats['my_task'] }}</span>
                    @endif
                </a>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 px-4 pt-4 pb-24 overflow-y-auto scrollbar-hide">

            {{-- Flash Message --}}
            @if(session('success'))
            <div id="flash-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-4 shadow-sm flex items-center gap-2" role="alert">
                <i class="fa-solid fa-circle-check"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            @endif

            <div class="space-y-4">
                @forelse($tickets as $ticket)
                {{-- Logic Tampilan Prioritas --}}
                @php
                $isUrgent = $ticket->priority == 'high';
                $borderColor = $isUrgent ? 'border-l-red-500' : ($ticket->priority == 'medium' ? 'border-l-orange-400' : 'border-l-blue-400');
                $bgColor = $isUrgent ? 'bg-red-50/50' : 'bg-white';
                @endphp

                <div class="bg-white rounded-xl shadow-sm border-l-4 {{ $borderColor }} border-y border-r border-gray-100 p-4 relative overflow-hidden group hover:shadow-md transition-all">

                    {{-- Background Merah Tipis Jika Urgent --}}
                    @if($isUrgent)
                    <div class="absolute inset-0 bg-red-50/30 pointer-events-none"></div>
                    @endif

                    <div class="relative z-10">
                        {{-- Header Card --}}
                        <div class="flex justify-between items-start mb-2">
                            <span class="{{ $isUrgent ? 'bg-red-100 text-red-700 border-red-200' : 'bg-blue-50 text-blue-600 border-blue-100' }} border text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide flex items-center gap-1">
                                <i class="fa-solid {{ $isUrgent ? 'fa-fire' : 'fa-info-circle' }}"></i> {{ $ticket->priority }}
                            </span>
                            <span class="text-[10px] font-mono text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100">
                                {{ $ticket->ticket_number }}
                            </span>
                        </div>

                        {{-- Judul & Deskripsi --}}
                        <h3 class="font-bold text-gray-900 text-lg leading-tight mb-1">{{ $ticket->title }}</h3>
                        <p class="text-xs text-gray-500 mb-3 flex items-center gap-1">
                            <i class="fa-regular fa-clock"></i> {{ $ticket->created_at->diffForHumans() }}
                        </p>

                        {{-- Lokasi Aset --}}
                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                            <i class="fa-solid fa-location-dot text-gray-400"></i>
                            <span class="truncate">{{ $ticket->asset->location->name ?? '-' }} • {{ $ticket->asset->name ?? '-' }}</span>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="flex gap-2 pt-2">
                            @if($tab == 'queue')
                            {{-- Form Ambil Tiket --}}
                            <form action="{{ route('technician.job.update', $ticket->id) }}" method="POST" class="flex-1">
                                @csrf @method('PATCH')
                                <input type="hidden" name="action" value="take">
                                <button type="submit" class="w-full bg-[#0A2647] hover:bg-blue-900 text-white text-sm font-bold py-2.5 rounded-lg shadow-lg shadow-blue-900/20 active:scale-95 transition-transform flex items-center justify-center gap-2">
                                    <span>Ambil Tiket</span> <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </form>
                            @else
                            {{-- Tombol Update / Selesai --}}
                            <a href="{{ route('technician.job.show', $ticket->id) }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2.5 rounded-lg shadow-lg shadow-green-600/20 active:scale-95 transition-transform text-center flex items-center justify-center gap-2">
                                <span>Update / Selesai</span> <i class="fa-solid fa-screwdriver-wrench"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center py-16 opacity-60">
                    <div class="bg-gray-100 p-4 rounded-full mb-3">
                        <i class="fa-solid fa-clipboard-check text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-gray-800 font-bold text-sm">Tidak ada pekerjaan.</h3>
                    <p class="text-xs text-gray-500">
                        {{ $tab == 'queue' ? 'Antrean kosong, silakan istirahat.' : 'Anda belum mengambil tugas.' }}
                    </p>
                </div>
                @endforelse
            </div>
        </main>

        {{-- BOTTOM STATUS BAR (Optional, sebagai footer pemanis) --}}
        <div class="fixed bottom-0 w-full max-w-md bg-white border-t border-gray-200 p-3 text-center text-[10px] text-gray-400 z-50 shadow-lg">
            <p>&copy; HMS System • V1.0</p>
        </div>

    </div>

    {{-- 3. JAVASCRIPT KHUSUS DASHBOARD INI --}}
    @push('scripts')
    <script>
        // Auto-hide flash message
        setTimeout(function() {
            let alert = document.getElementById('flash-message');
            if (alert) {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = 0;
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
    @endpush

</x-app-layout>