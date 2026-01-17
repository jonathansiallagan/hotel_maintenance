<x-app-layout :hideNav="true">

    {{-- 1. PUSH STYLES --}}
    @push('styles')
    <style>
        /* Custom Radio Button Styling */
        .category-radio:checked+label {
            background-color: #EFF6FF;
            border-color: #3B82F6;
            color: #1D4ED8;
        }

        .priority-radio:checked+label.low {
            background-color: #DCFCE7;
            border-color: #22C55E;
            color: #15803D;
        }

        .priority-radio:checked+label.normal {
            background-color: #DBEAFE;
            border-color: #3B82F6;
            color: #1E40AF;
        }

        .priority-radio:checked+label.urgent {
            background-color: #FEE2E2;
            border-color: #EF4444;
            color: #991B1B;
        }

        /* Fade In Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
    @endpush

    {{-- 2. CONTAINER UTAMA --}}
    <div class="relative max-w-md mx-auto min-h-screen flex flex-col bg-gray-50 text-gray-800 shadow-2xl font-sans pb-24">

        {{-- ALERT SUKSES --}}
        @if(session('success'))
        <div id="flash-message" class="absolute top-16 left-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl shadow-lg flex justify-between items-center animate-fade-in-up">
            <div>
                <strong class="font-bold text-sm">Berhasil!</strong>
                <span class="block text-xs sm:inline">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        @endif

        {{-- NAVBAR --}}
        <div class="bg-white shadow-sm sticky top-0 z-40 px-4 py-4 flex items-center gap-3">
            <a href="{{ route('staff.dashboard') }}" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-500 hover:text-slate-800 transition">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <h1 class="text-lg font-bold text-slate-800">Lapor Kerusakan</h1>
        </div>

        {{-- FORM CONTENT --}}
        <div class="p-4 space-y-6 flex-1 overflow-y-auto">

            {{-- Tampilkan Alert Jika Ada Error --}}
            @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl animate-fade-in-up">
                <div class="flex items-center gap-2 font-bold mb-1 text-sm">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>Gagal Mengirim Laporan</span>
                </div>
                <ul class="list-disc pl-8 text-xs">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('staff.tickets.store') }}" method="POST" enctype="multipart/form-data" id="reportForm" class="space-y-6">
                @csrf

                {{-- ========================================== --}}
                {{-- BAGIAN LOGIKA ASET (SCAN VS MANUAL)        --}}
                {{-- ========================================== --}}

                @if(isset($scannedAsset) && $scannedAsset)
                {{-- KONDISI 1: HASIL SCAN QR (TAMPILKAN KARTU) --}}
                <input type="hidden" name="asset_id" value="{{ $scannedAsset->id }}">

                <div class="bg-blue-50 rounded-xl shadow-sm border border-blue-200 p-4 animate-fade-in-up">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xs font-bold text-blue-600 uppercase tracking-wider">
                            <i class="fa-solid fa-circle-check mr-1"></i> Aset Terverifikasi
                        </h2>
                        <a href="{{ route('staff.tickets.create') }}" class="text-[10px] text-gray-500 underline hover:text-blue-600">Ganti Mode Manual</a>
                    </div>

                    <div class="flex gap-4 items-start">
                        <div class="w-14 h-14 bg-white rounded-lg border border-blue-100 flex items-center justify-center shadow-sm text-blue-500">
                            @php
                            $icon = match($scannedAsset->category->code ?? '') {
                            'CAT-ELC' => 'fa-tv',
                            'CAT-HVAC' => 'fa-fan',
                            'CAT-PLB' => 'fa-faucet',
                            'CAT-FUR' => 'fa-chair',
                            default => 'fa-box'
                            };
                            @endphp
                            <i class="fa-solid {{ $icon }} text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-base leading-tight">{{ $scannedAsset->name }}</h3>
                            <div class="mt-1 space-y-0.5">
                                <p class="text-xs text-gray-600 flex items-center gap-1.5">
                                    <i class="fa-solid fa-location-dot text-gray-400 w-3"></i>
                                    {{ $scannedAsset->location->name ?? 'Lokasi tidak set' }}
                                </p>
                                <p class="text-[10px] text-gray-500 flex items-center gap-1.5 font-mono">
                                    <i class="fa-solid fa-barcode text-gray-400 w-3"></i>
                                    {{ $scannedAsset->serial_number ?? 'No S/N' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @else
                {{-- KONDISI 2: MANUAL SELECT (TAMPILKAN DROPDOWN) --}}
                <div class="animate-fade-in-up">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Aset <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="asset_id" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-sm appearance-none bg-white shadow-sm" required>
                            <option value="" disabled selected>-- Cari Aset --</option>
                            @foreach($allAssets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->name }} ({{ $asset->location->name ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Gunakan tombol Scan QR di Dashboard untuk otomatisasi.
                    </p>
                </div>
                @endif


                {{-- PILIH JUDUL MASALAH (Hanya muncul jika commonIssues tersedia) --}}
                @if(isset($commonIssues) && count($commonIssues) > 0)
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori Masalah</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($commonIssues as $issue)
                        <input type="radio" name="title" id="cat_{{ Str::slug($issue) }}" value="{{ $issue }}"
                            class="category-radio hidden"
                            {{ old('title') == $issue ? 'checked' : '' }}>
                        <label for="cat_{{ Str::slug($issue) }}" class="px-4 py-2 border border-gray-200 rounded-full text-xs font-bold text-gray-500 bg-white cursor-pointer transition-all hover:bg-gray-50 select-none shadow-sm">
                            {{ $issue }}
                        </label>
                        @endforeach

                        <input type="radio" name="title" id="cat_manual" value="Lainnya" class="category-radio hidden">
                        <label for="cat_manual" class="px-4 py-2 border border-gray-200 rounded-full text-xs font-bold text-gray-500 bg-white cursor-pointer transition-all hover:bg-gray-50 select-none shadow-sm">
                            Lainnya
                        </label>
                    </div>
                </div>
                @else
                {{-- Jika Manual Mode, default title 'Lainnya' atau input manual --}}
                <input type="hidden" name="title" value="Lainnya">
                @endif

                {{-- PILIH PRIORITAS --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Prioritas <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-3">
                        {{-- Low --}}
                        <input type="radio" name="priority" id="prio_low" value="low" class="priority-radio hidden" required>
                        <label for="prio_low" class="low flex flex-col items-center justify-center p-3 border border-gray-200 rounded-xl cursor-pointer bg-white hover:bg-gray-50 transition shadow-sm">
                            <i class="fa-regular fa-face-smile mb-1 text-xl text-green-500"></i>
                            <span class="text-xs font-bold">Low</span>
                        </label>

                        {{-- Normal --}}
                        <input type="radio" name="priority" id="prio_medium" value="medium" class="priority-radio hidden" checked>
                        <label for="prio_medium" class="normal flex flex-col items-center justify-center p-3 border border-gray-200 rounded-xl cursor-pointer bg-white hover:bg-gray-50 transition shadow-sm">
                            <i class="fa-regular fa-face-meh mb-1 text-xl text-blue-500"></i>
                            <span class="text-xs font-bold">Normal</span>
                        </label>

                        {{-- Urgent --}}
                        <input type="radio" name="priority" id="prio_high" value="high" class="priority-radio hidden">
                        <label for="prio_high" class="urgent flex flex-col items-center justify-center p-3 border border-gray-200 rounded-xl cursor-pointer bg-white hover:bg-gray-50 transition shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation mb-1 text-xl text-red-500"></i>
                            <span class="text-xs font-bold">Urgent</span>
                        </label>
                    </div>
                </div>

                {{-- DESKRIPSI --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Tambahan</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-sm resize-none shadow-sm" placeholder="Ceritakan detail masalahnya...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- UPLOAD FOTO --}}
                <div class="pb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Bukti Foto <span class="text-red-500">*</span></label>

                    <div class="relative group">
                        <input type="file" name="photo_evidence_before" id="photoInput" accept="image/*" capture="environment" class="hidden" onchange="previewImage(event)">

                        <div id="uploadPlaceholder" class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 p-6 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-gray-100 transition-colors" onclick="document.getElementById('photoInput').click()">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-3 text-blue-600">
                                <i class="fa-solid fa-camera text-xl"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-900">Ambil Foto</p>
                            <p class="text-[10px] text-gray-500 mt-1">Ketuk untuk membuka kamera</p>
                        </div>

                        <div id="imagePreviewContainer" class="hidden relative rounded-xl overflow-hidden shadow-md border border-gray-200">
                            <img id="imagePreview" src="" alt="Preview" class="w-full h-48 object-cover">
                            <button type="button" onclick="retakePhoto()" class="absolute bottom-3 right-3 bg-white/90 backdrop-blur text-gray-800 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm hover:bg-white flex items-center gap-2 border border-gray-200">
                                <i class="fa-solid fa-rotate-right"></i> Ulangi
                            </button>
                        </div>
                    </div>
                    @error('photo_evidence_before') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- FOOTER BUTTON --}}
                <div class="fixed bottom-0 w-full max-w-md bg-white border-t border-gray-200 p-4 shadow-[0_-5px_15px_rgba(0,0,0,0.05)] z-40" style="left: 50%; transform: translateX(-50%);">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-blue-600/30 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <span>KIRIM LAPORAN</span>
                        <i class="fa-regular fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. PUSH SCRIPT --}}
    @push('scripts')
    <script>
        const photoInput = document.getElementById('photoInput');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    uploadPlaceholder.classList.add('hidden');
                    imagePreviewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        function retakePhoto() {
            photoInput.value = '';
            uploadPlaceholder.classList.remove('hidden');
            imagePreviewContainer.classList.add('hidden');
        }
    </script>
    @endpush

</x-app-layout>