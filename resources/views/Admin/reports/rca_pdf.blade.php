<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Analisis Akar Masalah (RCA)</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #0A2647;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #0A2647;
            margin-bottom: 5px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .meta-info {
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }

        .meta-row {
            display: table-row;
        }

        .meta-label {
            display: table-cell;
            font-weight: bold;
            width: 100px;
            padding: 3px 0;
        }

        .meta-value {
            display: table-cell;
            padding: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #0A2647;
        }

        .category-row {
            background-color: #e2e8f0;
            font-weight: bold;
            font-size: 13px;
        }

        .total-row {
            font-weight: bold;
            background-color: #f1f5f9;
            text-align: right;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="logo">Marianna Resort & Convention Tuktuk</div>
        <div class="title">Laporan Analisis Akar Masalah (RCA)</div>
    </div>

    <div class="meta-info">
        <div class="meta-row">
            <div class="meta-label">Periode:</div>
            <div class="meta-value">{{ $monthLabel }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Lokasi:</div>
            <div class="meta-value">{{ $locationLabel }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Total Kasus:</div>
            <div class="meta-value">{{ $totalRcaTickets }} Tiket Diselesaikan</div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Dicetak Pada:</div>
            <div class="meta-value">{{ now()->format('d F Y H:i:s') }}</div>
        </div>
    </div>

    @if (empty($rcaData))
        <p style="text-align: center; margin-top: 50px; color: #666; font-style: italic;">Tidak ada data kerusakan aset
            untuk periode ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 70%;">Akar Masalah (Root Cause)</th>
                    <th style="width: 30%; text-align: center;">Jumlah Kejadian</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rcaData as $category => $causes)
                    <tr class="category-row">
                        <td colspan="2">Kategori Aset: {{ $category }}</td>
                    </tr>
                    @php $catTotal = 0; @endphp
                    @foreach ($causes as $causeName => $count)
                        <tr>
                            <td>{{ $causeName }}</td>
                            <td style="text-align: center;">{{ $count }}</td>
                        </tr>
                        @php $catTotal += $count; @endphp
                    @endforeach
                    <tr class="total-row">
                        <td>Subtotal Kategori:</td>
                        <td style="text-align: center;">{{ $catTotal }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dicetak melalui Sistem Hotel Maintenance & Asset Management
    </div>

</body>

</html>
