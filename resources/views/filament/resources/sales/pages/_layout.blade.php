@props(['content'])

<x-filament::page>
    <div class="flex">
        <!-- Sidebar -->
        @include('filament.resources.sales.pages._sidebar')

        <!-- Konten utama -->
        <div class="flex-1 p-4">
            {{ $content }}
        </div>
    </div>
</x-filament::page>
