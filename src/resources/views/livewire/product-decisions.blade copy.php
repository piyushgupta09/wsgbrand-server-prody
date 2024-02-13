<div class="card border-0 rounded-0">

    <div class="card-header rounded-0 bg-dark py-2 d-flex justify-content-between align-items-center w-100">
        <div class="d-flex align-items-center justify-content-between w-100">
            <button class="flex-fill btn border-0 ps-0 text-white flex-fill text-start" type="button" data-bs-toggle="collapse"
                data-bs-target="#productDecisions" aria-expanded="true" aria-controls="productDecisions">
                <div class="d-flex align-items-center">
                    <i class="bi bi-chevron-down me-2"></i>
                    <span class="font-quick ls-1 fw-bold">Product Decisions</span>
                </div>
            </button>
            <button
                class="btn border-0 text-white font-quick ls-1 fw-bold text-capitalize
                {{ $decisionLocked ? 'btn-success' : 'btn-danger' }}"
                type="button" wire:click="loackDecision">
                {{ $decisionLocked ? 'Locked' : 'Pending' }}
            </button>
        </div>
    </div>

    <div class="collapse {{ $decisionLocked ? '' : 'show' }}" id="productDecisions">
        <div class="card card-body rounded-0">
            <div class="p-2 pt-0 fw-bold font-title fs-5">
                <div class="d-flex align-items-end">
                    <span>Purchase Decisions</span>
                    <span class="ps-2">[Choose Any One]</span>
                </div>
            </div>
            <div class="row g-3 mb-3">
                @foreach ($buyDecisions as $decision)
                    <div class="col-md-4">
                        <button
                            class="btn text-start w-100 {{ $decision['value'] ? 'btn-success' : 'btn-outline-dark' }}"
                            wire:click="updateDecision('{{ $decision['name'] }}')">
                            <div class="d-flex align-items-center">
                                <img src="{{ $decision['image'] }}" alt="" width="50" height="50">
                                <div class="d-flex flex-column ps-2 justify-content-center align-items-start">
                                    <span class="fw-bold text-capitalize font-title">{{ $decision['name'] }}</span>
                                    <span class="small text-capitalize">{{ $decision['label'] }}</span>
                                </div>
                            </div>
                        </button>
                    </div>
                @endforeach
            </div>
            <div class="p-2 fw-bold font-title fs-5">
                <div class="d-flex align-items-end">
                    <span>Sale Decisions</span>
                    <span class="ps-2">[Choose Atleast One]</span>
                </div>
            </div>
            <div class="row g-3">
                @foreach ($sellDecisions as $decision)
                    <div class="col-md-3">
                        <button
                            class="btn text-start w-100 {{ $decision['value'] ? 'btn-danger' : 'btn-outline-dark' }}"
                            wire:click="updateDecision('{{ $decision['name'] }}')">
                            <div class="d-flex align-items-center">
                                <img src="{{ $decision['image'] }}" alt="" width="50" height="50">
                                <div class="d-flex flex-column ps-2 justify-content-center align-items-start">
                                    <span class="fw-bold text-capitalize font-title">{{ $decision['name'] }}</span>
                                    <span class="small text-capitalize">{{ $decision['label'] }}</span>
                                </div>
                            </div>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
