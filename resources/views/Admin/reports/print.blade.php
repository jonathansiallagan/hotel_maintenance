<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Corrective Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                margin: 1cm;
                size: A4 landscape;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Style Border Tabel Klasik */
        table,
        th,
        td {
            border: 1px solid #000;
            border-collapse: collapse;
        }
    </style>
</head>

<body class="bg-white text-gray-900 p-4" onload="window.print()">

    {{-- KOP LAPORAN --}}
    <div class="flex items-center justify-between border-b-2 border-gray-800 pb-4 mb-6" style="border: none; border-bottom: 2px solid black;">
        <div>
            <h1 class="text-2xl font-bold uppercase tracking-wider">Marianna Resort</h1>
            <p class="text-sm text-gray-600">Laporan Kerusakan & Maintenance Aset</p>
        </div>
        <div class="text-right text-sm">
            <p>Periode:</p>
            <p class="font-bold">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
    </div>

    {{-- TABEL SESUAI GAMBAR EXCEL --}}
    <table class="w-full text-xs text-left">
        <thead class="bg-gray-200 font-bold uppercase text-center">
            <tr>
                {{-- Rowspan 2 baris --}}
                <th rowspan="2" class="px-2 py-2 align-middle w-32">Tiket</th>
                <th rowspan="2" class="px-2 py-2 align-middle">Aset</th>
                <th rowspan="2" class="px-2 py-2 align-middle w-24">Teknisi</th>

                {{-- Colspan 3 kolom untuk TIME --}}
                <th colspan="3" class="px-2 py-1 align-middle">Time</th>

                <th rowspan="2" class="px-2 py-2 align-middle w-20">Status</th>
            </tr>
            <tr>
                {{-- Sub Kolom Time --}}
                <th class="px-2 py-1 w-20">Respon</th>
                <th class="px-2 py-1 w-20">Pengerjaan</th>
                <th class="px-2 py-1 w-24">Total Downtime</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $t)
            <tr>
                {{-- TIKET (No & Tanggal) --}}
                <td class="px-2 py-2 align-top">
                    <div class="font-bold">{{ $t->ticket_number }}</div>
                    <div class="text-[10px] text-gray-600">{{ $t->created_at->format('d/m/Y') }}</div>
                </td>

                {{-- ASET (Nama & Masalah) --}}
                <td class="px-2 py-2 align-top">
                    <div class="font-bold">{{ $t->asset->name ?? '-' }}</div>
                    <div class="italic text-[10px]">{{ $t->title }}</div>
                </td>

                {{-- TEKNISI --}}
                <td class="px-2 py-2 align-top">
                    {{ $t->technician->name ?? '-' }}
                </td>

                {{-- WAKTU: Respon --}}
                <td class="px-2 py-2 align-top text-center">
                    {{ $t->started_at ? $t->created_at->diffForHumans($t->started_at, true) : '-' }}
                </td>

                {{-- WAKTU: Pengerjaan --}}
                <td class="px-2 py-2 align-top text-center">
                    @if($t->started_at && $t->completed_at)
                    {{ $t->started_at->diffForHumans($t->completed_at, true) }}
                    @else
                    -
                    @endif
                </td>

                {{-- WAKTU: Total Downtime --}}
                <td class="px-2 py-2 align-top text-center font-bold">
                    @if($t->completed_at)
                    {{ $t->created_at->diffForHumans($t->completed_at, true) }}
                    @else
                    Belum Selesai
                    @endif
                </td>

                {{-- STATUS --}}
                <td class="px-2 py-2 align-top text-center uppercase text-[10px]">
                    {{ str_replace('_', ' ', $t->status) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER TANDA TANGAN --}}
    <div class="mt-8 flex justify-end">
        <div class="text-center w-48">
            <p class="text-sm mb-12">Tuktuk, {{ now()->format('d M Y') }}</p>
            <p class="font-bold border-b border-black pb-1">Chief Engineering</p>
        </div>
    </div>
</body>

</html>