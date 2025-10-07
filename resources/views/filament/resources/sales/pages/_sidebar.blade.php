<div class="w-52 bg-gray-50 border-r p-4 space-y-2">
    <x-filament::button
        tag="a"
        href="{{ \App\Filament\Resources\SalesResource::getUrl('index') }}"
        :color="request()->routeIs('filament.admin.resources.sales.index') ? 'primary' : 'gray'"
        class="w-full justify-start">
        📋 Table List
    </x-filament::button>

    <x-filament::button
        tag="a"
        href="{{ \App\Filament\Resources\SalesResource::getUrl('pipeline') }}"
        :color="request()->routeIs('filament.admin.resources.sales.pipeline') ? 'primary' : 'gray'"
        class="w-full justify-start">
        🔄 Pipeline
    </x-filament::button>
</div>
