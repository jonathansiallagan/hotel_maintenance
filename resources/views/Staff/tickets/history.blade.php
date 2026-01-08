<x-app-layout :hideNav="true">

    {{-- 1. STYLE TAMBAHAN --}}
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

    {{-- 2. KONTEN UTAMA --}}
    <div class="relative max-w-md mx-auto min-h-screen flex flex-col bg-white text-slate-800 shadow-2xl">

        {{-- HEADER SECTION (Sama dengan Dashboard tapi tanpa stats) --}}
        <header class="bg-gradient-to-r from-blue-600 to-indigo-700 pt-8 pb-12 px-6 rounded-b-[3rem] shadow-md relative z-10">
            <div class="flex justify-between items-start">
                <div>
                    @php
                    $hour = date('H');
                    $greeting = ($hour < 11) ? "Selamat Pagi," : (($hour < 15) ? "Selamat Siang," : (($hour < 19) ? "Selamat Sore," : "Selamat Malam," ));
                        @endphp

                        <p class="text-blue-100 text-sm font-medium mb-1">{{ $greeting }}</p>
                        <h1 class="text-2xl font-bold text-white mb-1">{{ Auth::user()->name }}</h1>
                        <div class="inline-flex items-center bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full">
                            <span class="text-xs text-white font-medium tracking-wide">
                                {{ Auth::user()->department ?? 'General' }}
                            </span>
                        </div>
                </div>

                {{-- Logout Button --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-white/20 w-10 h-10 rounded-full backdrop-blur-sm border border-white/10 flex items-center justify-center text-white hover:bg-white/30 transition">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                </form>
            </div>
        </header>

        {{-- TITLE & FILTER BAR --}}
        <div class="px-6 mt-6 mb-2 flex justify-between items-center z-20">
            <h2 class="text-slate-800 font-bold text-lg uppercase tracking-wide">RIWAYAT LAPORAN</h2>

            {{-- Dropdown Filter (Visual Only - Logic bisa ditambahkan nanti) --}}
            <div class="relative">
                <select class="appearance-none bg-slate-100 text-slate-700 py-2 pl-4 pr-10 rounded-full text-xs font-bold border-none focus:ring-0 cursor-pointer shadow-sm hover:bg-slate-200 transition-colors">
                    <option>Semua</option>
                    <option>Menunggu</option>
                    <option>Diproses</option>
                    <option>Selesai</option>
                </select>
                <i class="fa-solid fa-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            </div>
        </div>

        {{-- MAIN CONTENT (LIST) --}}
        <main class="flex-1 px-4 py-2 pb-28 overflow-y-auto z-20 scrollbar-hide">
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
                        <span class="line-clamp-1">{{ $ticket->asset->location->name ?? 'Lokasi Manual' }}</span>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fa-regular fa-calendar mr-1.5"></i>
                            {{ $ticket->created_at->format('d M Y') }}
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                    </div>
                </a>
                @empty
                <div class="text-center py-10 opacity-60">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fa-regular fa-folder-open text-2xl text-slate-400"></i>
                    </div>
                    <p class="text-sm text-slate-500">Belum ada riwayat laporan.</p>
                </div>
                @endforelse

                <div class="h-8"></div>
            </div>
        </main>

        {{-- FOOTER NAVIGATION (Saya tambahkan agar konsisten dengan Dashboard) --}}
        <div class="fixed bottom-0 w-full max-w-md z-40 bg-white border-t border-slate-200 shadow-[0_-5px_15px_rgba(0,0,0,0.03)] h-20 rounded-t-2xl">
            <div class="grid grid-cols-3 h-full items-center">

                {{-- BERANDA (Link ke Dashboard - Abu) --}}
                <a href="{{ route('staff.dashboard') }}" class="flex flex-col items-center justify-center h-full text-slate-400 hover:text-blue-600 cursor-pointer hover:bg-slate-50 rounded-tl-2xl group transition">
                    <i class="fa-solid fa-house text-xl mb-1 group-hover:-translate-y-0.5 transition-transform"></i>
                    <span class="text-[10px] font-medium">Beranda</span>
                </a>

                {{-- SCAN BUTTON --}}
                <div class="relative h-full flex items-center justify-center">
                    <button onclick="startScanner()"
                        class="absolute -top-6 bg-blue-600 hover:bg-blue-700 text-white w-16 h-16 rounded-full shadow-lg shadow-blue-600/40 border-[6px] border-slate-50 flex items-center justify-center transform hover:scale-105 active:scale-95 transition-all">
                        <i class="fa-solid fa-qrcode text-2xl"></i>
                    </button>
                    <span class="absolute bottom-3 text-[10px] font-medium text-slate-500">Scan</span>
                </div>

                {{-- RIWAYAT (Aktif - Biru) --}}
                <a href="#" class="flex flex-col items-center justify-center h-full text-blue-600 cursor-pointer bg-slate-50/50 rounded-tr-2xl">
                    <i class="fa-solid fa-file-lines text-xl mb-1"></i>
                    <span class="text-[10px] font-bold">Riwayat</span>
                </a>
            </div>
        </div>
    </div>

    {{-- MODAL SCANNER --}}
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

    {{-- 3. SCRIPT SCANNER --}}
    @push('scripts')
    <script>
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