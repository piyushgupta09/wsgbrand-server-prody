<div class="card border-0 font-title">

    @include('prody::includes.form-toggle-button')

    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-transition.delay.100ms class="card-body p-0 text-bg-secondary">

        <div class="row m-0 py-3">

            {{-- Party --}}

            <div class="col-12 ps-0">
                <div class="text-bg-light fw-500 w-fc px-2 py-1 mb-3 br-end">
                    1. Select Party for Ledger
                </div>
            </div>

            @if ($ledgerParty)
                <div class="d-flex justify-content-between">
                    @include('panel::includes.select-option-card', [
                        'selectedParty' => $ledgerParty,
                    ])
                </div>
            @else
                <div class="col-12" style="z-index: 10000000;">
                    <livewire:add-search-select 
                        :datalist="$parties" label="Search for {{ $model->productDecisions->factory ? 'fabricators' : 'vendors' }} ..."
                        :modelCreateRoute="'parties.create'"    
                    />
                </div>
            @endif

            <div class="my-2"></div>

            {{-- Min --}}
            <div class="col-md-4 mb-3">
                <div class="form-floating">
                    <input type="number" id="inputMinQuantity" class="form-control" wire:model.lazy="min_qty" required>
                    <label for="inputMinQuantity" class="font-quick text-dark font-normal">Min Order Quantity</label>
                    @error('min_qty')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            {{-- Max --}}
            <div class="col-md-4 mb-3">
                <div class="form-floating">
                    <input type="number" id="inputMaxQuantity" class="form-control" wire:model.lazy="max_qty" required>
                    <label for="inputMaxQuantity" class="font-quick text-dark font-normal">Max Order Quantity</label>
                    @error('max_qty')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            {{-- Buy Rate --}}
            <div class="col-md-4 mb-3">
                <div class="form-floating">
                    <input type="number" id="inputFabRate" class="form-control" wire:model.lazy="fab_rate" required>
                    <label for="inputFabRate" class="font-quick text-dark font-normal">
                        @if ($model->productDecisions->factory)
                            Fab Rate (₹ /pc)
                        @elseif ($model->productDecisions->vendor)
                            Buy Rate (₹ /pc)
                        @endif
                    </label>
                    @error('fab_rate')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            {{-- Manager --}}

            <div class="col-12 ps-0">
                <div class="text-bg-light fw-500 w-fc px-2 py-1 mb-3 br-end">
                    2. Select Manager for Ledger
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                @if ($managers->isEmpty())
                    <div class="alert alert-warning py-2">
                        No manager available
                    </div>
                @else
                    <div class="form-floating">
                        <select class="form-select" id="floatingSelectLedgerManager" 
                            wire:model.lazy="manager_id"
                            aria-label="Choose ledger manager from list of available managers">
                            <option value="">Select Manager</option>
                            @foreach ($managers as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                            @endforeach
                        </select>
                        <label for="floatingSelectLedgerManager">Select Ledger Manager</label>
                    </div>                
                @endif
            </div>
            {{-- Order Cap --}}
            <div class="col-md-4 mb-3">
                <div class="form-floating">
                    <input type="number" id="inputOrderCapacity" class="form-control" wire:model.lazy="order_cap" required>
                    <label for="inputOrderCapacity" class="font-quick text-dark font-normal">Max Order Cap</label>
                    @error('order_cap')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            {{-- Commision Rate --}}
            <div class="col-md-4 mb-3">
                <div class="form-floating">
                    <input type="number" id="inputFeePerSale" class="form-control" wire:model.lazy="fee_rate" required>
                    <label for="inputFeePerSale" class="font-quick text-dark font-normal">Fee per sale</label>
                    @error('fee_rate')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Note --}}
            <div class="col-12 mb-3">
                <div class="form-floating">
                    <input type="text" id="inputNotes" class="form-control" wire:model.lazy="notes">
                    <label for="inputNotes" class="font-quick text-dark font-normal">Notes</label>
                    @error('notes')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="btn-group w-50 ms-auto">
                <button type="button" wire:click.prevent="resetForm()" class="btn btn-outline-light">Reset</button>
                @if ($formType == 'create')
                    <button type="button" wire:click.prevent="store()" class="btn btn-info">Save</button>
                @else
                    <button type="button" wire:click.prevent="update()" class="btn btn-info">Update</button>
                @endif
            </div>
            
        </div>

    </div>

    <div class="collapse show" id="productSuppliers">
        <ul class="list-group list-group-flushed">
            @foreach($ledgers as $key => $ledger)
                <li class="list-group-item rounded-0">
    
                    <div class="d-flex">
                        
                        <div class="flex-fill d-flex align-items-center font-quick mb-2">
                            <span style="width: 20px">{{ $loop->iteration }}</span>
                            <div class="pe-2">
                                @include('panel::includes.select-option-card', [
                                    'selectedParty' => $ledger->party,
                                ])
                            </div>
                            <div class="d-flex flex-column border-start px-3">
                                <span class="small">Min Quantity: {{ $ledger->min_qty }} pcs</span>
                                <span class="small">Max Quantity: {{ $ledger->max_qty }} pcs</span>
                            </div>
                            <div class="flex-fill d-flex flex-column border-start ps-3">
                                <span class="small">Fab Rate: ₹ {{ $ledger->fab_rate }} /pc</span>
                                @if ($ledger->notes)
                                    <span class="small">{{ $ledger->notes }}</span>
                                @endif
                            </div>
                        </div>
    
                        <div class="btn-group">
                            <button 
                                class="btn px-3" type="button"
                                wire:click.prevent="edit({{ $ledger->id }})">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button 
                                class="btn px-3" type="button"
                                wire:click.prevent="delete({{ $ledger->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
    
                    </div>
                                
                </li>
            @endforeach
        </ul>
    </div>

    @include('panel::includes.livewire-alert')

</div>
