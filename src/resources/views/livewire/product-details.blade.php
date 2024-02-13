<div class="">

    @include('prody::includes.nav-tabs', [
        'tabs' => $decisions, 
        'currentTab' => $currentTab,
        'tabId' => 'productNavtabsDetails'
    ])

    <div class="tab-content pt-2" id="productNavtabsDetails">
        <div class="tab-pane fade show active" tabindex="0">
            @switch($currentTab)

                @case('decisions')
                    @livewire('product-decisions', ['modelId' => $model->id], key($model->id))
                    @break

                @case('costing')
                    <div class="d-flex">
                        @include('prody::includes.nav-sections', [
                            'sections' => $costingSections, 
                            'currentTab' => $currentTab,
                            'currentSection' => $currentSection,
                            'sectionId' => 'productNavtabsCosting'
                        ])
                        @include('prody::includes.costing-content')
                    </div>
                    @break

                @case('basic-details')
                    <div class="d-flex">
                        @include('prody::includes.nav-sections', [
                            'sections' => $basicSections, 
                            'currentTab' => $currentTab,
                            'currentSection' => $currentSection,
                            'sectionId' => 'productNavtabsBasicDetails'
                        ])
                        @include('prody::includes.basic-content')
                    </div>
                    @break

                @case('advance-details')
                    <div class="d-flex">
                        @include('prody::includes.nav-sections', [
                            'sections' => $advanceSections, 
                            'currentTab' => $currentTab,
                            'currentSection' => $currentSection,
                            'sectionId' => 'productNavtabsAdvanceDetails'
                        ])
                        @include('prody::includes.advance-content')
                    </div>
                    @break

            @endswitch
        </div>
    </div>
</div>
