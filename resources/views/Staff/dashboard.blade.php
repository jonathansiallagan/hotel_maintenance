<x-app-layout :hideNav="true">

    @push('styles')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
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

    {{-- WRAPPER UTAMA --}}
    <div class="relative max-w-md mx-auto min-h-screen flex flex-col bg-slate-50 text-slate-800 shadow-2xl">

        {{-- HEADER (Sama seperti sebelumnya) --}}
        <header class="bg-gradient-to-r from-blue-600 to-indigo-700 pt-10 pb-24 px-6 rounded-b-[2.5rem] shadow-lg relative z-10">
            <div class="flex justify-between items-start">
                <div>
                    @php
                    $hour = date('H');
                    $greeting = ($hour < 11) ? "Selamat Pagi" : (($hour < 15) ? "Selamat Siang" : (($hour < 19) ? "Selamat Sore" : "Selamat Malam" ));
                        @endphp
                        <p class="text-blue-100 text-sm font-medium mb-1">{{ $greeting }},</p>
                        <h1 class="text-2xl font-bold text-white mb-1">{{ Auth::user()->name }}</h1>
                        <div class="inline-flex items-center bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">
                            <span class="text-xs text-white font-medium tracking-wide">
                                {{ Auth::user()->department ?? 'Staff' }}
                            </span>
                        </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-white/20 w-10 h-10 rounded-full backdrop-blur-sm border border-white/10 flex items-center justify-center text-white hover:bg-white/30 transition">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                </form>
            </div>
            <div class="flex gap-4 mt-6">
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 flex-1 border border-white/10">
                    <p class="text-xs text-blue-100">Diproses</p>
                    <p class="text-xl font-bold text-white">{{ $stats['process'] ?? 0 }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 flex-1 border border-white/10">
                    <p class="text-xs text-blue-100">Selesai</p>
                    <p class="text-xl font-bold text-white">{{ $stats['done'] ?? 0 }}</p>
                </div>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        {{-- Padding bottom (pb-24) diperbesar agar konten terbawah tidak tertutup Footer --}}
        <main class="flex-1 px-4 -mt-16 pb-28 overflow-y-auto z-20 scrollbar-hide">
            <div class="flex justify-between items-center mb-4 px-2">
                <h2 class="text-lg font-bold text-slate-800">Laporan Saya</h2>
                <a href="{{ route('staff.tickets.history') }}" class="text-xs text-blue-600 font-semibold cursor-pointer hover:underline">
                    Lihat Semua
                </a>
            </div>

            <div class="space-y-4">
                @forelse($tickets as $ticket)
                <a href="{{ route('staff.tickets.show', $ticket->id) }}" class="block bg-white p-4 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow active:scale-[0.98] transition-transform cursor-pointer">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $ticket->ticket_number }}</span>
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
                        <span class="line-clamp-1">{{ $ticket->asset->location->name ?? '-' }}</span>
                    </div>
                </a>
                @empty
                <div class="text-center py-10 opacity-60">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fa-regular fa-folder-open text-2xl text-slate-400"></i>
                    </div>
                    <p class="text-sm text-slate-500">Belum ada laporan.</p>
                </div>
                @endforelse
            </div>
        </main>

        {{-- === FOOTER NAVIGATION (BERANDA - SCAN - RIWAYAT) === --}}
        {{-- Fixed position di bawah layar, max-width mengikuti container aplikasi --}}
        <div class="fixed bottom-0 w-full max-w-md z-40 bg-white border-t border-slate-200 shadow-[0_-5px_15px_rgba(0,0,0,0.03)] h-20 rounded-t-2xl">

            <div class="grid grid-cols-3 h-full items-center">

                {{-- 1. BERANDA (Kiri) --}}
                <a href="{{ route('staff.dashboard') }}" class="flex flex-col items-center justify-center h-full text-blue-600 cursor-pointer hover:bg-slate-50 rounded-tl-2xl">
                    <i class="fa-solid fa-house text-xl mb-1"></i>
                    <span class="text-[10px] font-bold">Beranda</span>
                </a>

                {{-- 2. SCAN ASET (Tengah - Floating Effect) --}}
                <div class="relative h-full flex items-center justify-center">
                    {{-- Tombol Scan menonjol keluar (absolute position minus top) --}}
                    <button onclick="startScanner()"
                        class="absolute -top-6 bg-blue-600 hover:bg-blue-700 text-white w-16 h-16 rounded-full shadow-lg shadow-blue-600/40 border-[6px] border-slate-50 flex items-center justify-center transform hover:scale-105 active:scale-95 transition-all">
                        <i class="fa-solid fa-qrcode text-2xl"></i>
                    </button>
                    {{-- Text label Scan di bawah tombol --}}
                    <span class="absolute bottom-3 text-[10px] font-medium text-slate-500">Scan</span>
                </div>

                {{-- 3. RIWAYAT (Kanan) --}}
                <a href="{{ route('staff.tickets.history') }}" class="flex flex-col items-center justify-center h-full text-slate-400 hover:text-blue-600 cursor-pointer hover:bg-slate-50 transition rounded-tr-2xl group">
                    <i class="fa-regular fa-file-lines text-xl mb-1 group-hover:-translate-y-0.5 transition-transform"></i>
                    <span class="text-[10px] font-medium">Riwayat</span>
                </a>

            </div>
        </div>

    </div>

    {{-- MODAL SCANNER & SCRIPT (Tetap sama) --}}
    <div id="scannerModal" class="fixed inset-0 bg-black/90 z-50 hidden flex flex-col items-center justify-center p-4">
        <div class="relative w-full max-w-sm bg-white rounded-2xl overflow-hidden shadow-2xl">
            <div class="bg-gray-900 p-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg"><i class="fa-solid fa-qrcode mr-2"></i>Scan QR Aset</h3>
                <button onclick="stopScanner()" class="text-gray-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
            </div>
            <div class="bg-black relative">
                <div id="reader" class="w-full h-64 bg-black"></div>
                <div id="scan-loading" class="absolute inset-0 flex items-center justify-center text-white text-xs pointer-events-none">
                    <p>Memuat Kamera...</p>
                </div>
            </div>
            <div class="p-4 bg-white text-center">
                <p class="text-xs text-gray-500">Arahkan kamera ke QR Code aset.</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ... (Script scanner sama seperti sebelumnya) ...
        let html5QrcodeScanner;

        function startScanner() {
            document.getElementById('scannerModal').classList.remove('hidden');
            html5QrcodeScanner = new Html5Qrcode("reader");
            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            };
            html5QrcodeScanner.start({
                    facingMode: "environment"
                }, config, onScanSuccess, onScanFailure)
                .then(() => {
                    document.getElementById('scan-loading').style.display = 'none';
                });
        }

        function stopScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    document.getElementById('scannerModal').classList.add('hidden');
                }).catch(err => console.log(err));
            } else {
                document.getElementById('scannerModal').classList.add('hidden');
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            html5QrcodeScanner.stop();
            fetch(`/scan-asset/${decodedText}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = `/staff/lapor?asset_id=${data.id}`;
                    } else {
                        alert('Aset tidak dikenali.');
                        stopScanner();
                    }
                })
                .catch(error => {
                    alert('Gagal memproses QR Code.');
                    stopScanner();
                });
        }

        function onScanFailure(error) {}
    </script>
    @endpush

</x-app-layout>