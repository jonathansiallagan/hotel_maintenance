<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                margin: 1cm;
                size: A4;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body class="bg-white text-gray-900 p-8" onload="window.print()">

    {{-- KOP SURAT SEDERHANA --}}
    <div class="flex items-center justify-between border-b-2 border-gray-800 pb-4 mb-6">
        <div class="flex items-center gap-4">
            {{-- Logo bisa dipanggil disini jika mau --}}
            {{-- <img src="{{ asset('images/logo.png') }}" class="h-12"> --}}
            <div>
                <h1 class="text-2xl font-bold uppercase tracking-wider">Marianna Resort</h1>
                <p class="text-sm text-gray-600">Laporan Kerusakan & Maintenance Aset</p>
            </div>
        </div>
        <div class="text-right text-sm">
            <p>Periode:</p>
            <p class="font-bold">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
    </div>

    {{-- TABEL LAPORAN --}}
    <table class="w-full text-xs text-left border border-gray-300">
        <thead class="bg-gray-100 font-bold uppercase">
            <tr>
                <th class="px-3 py-2 border border-gray-300">Tanggal</th>
                <th class="px-3 py-2 border border-gray-300">Tiket</th>
                <th class="px-3 py-2 border border-gray-300">Aset</th>
                <th class="px-3 py-2 border border-gray-300">Masalah / Deskripsi</th>
                <th class="px-3 py-2 border border-gray-300">Teknisi</th>
                <th class="px-3 py-2 border border-gray-300">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $t)
            <tr>
                <td class="px-3 py-2 border border-gray-300">{{ $t->created_at->format('d/m/Y') }}</td>
                <td class="px-3 py-2 border border-gray-300">{{ $t->ticket_number ?? $t->id }}</td>
                <td class="px-3 py-2 border border-gray-300 font-bold">{{ $t->asset->name ?? '-' }}</td>
                <td class="px-3 py-2 border border-gray-300">{{ $t->description }}</td>
                <td class="px-3 py-2 border border-gray-300">{{ $t->technician->name ?? '-' }}</td>
                <td class="px-3 py-2 border border-gray-300 uppercase">{{ str_replace('_', ' ', $t->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER TTD --}}
    <div class="mt-12 flex justify-end">
        <div class="text-center w-48">
            <p class="text-sm mb-16">Tuktuk, {{ now()->format('d M Y') }}</p>
            <p class="font-bold border-b border-gray-400 pb-1">General Manager</p>
        </div>
    </div>

</body>

</html>