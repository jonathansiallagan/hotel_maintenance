<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor Kerusakan - HMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
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
    </style>
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen pb-24 font-sans">

    {{-- Alert Sukses (Session Laravel) --}}
    @if(session('success'))
    <div id="flash-message" class="fixed top-4 left-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-lg">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
            </svg>
        </span>
    </div>
    @endif

    {{-- Navbar --}}
    <div class="bg-white shadow-sm sticky top-0 z-10">
        <div class="max-w-md mx-auto px-4 py-3 flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-800">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            <h1 class="text-lg font-bold text-gray-800">Lapor Kerusakan</h1>
        </div>
    </div>

    <div class="max-w-md mx-auto p-4 space-y-6">

        {{-- FORM UTAMA --}}
        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" id="reportForm" class="space-y-6">
            @csrf

            {{-- 1. PILIH ASET (Dynamic Dropdown pengganti Card Static sementara) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi / Aset <span class="text-red-500">*</span></label>
                <select name="asset_id" required class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-gray-50">
                    <option value="" disabled selected>-- Pilih Aset --</option>
                    @foreach($assets as $asset)
                    <option value="{{ $asset->id }}">
                        {{ $asset->name }} - {{ $asset->location->name ?? 'Unknown' }}
                    </option>
                    @endforeach
                </select>
                @error('asset_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- 2. JUDUL (Kategori Masalah sebagai Judul) --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Masalah <span class="text-red-500">*</span></label>
                <div class="flex flex-wrap gap-2">
                    @php $issues = ['AC Bocor', 'Tidak Dingin', 'Berisik', 'Mati Total', 'Lampu Mati', 'Air Mampet']; @endphp
                    @foreach($issues as $issue)
                    <input type="radio" name="title" id="cat_{{ Str::slug($issue) }}" value="{{ $issue }}" class="category-radio hidden" required>
                    <label for="cat_{{ Str::slug($issue) }}" class="px-4 py-2 border border-gray-200 rounded-full text-sm font-medium text-gray-600 bg-white cursor-pointer transition-all hover:bg-gray-50 select-none">
                        {{ $issue }}
                    </label>
                    @endforeach
                </div>
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- 3. PRIORITAS --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Prioritas <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-3">
                    <input type="radio" name="priority" id="prio_low" value="low" class="priority-radio hidden" required>
                    <label for="prio_low" class="low flex flex-col items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer bg-white">
                        <i class="fa-regular fa-face-smile mb-1 text-lg"></i>
                        <span class="text-xs font-bold">Low</span>
                    </label>

                    <input type="radio" name="priority" id="prio_medium" value="medium" class="priority-radio hidden" checked>
                    <label for="prio_medium" class="normal flex flex-col items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer bg-white">
                        <i class="fa-regular fa-face-meh mb-1 text-lg"></i>
                        <span class="text-xs font-bold">Normal</span>
                    </label>

                    <input type="radio" name="priority" id="prio_high" value="high" class="priority-radio hidden">
                    <label for="prio_high" class="urgent flex flex-col items-center justify-center p-3 border border-gray-200 rounded-lg cursor-pointer bg-white">
                        <i class="fa-solid fa-triangle-exclamation mb-1 text-lg"></i>
                        <span class="text-xs font-bold">Urgent</span>
                    </label>
                </div>
            </div>

            {{-- 4. DESKRIPSI --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none text-sm resize-none" placeholder="Ceritakan detail masalahnya...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- 5. UPLOAD FOTO --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Bukti Foto <span class="text-red-500">*</span></label>

                <div class="relative group">
                    {{-- Input File --}}
                    <input type="file" name="photo_evidence_before" id="photoInput" accept="image/*" capture="environment" class="hidden" onchange="previewImage(event)">

                    {{-- Placeholder --}}
                    <div id="uploadPlaceholder" class="border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 p-6 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-gray-100 transition-colors" onclick="document.getElementById('photoInput').click()">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-3 text-blue-600">
                            <i class="fa-solid fa-camera text-xl"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Ambil Foto</p>
                    </div>

                    {{-- Preview --}}
                    <div id="imagePreviewContainer" class="hidden relative rounded-xl overflow-hidden shadow-md border border-gray-200 mt-2">
                        <img id="imagePreview" src="" alt="Preview" class="w-full h-48 object-cover">
                        <button type="button" onclick="retakePhoto()" class="absolute bottom-3 right-3 bg-white/90 backdrop-blur text-gray-800 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm hover:bg-white flex items-center gap-2">
                            <i class="fa-solid fa-rotate-right"></i> Ulangi
                        </button>
                    </div>
                </div>
                @error('photo_evidence_before') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- FOOTER BUTTON --}}
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg">
                <div class="max-w-md mx-auto">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <span>KIRIM LAPORAN</span>
                        <i class="fa-regular fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Javascript Sederhana untuk Preview Gambar --}}
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
</body>

</html>