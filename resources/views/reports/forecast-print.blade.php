<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forecast Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin-bottom: 15px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 40px;   table-layout: auto;  /*font-size: 11px; */ }
         table3{ width: 100%; border-collapse: collapse; margin-top: 40px;   table-layout: fixed;  /*font-size: 11px; */ }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; font-size: 11px; word-wrap: break-word; white-space: normal;}
        th { background: #f2f2f2; }
        .table2 { width: 35%; border-collapse: collapse; margin-top: 40px; }
        th.no-col, td.no-col { width: 60px !important; /*text-align: center; */ max-width: 60px !important; }
        th.fu-col, td.fu-col { /*width: 130px !important;*/ /*max-width: 130px !important;*/ white-space: normal; word-wrap: break-word;}
       td.fu-col-1 { height: 220px; max-height: 220px; width: 45px !important; max-width: 45px !important; white-space: normal; word-wrap: break-word; border-collapse: collapse;}
        th.fu-col-th{  /*width: 45px !important; max-width: 45px !important;*/ white-space: normal; word-wrap: break-word; border-collapse: collapse;}
 </style>
    <!-- <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #d9d9d9; }
        h3 { margin: 20px 0 5px; }
        @media print {
            .no-print { display: none; }
        }
    </style> -->
</head>
<body>

<!-- <div class="no-print" style="margin-bottom: 15px;">
    <button onclick="window.print()">🖨️ Print</button>
</div> -->
    <h2>

        
        {{-- Filter bulan & tahun --}}
        @if(!empty($bulan))
            Forecast Penjualan {{ \Carbon\Carbon::create()->locale('id')->month((int)$bulan)->translatedFormat('F') }} 
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
    </h2>

    <p>
       Dicetak pada: {{ now()->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
    </p>

    

<div @if ($maxFollowUps >= 6) 
        style="transform: scale(0.7); transform-origin: top left;"
    @elseif ($maxFollowUps >= 2 || $maxFollowUps <= 4 )
        style="transform: scale(1.0); transform-origin: top left;"
     @elseif ($maxFollowUps == 5 )
        style="transform: scale(0.9); transform-origin: top left;"    
    @endif
>
    <table border="1" cellspacing="0" cellpadding="5" width="100%"
     @if ($maxFollowUps>=6)
         class = "table3"
    @elseif ($maxFollowUps < 6)
         class =""
    @endif
    >


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
            <th style="70px;">Tgl FU {{ $i }}</th>
            <th @if($maxFollowUps >= 3)  
                                class="fu-col-1"
                            @elseif ($maxFollowUps <= 3)  
                                class="fu-col"
                            @endif
            >
            Hasil FU {{ $i }}
        </th>
        @endfor
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $index => $sale)
                <tr @if($sale->status === 'deal')
                        style="background-color: #c6efce;"    
                     @elseif($sale->status === 'pending')
                        style="background-color:#ddebf7;"
                    @elseif($sale->status === 'no_deal')
                        style="background-color:#fff2cc;"
                    @endif
                >
                    <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $sale->customer->name ?? '-' }}</td>
                    <td>{{ $sale->customer->company_name ?? '-' }}</td>
                    <td>{{ $sale->project->name ?? '-' }}</td>
                    <td>Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                    <td>{{ $sale->customer->phone ?? '-' }}</td>
                    <td>{{ $sale->status }}</td>
                    <td>{{ $sale->customer->source->name ?? '-' }}</td>

                    {{-- isi follow up --}}
                    @foreach($sale->followUps as $fu)
                        <td>{{ $fu->follow_up_date ? \Carbon\Carbon::parse($fu->follow_up_date)->format('d-m-Y') : '-' }}</td>
                        <td @if($maxFollowUps >= 3)  
                                class="fu-col-1"
                            @elseif ($maxFollowUps <= 3)  
                                class="fu-col"
                            @endif
                        >
                            {{ $fu->result ?? '-' }}
                        </td>
                    @endforeach

                    {{-- kosongkan sisa kolom FU --}}
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
</div>

    <table class="table2">
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



</body>
</html>