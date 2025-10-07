<div class="space-y-4">
  @foreach($followUps as $followUp)
    <div class="flex justify-between items-center border-b py-2">
        <div>
            <div class="font-semibold">
                {{ $followUp->follow_up_date->format('d M Y') }}
            </div>
            <div class="text-sm text-gray-600">
                {{ $followUp->result }}
            </div>
        </div>
        <x-filament::button
            color="danger"
            size="sm"
            wire:click="dispatch('delete-follow-up', {{ $followUp->id }})"
        >
            Hapus
        </x-filament::button>
    </div>
@endforeach

</div>
