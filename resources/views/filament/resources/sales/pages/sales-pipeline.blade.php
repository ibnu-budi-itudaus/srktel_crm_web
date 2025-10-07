<x-filament::page>
    {{-- Tombol tambah otomatis dari getHeaderActions() --}}
    
    <div class="mb-4">
        <input 
            type="text" 
            id="kanban-search" 
            placeholder="Cari project atau customer..." 
            class="w-full p-2 border rounded-lg focus:ring focus:ring-primary-300"
        >
    </div>

    <div class="flex overflow-x-auto pb-2">
        <div class="flex gap-4">
            @foreach (['prospect','pending','deal','no_deal'] as $status)
                 @php 
                    $count = $statuses->where('status', $status)->count();
                    $pendingLimit = 10;
                @endphp
                
                <div class="kanban-column w-[280px] flex-shrink-0 bg-white rounded-xl shadow-sm border p-3 flex flex-col">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-semibold text-gray-700 capitalize">{{ str_replace('_', ' ', $status) }}</h2>
                        
                        {{-- Badge --}}
                        @if ($status === 'pending')
                            <x-filament::badge :color="$count > $pendingLimit ? 'danger' : 'warning'"  :title="$count > $pendingLimit ?
        '⚠️ Melebihi limit, segera follow up sebelum menumpuk' 
        : null">
                                {{ $count }}/{{ $pendingLimit }}
                            </x-filament::badge>
                        @else

                        <x-filament::badge :color="match($status) {
                            'prospect' => 'info',
                            'pending' => 'warning',
                            'deal' => 'success',
                            'no_deal' => 'danger',
                            default => 'gray'
                        }"
                        
                        
                        >
                            {{ $statuses->where('status', $status)->count() }}
                        </x-filament::badge>
                        @endif
                    </div>

                    {{-- Items --}}
                    <div class="kanban-items flex-1 space-y-3 min-h-[120px] max-h-[70vh] overflow-y-auto pr-1" id="{{ $status }}">
                        @forelse ($statuses->where('status', $status) as $sale)
                            <div class="kanban-item"
                                data-id="{{ $sale->id }}"
                                data-search="{{ strtolower($sale->project?->name . ' ' . ($sale->customer->name ?? '')) }}">
                                <div class="bg-gray-50 border rounded-lg p-3 hover:shadow-md transition cursor-grab relative">
                                    <div class="flex items-center gap-2 font-medium text-gray-800">
                                        <x-filament::icon icon="heroicon-o-briefcase" class="w-5 h-5 text-primary-600"/>
                                        <span>{{ $sale->project?->name ?? 'No Project' }}</span>
                                    </div>

                                    <hr class="my-2">

                                    <div class="text-sm text-gray-600">
                                        👤 {{ $sale->customer->name ?? 'No Customer' }}
                                    </div>
                                    <div class="text-sm font-semibold text-emerald-600">
                                        💰 Rp {{ number_format($sale->price, 0, ',', '.') }}
                                    </div>


                                    {{-- Badge jumlah follow up --}}
                                    <div class="mx-1">
                                  <x-filament::button
                                        wire:click="openFollowUpTimeline({{ $sale->id }})"
                                        class="mt-2 px-2 py-1 text-xs rounded-3  
                                            {{ $sale->followUps->count() > 0 ? 'bg-blue-500 text-white hover:bg-blue-400' : 'bg-gray-300 text-gray-700 hover:bg-gray-200' }}"
                                         tooltip="Timeline Follow Up"
                                            >
                                        🔔 {{ $sale->followUps->count() }} FU
                                    </x-filament::button>
                                    </div>
                                     {{-- Badge Follow Up --}}
                                      {{-- Badge Follow Up --}}
                                   
                                   <div class="flex items-center gap-2 mt-2">
                                     <x-filament::button
                                        wire:click="editSale({{ $sale->id }})"
                                        size="sm"
                                        color="primary"
                                         icon="heroicon-s-pencil-square"
                                    >
                                        Edit
                                    </x-filament::button>

                                     {{-- Tombol tambah follow up --}}
                                    <x-filament::button
                                        wire:click="openFollowUpModal({{ $sale->id }})"
                                        size="sm"
                                        color="success" 
                                        class="mt-1"
                                         icon="heroicon-s-phone"
                                    >
                                     Follow Up
                                    </x-filament::button>
                                     @if ($status === 'deal' || $status ==='no_deal')
                                    <x-filament::button
                                        color="danger" 
                                        size="sm"
        
                                        icon="heroicon-o-archive-box"
                                        wire:click="archiveSale({{ $sale->id }})"
                                        tooltip="Arsipkan"
                                    />
                                    
                                    @endif
                                   </div>
                                </div>
                            </div>
                        @empty
                            <p class="empty text-sm text-gray-400 italic text-center py-4">
                                Tidak ada data
                            </p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
  
    <x-filament::modal id="edit-sale">
         <x-slot name="heading">Edit Deal</x-slot>

        <form wire:submit.prevent="updateSale">
            {{ $this->form }}

            <x-filament::button type="submit" color="primary">
                Simpan
            </x-filament::button>
        </form>
    </x-filament::modal>

       {{-- Modal follow up --}}
    {{-- Modal follow up --}}
