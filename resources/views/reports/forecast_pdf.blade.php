<!DOCTYPE html>
<html>
<head>
    <title>Forecast Report (PDF)</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #d9d9d9; }
        h2 { margin-bottom: 10px; text-align: center; }
        h3 { margin: 15px 0 5px; }
        .subtotal { background: #f2f2f2; font-weight: bold; }
        .grand-total { background: #c6e0b4; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Forecast Report</h2>

    @php
        $grandTotal = 0;
        $grouped = $sales->groupBy(fn($s) => $s->customer->name ?? 'Tanpa PIC');
    @endphp

    @foreach($grouped as $pic => $picSales)
        <h3>PIC: {{ $pic }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No</th>
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
                @php $subtotal = 0; @endphp
                @foreach($picSales as $index => $sale)
                    <tr>
                        <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $sale->customer->company_name ?? '-' }}</td>
                        <td>{{ $sale->project->name ?? '-' }}</td>
                        <td>Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                        <td>{{ $sale->customer->phone ?? '-' }}</td>
                        <td>{{ $sale->status }}</td>
                        <td>{{ $sale->customer->source->name ?? '-' }}</td>
                        @foreach($sale->followUps as $fu)
                            <td>{{ $fu->follow_up_date ?? '-' }}</td>
                            <td>{{ $fu->result ?? '-' }}</td>
                        @endforeach
                        @for ($i = $sale->followUps->count(); $i < $maxFollowUps; $i++)
                            <td>-</td>
                            <td>-</td>
                        @endfor
                    </tr>
                    @php $subtotal += $sale->price; @endphp
                @endforeach

                {{-- subtotal per PIC --}}
                <tr class="subtotal">
                    <td colspan="{{ 7 + ($maxFollowUps * 2) }}" style="text-align:right">
                        Subtotal {{ $pic }}
                    </td>
                    <td colspan="2">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
                @php $grandTotal += $subtotal; @endphp
            </tbody>
        </table>
    @endforeach

    {{-- total semua --}}
    <table>
        <tr class="grand-total">
            <td colspan="{{ 8 + ($maxFollowUps * 2) }}" style="text-align:right">
                GRAND TOTAL
            </td>
            <td colspan="2">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
