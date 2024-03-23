<div class="tab-content px-2 w-100" id="productNavtabsCosting">
    <div class="tab-pane fade show active" tabindex="0">
        
        @switch($currentSection)
        
            @case('attributes')
                <livewire:product-attributes :modelId="$modelId" />
                @break

            @case('measurments')
                <livewire:product-measurements :modelId="$modelId" />
                @break

            @case('collections')
                <livewire:product-collections :modelId="$modelId" />
                @break

        @endswitch
    </div>
</div>