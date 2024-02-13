<div class="card border-0">

    @include('prody::includes.form-toggle-button')

    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-transition.delay.100ms class="card-body p-0 text-bg-secondary">

        <div class="row m-0 py-3">

            @if ($ledgerParty)
                <div class="d-flex justify-content-between">
                    @include('panel::includes.select-option-card', [
                        'selectedParty' => $ledgerParty,
                    ])
                    {{-- <div class="bg-light h-100">
                        <button class="btn btn-danger h-100 border-0 rounded-0" wire:click="removeParty">
                            <i class="bi bi-x-lg px-2"></i>
                        </button>
                    </div> --}}
                </div>
            @else
                <div class="col-12" style="z-index: 10000000;">
                    <livewire:add-search-select 
                        :datalist="$parties" 
                        :modelCreateRoute="'parties.create'"    
                    />
                </div>
            @endif

            <div class="my-2"></div>

            <div class="col-md-4 mb-3">
                <div class="form-floating">
                    <input type="number" id="inputMinQuantity" class="form-control" wire:model.lazy="min_qty" required>
                    <label for="inputMinQuantity" class="font-quick text-dark font-normal">Min Quantity</label>
                    @error('min_qty')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="form-floating">
                    <input type="number" id="inputMaxQuantity" class="form-control" wire:model.lazy="max_qty" required>
                    <label for="inputMaxQuantity" class="font-quick text-dark font-normal">Max Quantity</label>
                    @error('max_qty')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="form-floating">
                    <input type="number" id="inputFabRate" class="form-control" wire:model.lazy="fab_rate" required>
                    <label for="inputFabRate" class="font-quick text-dark font-normal">
                        @if ($model->factory)
                            Fab Rate (₹ /pc)
                        @elseif ($model->vendor)
                            Buy Rate (₹ /pc)
                        @endif
                    </label>
                    @error('fab_rate')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

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
