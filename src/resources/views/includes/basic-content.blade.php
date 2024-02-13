<div class="tab-content px-2 w-100" id="productNavtabsBasicDetails">
    <div class="tab-pane fade show active" tabindex="0">
        @switch($currentSection)
            @case('parties')
                @if ($model->productDecisions->factory || $model->productDecisions->vendor)
                    <livewire:product-party-ledger :modelId="$modelId" />
                @else
                    <p class="fs-5 p-3 mb-0">
                        Suppliers are not available for this product. As per product decisions table.
                    </p>
                @endif
            @break

            @case('materials')
                @if ($model->productDecisions->factory)
                    <livewire:product-materials :modelId="$modelId" />
                @else
                    <p class="fs-5 p-3 mb-0">
                        Materials are not available for this product. As per product decisions table.
                    </p>
                @endif
            @break

            @case('color-options')
                @if ($model->productDecisions->factory)
                    <livewire:product-options-material :modelId="$modelId" />
                @else
                    <livewire:product-options :modelId="$modelId" />
                @endif
            @break

            @case('size-range')
                @if ($model->productDecisions->factory)
                    <livewire:product-ranges-material :modelId="$modelId" />
                @else
                    <livewire:product-ranges :modelId="$modelId" />
                @endif
            @break

            @case('stock')
                <livewire:product-stock :modelId="$modelId" />
                
            @break

            @default
        @endswitch
    </div>
</div>
