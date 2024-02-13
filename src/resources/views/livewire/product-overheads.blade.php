<div class="card border-0">

    @include('prody::includes.form-toggle-button')

    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-transition.delay.100ms>
        <form wire:submit.prevent="{{ $formType === 'create' ? 'store' : 'update' }}">
            <div class="card-body p-0 text-bg-secondary">
                <div class="row m-0 py-3">
                    <p class="font-robot ls-1">Overhead Apportionment</p>

                    {{-- Select Overhead Stage --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <select class="form-select" id="selectOverheadStage" wire:model="overhead_stage" required
                                aria-label="Choose Overhead Stage">
                                <option value="">Select Overhead Stage</option>
                                @foreach ($overheadStages as $overheadStage)
                                <option value="{{ $overheadStage['stage'] }}">{{ $overheadStage['stage'] }}</option>
                                @endforeach
                            </select>
                            <label for="selectOverheadStage" class="font-quick text-dark font-normal">Select Overhead
                                Stage</label>
                            <small>Select the overhead stage</small>
                            @error('overhead_stage')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Select Overhead --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <select class="form-select" id="selectOverhead" wire:model="overhead" required aria-label="Choose nature">
                                @if ($overheads->isEmpty())
                                    <option value="">First Select overhead stage</option>
                                @else
                                    <option value="">Select Overhead</option>
                                    @foreach ($overheads as $overhead)
                                        <option value="{{ $overhead->id }}">{{ $overhead->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <label for="selectOverhead" class="font-quick text-dark font-normal">Select Overhead</label>
                            <small>Select the overhead</small>
                            @error('overhead')
                                <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Cost --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <input type="number" id="overheadRate" class="form-control" wire:model="rate" disabled>
                            <label for="overheadRate" class="font-quick text-dark font-normal">Rate</label>
                            <small>Apportioned rate of this overhead in INR.</small>
                            @error('rate')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Ratio --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <input type="number" id="overheadRatio" class="form-control" wire:model="ratio" step="0.5" required>
                            <label for="overheadRatio" class="font-quick text-dark font-normal">Consumption Rate</label>
                            <small>Enter the Rate in which this whould apply on this product</small>
                            @error('ratio')
                                <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Reason --}}
                    <div class="col-12 mb-3">
                        <div class="form-floating">
                            <textarea id="overheadReason" class="form-control" wire:model="reasons"
                                style="height: 100px;"></textarea>
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

    @if (isset($productOverheads) && $productOverheads->isNotEmpty())
        <div class="collapse show" id="productOverheadList">
            <div class="card-body p-0">
                <ul class="list-group list-group-flushed">
                    @foreach ($productOverheads as $key => $productOverhead)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="pe-2">{{ $loop->iteration }}.</span>
                                <strong>{{ $productOverhead->overhead->name }}</strong>
                                <span class="ms-3">₹ {{ $productOverhead->amount }}</span>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseProductOverheads{{ $key }}"
                                    aria-expanded="false" aria-controls="collapseProductOverheads{{ $key }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-dark" wire:click="edit({{ $productOverhead->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-dark" wire:click="delete({{ $productOverhead->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="collapse" id="collapseProductOverheads{{ $key }}">
                            <div class="card-body">
                                <p><strong>Overhead:</strong> {{ $productOverhead->overhead->name }}</p>
                                <p><strong>Cost:</strong> {{ $productOverhead->rate }} INR</p>
                                <p><strong>Ratio:</strong> {{ $productOverhead->ratio }}</p>
                                <p><strong>Reason:</strong> {{ $productOverhead->reasons }}</p>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

</div>