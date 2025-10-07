<div style="max-height:70vh; overflow-y:auto; padding-right:12px;">
    @foreach ($followUps as $followUp)
        <div style="background-color:#f9f9f9; border-left:4px solid #9ca3af; padding:10px; margin-bottom:10px; border-radius:6px;">
            
            {{-- Kalau sedang edit --}}
            @if ($editingId === $followUp->id)
                <div class="space-y-2">
                    <input type="date" wire:model="editDate" class="border rounded p-1 w-full">
                    @error('editDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                    
                    <textarea wire:model="editResult" class="border rounded p-1 w-full"></textarea>
                     @error('editResult') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                     
                     {{-- Pilihan status --}}
                    <select wire:model="editStatus" class="border rounded p-1 w-full">
                        <option value="">-- Pilih Status --</option>
                        <option value="prospect">Prospect</option>
                        <option value="pending">Pending</option>
                        <option value="deal">Deal</option>
                        <option value="no_deal">No Deal</option>
                    </select>
                    @error('editStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    
                     <div class="flex space-x-2 mt-2">
                        <x-filament::button wire:click="updateFollowUp" size="xs" color="success" icon="heroicon-m-check">
                            Simpan
                        </x-filament::button>

                        <x-filament::button wire:click="cancelEdit" size="xs" color="gray" icon="heroicon-m-x-mark">
                            Batal
                        </x-filament::button>
                    </div>
                </div>
            @else
                {{-- Mode tampilan biasa --}}
                <p style="font-size:14px; color:#4b5563; margin:0;">
                    📅 {{ $followUp->follow_up_date->format('d M Y') }}
                </p>
                <p style="margin:4px 0;">{{ $followUp->result }}</p>

                {{-- Status badge pakai Filament --}}
                    <div style="margin:6px 0;">
                        <x-filament::badge
                            :color="match($followUp->status) {
                                'prospect' => 'warning',
                                'pending'  => 'info',
                                'deal'     => 'success',
                                'no_deal'  => 'danger',
                                default    => 'gray',
                            }"
                        >
                            {{ strtoupper($followUp->status) }}
                        </x-filament::badge>
                    </div>
                <div style="display:flex; gap:6px; margin-top:6px;">
                    <x-filament::button
                        wire:click="editFollowUp({{ $followUp->id }})"
                        size="xs"
                        color="warning"
                        icon="heroicon-m-pencil-square"
                    >
                        Edit
                    </x-filament::button>

                    <x-filament::button
                        wire:click="deleteFollowUp({{ $followUp->id }})"
                        size="xs"
                        color="danger"
                        icon="heroicon-m-trash"
                    >
                        Delete
                    </x-filament::button>
                </div>
            @endif

        </div>
    @endforeach

</div>