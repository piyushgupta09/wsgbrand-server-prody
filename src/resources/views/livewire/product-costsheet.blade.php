<div class="border border-dark">
    
    {{-- @if ($model->decision_locked && $model->status == 'draft')     --}}
        <ul class="section-tabs nav nav-pills nav-fill" id="productSectionTabs" role="tablist">
            @foreach ($sections as $section)
                @if ($section['available'])    
                    <li class="nav-item {{ $loop->first ? '' : 'border-start' }}">
                        @if ($section['slug'] == $currentSection)
                            <a class="nav-link fw-bold font-title active"
                                data-bs-toggle="collapse" 
                                href="#productSectionTabContent" role="button" aria-expanded="false" aria-controls="productSectionTabContent">
                                {{ $section['name'] }}
                                @if ($section['required'])
                                    <i class="bi bi-record-fill text-danger ms-3"></i>
                                @endif
                            </a>
                        @else                  
                            <a class="nav-link fw-bold font-title {{ $section['slug'] == $currentSection ? 'active' : '' }}"
                                href="{{ route('products.show', ['product' => $model->slug, 'section' => $section['slug']]) }}">
                                {{ $section['name'] }}
                                @if ($section['required'])
                                    <i class="bi bi-record-fill text-danger ms-3"></i>
                                @endif
                            </a>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    {{-- @elseif ($model->status == 'live')
        <div class="alert alert-success rounded-0 mb-0 fs-5 fw-bold">
            Product is Live, changes are not allowed.
        </div>
    @else
        <div class="alert alert-danger rounded-0 mb-0 fs-5 fw-bold">
            First you need to lock the decision to create the product.
        </div>
    @endif --}}

    {{-- Section Contents --}}
    <div class="collapse show border-top" id="productSectionTabContent">
        <div class="tab-content" id="productSectionTabs">
            <div class="tab-pane fade show active" tabindex="0">
                @switch($currentSection)
                   
                    @case('fixed-costs')
                        <livewire:product-fixedcosts :modelId="$modelId" />
                        @break

                    @case('cost-sheet')
                        <livewire:product-costsheet-preview :modelId="$modelId" />
                        @break
                   
                    @case('overhead-costs')
                        @if ($model->factory)
                            <livewire:product-overheads :modelId="$modelId" />
                        @else
                            <p class="fs-5 p-3 mb-0">
                                Overhead apportionments are not available for this product. As per product decisions table.
                            </p>
                        @endif
                        @break

                    @case('consumables')
                        @if ($model->factory)
                            <livewire:product-consumables :modelId="$modelId" />
                        @else
                            <p class="fs-5 p-3 mb-0">
                                Consumables are not available for this product. As per product decisions table.
                            </p>
                        @endif
                        @break

                    @case('pricing-strategy')
                        <livewire:product-pricing :modelId="$modelId" />
                        @break

                    @default
                @endswitch
            </div>
        </div>
        <style scoped>
            .section-tabs.nav-pills .nav-item .nav-link {
                border-radius: 0px !important;
            }

            .section-tabs.nav-pills .nav-link {
                background-color: #f8f9fa;
                color: #212529;
            }

            .section-tabs.nav-pills .nav-link.active {
                color: #fff;
                background-color: #212529;
            }

            .section-tabs.nav-pills .nav-link:hover {
                color: #fff;
                background-color: #212529;
            }
        </style>
    </div>

</div>
