<!DOCTYPE html>
<html>
<head>
    <title>Forecast Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #d9d9d9; }
        h3 { margin: 20px 0 5px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 15px;">
    <button onclick="window.print()">🖨️ Print</button>
</div>

@include('exports.forecast', [
    'sales' => $sales,
    'maxFollowUps' => $maxFollowUps,
    'bulan' => $bulan,
    'tahun' => $tahun,
    'startDate' => $startDate,
    'endDate' => $endDate,
    'jumlahProspek' => $jumlahProspek,
    'totalDeal'     => $totalDeal,
    'dealPercent'   => $dealPercent,
    'totalNoDeal'   => $totalNoDeal,
    'noDealPercent' => $noDealPercent,
    'totalPending'  => $totalPending,
    'pendingPercent'=> $pendingPercent,
    ])

</body>
</html>
