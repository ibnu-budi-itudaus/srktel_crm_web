<div class="space-y-4">
    <h3 class="text-lg font-bold">Data Forecast</h3>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <strong>Customer:</strong> {{ $record->customer?->name ?? '-' }}
        </div>
        <div>
            <strong>Project:</strong> {{ $record->project?->name ?? '-' }}
        </div>
        <div>
            <strong>Source:</strong> {{ $record->customer->source?->name ?? '-' }}
        </div>
        <div>
            <strong>Tanggal Dibuat:</strong> 
            {{ $record->created_at?->format('d M Y') ?? '-' }}
        </div>
        <div class="col-span-2">
            <strong>Nominal:</strong> Rp {{ number_format($record->price, 0, ',', '.') }}
        </div>
         <div>
            <strong>Status:</strong> {{ $record->status ?? '-' }}
        </div>
    </div>

    <h3 class="text-lg font-bold mt-6"><strong>Follow Ups : </strong></h3>
    @if ($record->followUps->isNotEmpty())
        <ul class="list-disc list-inside space-y-2">
            @foreach ($record->followUps as $follow)
                <li>
                    <div><strong>{{ $follow->created_at->format('d M Y H:i') }}</strong></div>
                    <div>{{ $follow->result }}</div>
                    <!-- <div>Status: {{ $follow->sale->status }}</div> -->
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-500">Belum ada follow up</p>
    @endif
</div>
