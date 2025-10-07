<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forecast Report (PDF)</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin-bottom: 15px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; font-size: 10px; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>
        Forecast Report 
        @if(request('month')) {{ \Carbon\Carbon::create()->month(request('month'))->translatedFormat('F') }} @endif
        @if(request('year')) {{ request('year') }} @endif
        @if(request('start_date') && request('end_date'))
            Periode {{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }} 
            s/d {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}
        @endif
    </h2>

    <p>
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No</th>
                <th>Nama PIC</th>
                <th>Perusahaan</th>
                <th>Proyek</th>
                <th>Nilai</th>
                <th>No HP</th>
                <th>Status</th>
                <th>Sumber</th>
                @for ($i = 1; $i <= $maxFollowUps; $i++)
                    <th>Tgl FU {{ $i }}</th>
                    <th>Hasil FU {{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $index => $sale)
                <tr>
                    <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $sale->customer->name ?? '-' }}</td>
                    <td>{{ $sale->customer->company_name ?? '-' }}</td>
                    <td>{{ $sale->project->name ?? '-' }}</td>
                    <td>Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                    <td>{{ $sale->customer->phone ?? '-' }}</td>
                    <td>{{ $sale->status }}</td>
                    <td>{{ $sale->customer->source->name ?? '-' }}</td>

                    @foreach($sale->followUps as $fu)
                        <td>{{ $fu->follow_up_date ? \Carbon\Carbon::parse($fu->follow_up_date)->format('d-m-Y') : '-' }}</td>
                        <td>{{ $fu->result ?? '-' }}</td>
                    @endforeach

                    @for ($i = $sale->followUps->count(); $i < $maxFollowUps; $i++)
                        <td>-</td>
                        <td>-</td>
                    @endfor
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 9 + ($maxFollowUps * 2) }}" style="text-align:center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
