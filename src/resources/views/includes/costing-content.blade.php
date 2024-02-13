<div class="tab-content px-2 w-100" id="productNavtabsCosting">
    <div class="tab-pane fade show active" tabindex="0">
        
        @switch($currentSection)
        
            @case('fixed-costs')
                <livewire:product-fixedcosts :modelId="$modelId" />
                @break

            @case('cost-sheet')
                <livewire:product-costsheet-preview :modelId="$modelId" />
                @break

            @case('overhead-costs')
                @if ($model->productDecisions->factory)
                    <livewire:product-overheads :modelId="$modelId" />
                @else
                    <p class="fs-5 p-3 mb-0">
                        Overhead apportionments are not available for this product. As per product decisions table.
                    </p>
                @endif
                @break

            @case('consumables')
                @if ($model->productDecisions->factory)
                    <livewire:product-consumables :modelId="$modelId" />
                @else
                    <p class="fs-5 p-3 mb-0">
                        Consumables are not available for this product. As per product decisions table.
                    </p>
                @endif
                @break

        @endswitch
    </div>
</div>