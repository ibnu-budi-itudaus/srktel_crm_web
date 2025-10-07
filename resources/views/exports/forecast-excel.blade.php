<table style="width:400px; height:150px;">
    <tr>
        <th colspan="{{ 10 + ($maxFollowUps * 2) }}" style="text-align:center; font-size:14px;">
            <strong>{{ $judul }}</strong>
        </th>
    </tr>
    <tr>
        <th><strong>Tanggal</strong></th>
        <th><strong>No</strong></th>
        <th><strong>Nama PIC</strong></th>
        <th><strong>Perusahaan</strong></th>
        <th><strong>Proyek</strong></th>
        <th><strong>Nilai</strong></th>
        <th><strong>No HP</strong></th>
        <th><strong>Status</strong></th>
        <th><strong>Sumber</strong></th>
        @for ($i = 1; $i <= $maxFollowUps; $i++)
            <th><strong>Tgl FU {{ $i }}</strong></th>
            <th><strong>Hasil FU {{ $i }}</strong></th>
        @endfor
        <th><strong>Subtotal</strong></th>
    </tr>
    @foreach($sales as $index => $sale)
        <tr style="width:400px; height:500px;">
            <td>{{ $sale->created_at->format('Y-m-d') }}</td>
            <td>{{ $index + 1 }}</td>
            <td>{{ $sale->customer->name ?? '-' }}</td>
            <td>{{ $sale->customer->company_name ?? '-' }}</td>
            <td>{{ $sale->project->name ?? '-' }}</td>
            <td>{{ $sale->price }}</td>
            <td>{{ $sale->customer->phone ?? '-' }}</td>
            <td>{{ $sale->status }}</td>
            <td>{{ $sale->customer->source->name ?? '-' }}</td>

           @for ($i = 0; $i < $maxFollowUps; $i++)
                @php
                    $fu = $sale->followUps->get($i); // aman, kalau tidak ada return null
                @endphp

                <td>
                    {{ $fu && $fu->follow_up_date 
                        ? \Carbon\Carbon::parse($fu->follow_up_date)->format('d-m-Y') 
                        : '-' }}
                </td>
                <td>
                    {{ $fu->result ?? '-' }}
                </td>
            @endfor


            <td>{{ $sale->price }}</td> {{-- subtotal = price --}}
        </tr>
    @endforeach

    {{-- Baris total --}}
    <tr>
        <td colspan="{{ 9 + ($maxFollowUps * 2) }}" style="text-align:right; font-weight:bold;">
            TOTAL
        </td>
        <td style="font-weight:bold;">{{ $total }}</td>
    </tr>
</table>

<!-- <table>
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
</table> -->
