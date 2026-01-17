<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR - {{ $asset->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">

    {{-- Kartu QR Code --}}
    <div class="bg-white p-8 rounded-xl shadow-lg border-2 border-gray-800 w-80 text-center relative print:shadow-none print:border-2 print:w-full print:h-full print:absolute print:top-0 print:left-0">

        {{-- Header --}}
        <div class="mb-4 border-b-2 border-gray-100 pb-2">
            <h1 class="text-xl font-bold text-gray-800 uppercase leading-none">Marianna Resort</h1>
            <p class="text-[10px] text-gray-500 font-bold tracking-widest mt-1">ASSET MANAGEMENT</p>
        </div>

        {{-- QR Image Wrapper --}}
        <div class="flex justify-center my-4">

            {{-- Container Relative: Agar elemen di dalamnya bisa ditumpuk --}}
            <div class="relative inline-block">

                {{-- 1. QR Code (SVG) --}}
                {{-- Ini dirender langsung sebagai grafik vector --}}
                <div class="w-45 h-45">
                    {!! $qrCode !!}
                </div>

                {{-- 2. Logo (Overlay/Tumpukan) --}}
                {{-- Logo ini "melayang" di atas QR Code --}}
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white p-1 rounded-full shadow-sm">
                    {{-- Pastikan path gambar logo benar --}}
                    <img src="{{ asset('images/Logo_Marianna.png') }}"
                        class="w-12 h-12 object-contain"
                        alt="Logo">
                </div>

            </div>
        </div>

        {{-- Detail Aset --}}
        <h2 class="text-lg font-bold text-black mb-1">{{ $asset->name }}</h2>
        <p class="text-sm text-gray-500 font-mono">{{ $asset->serial_number ?? 'No S/N' }}</p>

        <div class="mt-4 pt-3 border-t-2 border-gray-100">
            <p class="text-[10px] text-gray-400">Scan untuk melaporkan kerusakan</p>
        </div>

        {{-- Logo Watermark (Opsional, teks saja biar ringan) --}}
        <div class="absolute top-2 right-2 opacity-10">
            <i class="fa-solid fa-qrcode text-4xl"></i>
        </div>
    </div>

    {{-- Tombol Print (Hilang saat diprint) --}}
    <div class="mt-8 flex gap-4 no-print">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-full font-bold shadow-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-print"></i> Cetak Sekarang
        </button>
        <a href="{{ route('admin.assets.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-full font-bold hover:bg-gray-300 transition">
            Kembali
        </a>
    </div>

    {{-- Font Awesome untuk Ikon --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</body>

</html>