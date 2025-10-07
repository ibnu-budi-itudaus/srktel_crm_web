@php
    // Debugging - hapus komentar untuk melihat nilai
    /*
    dump('Active Tab in View: ' . $activeTab);
    dump('List URL in View: ' . $listUrl);
    dump('Pipeline URL in View: ' . $pipelineUrl);
    */
@endphp

<div class="w-full">
    <x-filament::tabs>
        <x-filament::tabs.item 
            :href="$listUrl" 
            :active="$activeTab === 'list'"
            tag="a"
        >
            List
        </x-filament::tabs.item>
        
        <x-filament::tabs.item 
            :href="$pipelineUrl" 
            :active="$activeTab === 'pipeline'"
            tag="a"
        >
            Pipeline
        </x-filament::tabs.item>
    </x-filament::tabs>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Fix tabs alignment on page load
        const fixTabsAlignment = () => {
            const tabsContainers = document.querySelectorAll(".filament-tabs");
            
            tabsContainers.forEach(container => {
                // Force left alignment
                container.style.justifyContent = "flex-start";
                
                // Ensure all tab items are left-aligned
                const tabItems = container.querySelectorAll(".filament-tabs-item");
                tabItems.forEach(item => {
                    item.style.justifyContent = "flex-start";
                    item.style.textAlign = "left";
                    // Fix padding yang tidak konsisten
                    item.style.paddingRight = "1rem";
                    item.style.paddingLeft = "1rem";
                });
            });
        };
        
        // Initial fix
        fixTabsAlignment();
        
        // Fix after Livewire updates
        if (typeof Livewire !== "undefined") {
            Livewire.hook("message.processed", () => {
                setTimeout(fixTabsAlignment, 100);
            });
            
            Livewire.hook("navigated", () => {
                setTimeout(fixTabsAlignment, 100);
            });
        }
    });
</script>
@endpush