<x-filament::modal id="open-follow-up-modal">
    <x-slot name="heading">Tambah Follow Up : {{ $followUpSale?->project?->name }} - {{ $followUpSale?->customer?->name }}</x-slot>

    <form wire:submit.prevent="saveFollowUp" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Follow Up</label>
            <input
                type="date"
                wire:model.defer="followUpData.follow_up_date"
                class="mt-1 block w-full border rounded p-2"
                required
            />
            @error('followUpData.follow_up_date') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Catatan</label>
            <textarea
                wire:model.defer="followUpData.result"
                rows="4"
                class="mt-1 block w-full border rounded p-2"
                required
            ></textarea>
            @error('followUpData.result') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
             <select wire:model.defer="followUpData.status" class="border rounded p-1 w-full">
                        <option value="">-- Pilih Status --</option>
                        <option value="prospect">Prospect</option>
                        <option value="pending">Pending</option>
                        <option value="deal">Deal</option>
                        <option value="no_deal">No Deal</option>
                    </select>
                    @error('followUpData.status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end">
            <x-filament::button type="submit" color="success">Simpan</x-filament::button>
        </div>
    </form>
</x-filament::modal>

<x-filament::modal id="follow-up-timeline-modal" width="lg">
    <x-slot name="heading">
        Timeline Follow Up : {{ $timelineSale?->project?->name }} - {{ $timelineSale?->customer?->name }}
    </x-slot>

    @if ($timelineSale)
        <div class="space-y-4 max-h-96 overflow-y-auto">
            @forelse ($timelineSale->followUps as $followUp)
                <div class="p-3 border-l-4 border-blue-500 bg-gray-50 rounded">
                    <p class="text-sm text-gray-600">
                        📅 {{ $followUp->follow_up_date->format('d M Y') }}
                    </p>
                    <p class="mt-1 text-sm">{{ $followUp->result }}</p>

                     {{-- Status badge pakai Filament --}}
                    <div class="mt-1 mb-1">
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
                </div>
                
                 <div class="mt-2 flex gap-2">
                        {{-- Tombol Edit --}}
                        <x-filament::button
                            size="xs"
                            color="warning"
                            icon="heroicon-s-pencil-square"
                            wire:click="editFollowUp({{ $followUp->id }})"
                        >
                            Edit
                        </x-filament::button>

                        {{-- Tombol Delete --}}
                        <x-filament::button
                            size="xs"
                            color="danger"
                            icon="heroicon-s-trash"
                            wire:click="deleteFollowUp({{ $followUp->id }})"
                        >
                            Delete
                        </x-filament::button>
                    </div>
                   
            @empty
                <p class="text-gray-500 italic">Belum ada follow up.</p>
            @endforelse
        </div>
    @endif
</x-filament::modal>

<x-filament::modal id="edit-follow-up-modal">
    <x-slot name="heading">Edit Follow Up</x-slot>

    <form wire:submit.prevent="updateFollowUp" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Follow Up</label>
            <input
                type="date"
                wire:model.defer="followUpData.follow_up_date"
                class="mt-1 block w-full border rounded p-2"
                required
            />
            @error('followUpData.follow_up_date') 
                <span class="text-sm text-red-600">{{ $message }}</span> 
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Catatan</label>
            <textarea
                wire:model.defer="followUpData.result"
                rows="4"
                class="mt-1 block w-full border rounded p-2"
                required
            ></textarea>
            @error('followUpData.result') 
                <span class="text-sm text-red-600">{{ $message }}</span> 
            @enderror
        </div>

          {{--  Status --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select 
                wire:model.defer="followUpData.status"
                class="mt-1 block w-full border rounded p-2"
                required
            >
                <option value="prospect">Prospect</option>
                <option value="pending">Pending</option>
                <option value="deal">Deal</option>
                <option value="no_deal">No Deal</option>
            </select>
            @error('followUpData.status') 
                <span class="text-sm text-red-600">{{ $message }}</span> 
            @enderror
        </div>


        <div class="flex justify-end">
            <x-filament::button type="submit" color="primary">Update</x-filament::button>
        </div>
    </form>
</x-filament::modal>



</x-filament::page>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom2.css') }}">
@endpush

@push('scripts')
    @vite(['resources/js/app.js'])
@endpush

@push('styles')
    @vite(['resources/css/app.css'])
@endpush

  