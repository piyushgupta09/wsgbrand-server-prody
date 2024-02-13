<div class="card border-0">

    @include('prody::includes.form-toggle-button')

    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-transition.delay.100ms>
        <form wire:submit.prevent="{{ $formType === 'create' ? 'store' : 'update' }}">
            <div class="card-body p-0 text-bg-secondary">
                <div class="row m-0 py-3">

                    {{-- Select Consumable --}}
                    <div class="col-md-4 mb-3">
                        <div class="form-floating">
                            <select class="form-select" id="selectConsumable" wire:model="consumable" required
                                aria-label="Choose nature">
                                <option value="">Select Consumable</option>
                                @foreach ($consumables as $consumable)
                                    <option value="{{ $consumable->id }}">{{ $consumable->name }}</option>
                                @endforeach
                            </select>
                            <label for="selectConsumable" class="font-quick text-dark font-normal">Select Consumable</label>

                            {{-- <small>Select the consumable</small> --}}
                            @if ($selectedConsumable)
                                <small class="ms-3 fw-bold text-warning">{{ $selectedConsumable->details }}</small>
                            @endif
                            
                            @error('consumable')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Rate --}}
                    <div class="col-md-4 mb-3">
                        <div class="form-floating">
                            <input type="text" id="processCost" class="form-control" wire:model="rate" required>
                            <label for="processCost" class="font-quick text-dark font-normal">Rate</label>
                            {{-- <small>Enter the rate of this consumable in INR.</small> --}}
                            @if ($selectedConsumable)
                                <small class="ms-3 fw-bold text-warning">{{ $selectedConsumable->unit }}</small>
                            @endif
                            @error('rate')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Consumption Rate --}}
                    <div class="col-md-4 mb-3">
                        <div class="form-floating">
                            <input type="text" id="overheadRatio" class="form-control" wire:model="ratio" step="0.1">
                            <label for="overheadRatio" class="font-quick text-dark font-normal">Consumption rate</label>
                            {{-- <small>Enter the consumption of consumable on this product</small> --}}
                            @error('ratio')
                                <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Reason --}}
                    <div class="col-12 mb-3">
                        <div class="form-floating">
                            <textarea id="overheadReason" class="form-control" wire:model="reasons"></textarea>
                            <label for="overheadReason" class="font-quick text-dark font-normal">Reason</label>
                            <small>Explain the reasons for apportioning the overhead to the product</small>
                            @error('reasons')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="btn-group w-50 ms-auto">
                        <button type="button" wire:click.prevent="resetForm()"
                            class="btn btn-outline-light">Reset</button>
                        <button type="submit" class="btn btn-info">{{ $formType === 'create' ? 'Save' : 'Update'
                            }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @if (isset($productConsumables) && $productConsumables->isNotEmpty())
        <div class="collapse show" id="productConsumableList">
            <div class="card-body p-0">
                <ul class="list-group list-group-flushed">
                    @foreach ($productConsumables as $key => $productConsumable)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="pe-2">{{ $loop->iteration }}.</span>
                                <strong>{{ $productConsumable->consumable->name }}</strong> - {{ $productConsumable->amount }}
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseProductConsumables{{ $key }}"
                                    aria-expanded="false" aria-controls="collapseProductConsumables{{ $key }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-dark" wire:click="edit({{ $productConsumable->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-dark" wire:click="delete({{ $productConsumable->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="collapse" id="collapseProductConsumables{{ $key }}">
                            <div class="card-body">
                                <p><strong>Consumable:</strong> {{ $productConsumable->consumable->name }}</p>
                                <p><strong>Cost:</strong> {{ $productConsumable->rate }} INR</p>
                                <p><strong>Ratio:</strong> {{ $productConsumable->ratio }}</p>
                                <p><strong>Reason:</strong> {{ $productConsumable->reasons }}</p>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

</div>
