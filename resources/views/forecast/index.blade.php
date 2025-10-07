

<div class="container">
    <h2>Forecast Report</h2>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('forecast.index') }}" class="mb-4 flex gap-2">
        <input type="number" name="month" class="border p-2" placeholder="Bulan (1-12)" value="{{ request('month') }}">
        <input type="number" name="year" class="border p-2" placeholder="Tahun" value="{{ request('year') }}">
        <input type="date" name="start_date" class="border p-2" value="{{ request('start_date') }}">
        <input type="date" name="end_date" class="border p-2" value="{{ request('end_date') }}">
        <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded">Filter</button>
    </form>

    {{-- Action Buttons --}}
    <div class="mb-3">
        <a href="{{ route('forecast.print', request()->all()) }}" target="_blank" class="px-3 py-2 bg-yellow-500 text-white rounded">Print</a>
        <a href="{{ route('forecast.pdf', request()->all()) }}" class="px-3 py-2 bg-green-600 text-white rounded">Download PDF</a>
    </div>

    {{-- Tabel bisa ditampilkan langsung disini juga kalau mau --}}
   <table class="table-auto w-full border-collapse border border-gray-300">
    <thead class="bg-gray-100">
        <tr>
            <th class="border p-2">Customer</th>
            <th class="border p-2">Project</th>
            <th class="border p-2">Created At</th>
            <th class="border p-2">Follow Ups</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $sale)
            <tr>
                <td class="border p-2">{{ $sale->customer->name ?? '-' }}</td>
                <td class="border p-2">{{ $sale->project->name ?? '-' }}</td>
                <td class="border p-2">{{ $sale->created_at->format('d/m/Y') }}</td>
                <td class="border p-2">
                    {{ $sale->followUps->count() }} FU
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</div>

