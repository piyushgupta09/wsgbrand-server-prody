<div class="d-flex justify-content-start">

    @include('prody::includes.nav-sections', [
        'sections' => $decisions, 
        'currentTab' => $currentTab,
        'currentSection' => $currentSection,
        'sectionId' => 'productNavtabsDecisions'
    ])

    <div class="flex-fill tab-content ms-2" id="productNavtabsDecisions">
        <div class="tab-pane fade show active" tabindex="0">
            @switch($currentSection)

                @case('procurement')
                    @include('prody::includes.decision-card', [ 'decisions' => $buyDecisions ])
                    @break

                @case('distribution')
                    @include('prody::includes.decision-card', [ 'decisions' => $sellDecisions ])
                    @break

                @case('strategy')
                    @livewire('product-strategies', [ 'modelId' => $modelId, 'decisions' => $sellDecisions ])
                    @break

                @case('payments')
                    @include('prody::includes.decision-card', [ 'decisions' => $paymentDecisions ])
                    @break

                @case('deliveries')
                    @include('prody::includes.decision-card', [ 'decisions' => $deliveryDecisions ])
                    @break

            @endswitch
        </div>
    </div>
</div>
