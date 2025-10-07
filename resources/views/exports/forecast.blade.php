@php
    use Carbon\Carbon;

    // Grouping data per bulan (Closing Date)
    $groupedByMonth = $sales->groupBy(function ($item) {
        return Carbon::parse($item->closing_date)->format('F Y'); // contoh: "September 2025"
    });
@endphp

@foreach ($groupedByMonth as $month => $monthSales)
    <h3 style="font-weight: bold; text-align: left; margin-bottom: 10px;">
        {{-- Filter bulan & tahun --}}
        @if(!empty($bulan))
            Forecast Penjualan {{ \Carbon\Carbon::create()->locale('id')->month((int)$bulan)->translatedFormat('F') }} {{ now()->setTimezone('Asia/Jakarta')->format(' Y ') }}
        @endif
        @if(!empty($tahun))
            Forecast Penjualan {{ $tahun }}
        @endif

        {{-- Filter tanggal range --}}
        @if(!empty($startDate) && !empty($endDate))
            Forecast Penjualan Periode {{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d M Y');  }} 
            s/d {{ \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d M Y'); }}
        @endif

        @if(empty($bulan) && empty($tahun) && empty($startDate) && empty($endDate))
            Akumulasi Forecast Penjualan {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }} 
        @endif
    </h3>
<div @if ($maxFollowUps >= 6) 
        style="transform: scale(0.8); transform-origin: top left;"
    @elseif ($maxFollowUps < 5 )
        style="transform: scale(1.0); transform-origin: top left;"    
    @endif
>
    <table border="1" cellspacing="0" cellpadding="4" style="margin-bottom: 20px; width: 100%; border-collapse: collapse; margin-top: 40px;  ">
        <thead>
            <tr style="background: #d9d9d9; font-weight: bold;">
                <th>Tanggal</th>
                <th>No.</th>
                <th>Nama PIC</th>
                <th>Nama Perusahaan</th>
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
            @php
                $no = 1;
                $groupedByPIC = $monthSales->groupBy(fn($s) => $s->customer->name ?? '-');
            @endphp

            @foreach ($groupedByPIC as $pic => $picSales)
                <tr style="background: #f2f2f2; font-weight: bold;">
                    <td colspan="{{ 9 + ($maxFollowUps * 2) }}">
                        {{ $pic }}
                    </td>
                </tr>

                @foreach ($picSales as $sale)
                    <tr @if ($sale->status === 'deal')
                            style="background-color: #c6efce;"
                        @elseif($sale->status === 'pending') 
                            style="background-color: #ddebf7;" 
                        @elseif($sale->status === 'no_deal') 
                            style="background-color: #fff2cc;" 
                        @endif  
                    > 
                        <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                        <td>{{ $no++ }}</td>
                        <td>{{ $sale->customer->name ?? '-' }}</td>
                        <td>{{ $sale->customer->company_name ?? '-' }}</td>
                        <td>{{ $sale->project->name ?? '-' }}</td>
                        <td>Rp. {{ number_format($sale->price, 0, ',', '.') }}</td>
                        <td>{{ $sale->customer->phone ?? '-' }}</td>
                        <td>{{ ucfirst($sale->status) }}</td>
                        <td>{{ $sale->customer->source->name ?? '-' }}</td>

                        {{-- Follow Ups --}}
                        @for ($i = 0; $i < $maxFollowUps; $i++)
                            <td>
                                {{ isset($sale->followUps[$i]) ? Carbon::parse($sale->followUps[$i]->date)->format('d-m-Y') : '-' }}
                            </td>
                            <td>
                                {{ isset($sale->followUps[$i]) ? $sale->followUps[$i]->result : '-' }}
                            </td>
                        @endfor
                    </tr>
                @endforeach

                {{-- subtotal per PIC --}}
                <tr style="background:  #87CEFA; font-weight: bold;">
                    <td colspan="5" style="text-align: right;">Subtotal {{ $pic }}</td>
                    <td>{{ number_format($picSales->sum('price'), 0, ',', '.') }}</td>
                    <td colspan="{{ 3 + ($maxFollowUps * 2) }}"></td>
                </tr>
            @endforeach

            {{-- total per bulan --}}
            <tr style="background: #90EE90; font-weight: bold;">
                <td colspan="5" style="text-align: right;">TOTAL {{ $month }}</td>
                <td>{{ number_format($monthSales->sum('price'), 0, ',', '.') }}</td>
                <td colspan="{{ 3 + ($maxFollowUps * 2) }}"></td>
            </tr>
        </tbody>
    </table>

    <table style="width: 35%; border-collapse: collapse; margin-top: 40px;">
    <tr>
        <td colspan="5"><strong>Jumlah Prospek</strong></td>
        <td>{{ $jumlahProspek }}</td>
    </tr>
    <tr style="background-color: #c6efce;">
        <td colspan="5"><strong>Total Deal :</strong></td>
        <td>{{ $totalDeal }} ({{ $dealPercent }}%)</td>
    </tr>
    <tr style="background-color: #ddebf7;">
        <td colspan="5"><strong>Pending / Belum ada jawaban pasti</strong></td>
        <td>{{ $totalPending }} ({{ $pendingPercent }}%)</td>
    </tr>
    <tr style="background-color: #fff2cc;">
        <td colspan="5"><strong>No Deal</strong></td>
        <td>{{ $totalNoDeal }} ({{ $noDealPercent }}%)</td>
    </tr>
</table>
</div>
@endforeach
