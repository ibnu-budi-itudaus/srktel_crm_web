<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Forecast</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h2, h4 {
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        table th, table td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
        }
        table th {
            background: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>Laporan Forecast Penjualan</h2>
        <h4>SRKTel CRM</h4>
        <p>
            @if(request('filters.bulan.value'))
                Bulan: {{ \Carbon\Carbon::create()->month(request('filters.bulan.value'))->translatedFormat('F') }}
            @endif
            @if(request('filters.tahun.value'))
                Tahun: {{ request('filters.tahun.value') }}
            @endif
        </p>
        <p>
            @if(request('filters.periode.from'))
                Dari: {{ \Carbon\Carbon::parse(request('filters.periode.from'))->format('d/m/Y') }}
            @endif
            @if(request('filters.periode.until'))
                - Sampai: {{ \Carbon\Carbon::parse(request('filters.periode.until'))->format('d/m/Y') }}
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama PIC</th>
                <th>Perusahaan</th>
                <th>Proyek</th>
                <th>Nilai</th>
                <th>No HP</th>
                <th>Status</th>
                <th>Sumber</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $i => $sale)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $sale->customer->name ?? '-' }}</td>
                    <td>{{ $sale->customer->company_name ?? '-' }}</td>
                    <td>{{ $sale->project->name ?? '-' }}</td>
                    <td>Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                    <td>{{ $sale->customer->phone ?? '-' }}</td>
                    <td>{{ ucfirst($sale->status) }}</td>
                    <td>{{ $sale->customer->source->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
