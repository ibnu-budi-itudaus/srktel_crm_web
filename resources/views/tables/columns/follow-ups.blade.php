<ul class="list-disc list-inside text-sm text-gray-700">
    @foreach ($getRecord()->followUps as $followUp)
        <li>{{ $followUp->follow_up_date->format('d/m/Y') ?? '-' }} - {{ $followUp->result }}</li>
    @endforeach
</ul>
