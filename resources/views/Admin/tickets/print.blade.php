<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket #{{ $ticket->id }} - Hotel Maintenance</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 20px;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.3;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 15px;
            font-size: 11px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #D0BBB8;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #D0BBB8;
            margin-bottom: 8px;
        }

        .title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-open {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-in_progress {
            background: #dbeafe;
            color: #2563eb;
        }

        .status-pending_sparepart {
            background: #fef3c7;
            color: #d97706;
        }

        .status-resolved {
            background: #d1fae5;
            color: #059669;
        }

        .status-closed {
            background: #f3f4f6;
            color: #374151;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #D0BBB8;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
            margin-bottom: 10px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 10px 5px 0;
            width: 120px;
            vertical-align: top;
            font-size: 10px;
        }

        .info-value {
            display: table-cell;
            padding: 5px 0;
            font-size: 10px;
        }

        .activity-item {
            border-left: 2px solid #D0BBB8;
            padding-left: 10px;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .activity-user {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 10px;
        }

        .activity-time {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
        }

        .activity-desc {
            font-size: 9px;
        }

        .sparepart-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .sparepart-table th,
        .sparepart-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
            font-size: 9px;
        }

        .sparepart-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .sparepart-table .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #D0BBB8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        @media screen {
            .print-button {
                display: block;
            }
        }

        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>

<body>

    <button onclick="window.print()" class="print-button no-print">Cetak</button>

    <div class="header">
        <div class="logo">Hotel Maintenance System</div>
        <div class="title">Detail Tiket #{{ $ticket->id }}</div>
        <div class="status status-{{ $ticket->status }}">
            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Informasi Tiket</div>

        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">ID Tiket:</div>
                <div class="info-value">#{{ $ticket->id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Judul:</div>
                <div class="info-value">{{ $ticket->title }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Dibuat:</div>
                <div class="info-value">{{ $ticket->created_at->format('d F Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Deskripsi:</div>
                <div class="info-value">{{ $ticket->description }}</div>
            </div>
            @if($ticket->technician_note)
            <div class="info-row">
                <div class="info-label">Catatan Teknisi:</div>
                <div class="info-value">{{ $ticket->technician_note }}</div>
            </div>
            @endif
            @if($ticket->status === 'resolved' && $ticket->root_cause)
            <div class="info-row">
                <div class="info-label">Akar Masalah (RCA):</div>
                <div class="info-value" style="font-weight: bold;">{{ $ticket->root_cause }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Informasi Pelapor</div>

        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nama:</div>
                <div class="info-value">{{ $ticket->user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $ticket->user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Role:</div>
                <div class="info-value">{{ ucfirst($ticket->user->role) }}</div>
            </div>
        </div>
    </div>

    @if($ticket->asset)
    <div class="section">
        <div class="section-title">Informasi Aset</div>

        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nama Aset:</div>
                <div class="info-value">{{ $ticket->asset->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Serial Number:</div>
                <div class="info-value">{{ $ticket->asset->serial_number ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kategori:</div>
                <div class="info-value">{{ $ticket->asset->category->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Lokasi:</div>
                <div class="info-value">{{ $ticket->asset->location->name ?? '-' }}</div>
            </div>
        </div>
    </div>
    @endif

    @if($ticket->technician)
    <div class="section">
        <div class="section-title">Teknisi Ditugaskan</div>

        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nama:</div>
                <div class="info-value">{{ $ticket->technician->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $ticket->technician->email }}</div>
            </div>
        </div>
    </div>
    @endif

    @if($ticket->spareparts && $ticket->spareparts->count() > 0)
    <div class="section">
        <div class="section-title">Sparepart yang Digunakan</div>

        <table class="sparepart-table">
            <thead>
                <tr>
                    <th>Nama Sparepart</th>
                    <th>Quantity</th>
                    <th>Harga per Unit</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totalCost = 0; @endphp
                @foreach($ticket->spareparts as $sparepart)
                <tr>
                    <td>{{ $sparepart->name }}</td>
                    <td>{{ $sparepart->pivot->quantity }} {{ $sparepart->unit }}</td>
                    <td>Rp {{ number_format($sparepart->price_per_unit, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($sparepart->pivot->quantity * $sparepart->price_per_unit, 0, ',', '.') }}</td>
                </tr>
                @php $totalCost += $sparepart->pivot->quantity * $sparepart->price_per_unit; @endphp
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Total Biaya Sparepart:</td>
                    <td>Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    @if($ticket->activities && $ticket->activities->count() > 0)
    <div class="section">
        <div class="section-title">Riwayat Aktivitas</div>

        @foreach($ticket->activities as $activity)
        <div class="activity-item">
            <div class="activity-user">{{ $activity->user->name ?? 'Sistem' }}</div>
            <div class="activity-time">{{ $activity->created_at->format('d F Y H:i') }}</div>
            <div class="activity-desc">{{ $activity->description }}</div>
        </div>
        @endforeach
    </div>
    @endif

    <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #eee; text-align: center; font-size: 9px; color: #666;">
        Dicetak pada: {{ now()->format('d F Y H:i:s') }}
    </div>

</body>

</html>