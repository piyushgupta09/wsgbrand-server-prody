<div class="card border-0">
    
    @include('prody::includes.form-toggle-button')
    
    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-transition.delay.100ms
        class="card-body px-2 text-bg-secondary">

        <form wire:submit.prevent="{{ $formType === 'create' ? 'store' : 'update' }}">

            <div class="row g-2 m-0">

                <p class="font-robot ls-1">
                    @if ($formType == 'create')
                    Create New Range
                    @else
                    Edit Range
                    @endif
                </p>

                {{-- Select Range Type --}}
                <div class="col-md-3 mb-3">
                    <div class="form-floating">
                        <select class="form-select" id="productRangeType" wire:model="rangeType" required
                            aria-label="Choose range type">
                            <option selected>Select Type</option>
                            @foreach ($ranges as $key => $type)
                            <option value="{{ $key }}">{{ $type }}</option>
                            @endforeach
                        </select>
                        <label for="productRangeType" class="font-quick text-dark font-normal">Product Range</label>
                        @error('rangeType')
                        <span class="text-bg-danger error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Product Range Cost --}}
                <div class="col-md-3 mb-3">
                    <div class="form-floating">
                        <input type="text" id="productRangeCost" class="form-control" wire:model="rangeCost">
                        <label for="productRangeCost" class="font-quick text-dark font-normal">
                            Buying Rate (cost)
                        </label>
                        @error('rangeCost')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Product Range Mrp --}}
                <div class="col-md-3 mb-3">
                    <div class="form-floating">
                        <input type="text" id="productRangeMrp" class="form-control" wire:model="rangeMrp">
                        <label for="productRangeMrp" class="font-quick text-dark font-normal">
                            Market Rate (mrp)
                        </label>
                        @error('rangeMrp')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Product Range Rate --}}
                <div class="col-md-3 mb-3">
                    <div class="form-floating">
                        <input type="text" id="productRangeRate" class="form-control" wire:model="rangeRate" required>
                        <label for="productRangeRate" class="font-quick text-dark font-normal">
                            Selling Rate (payable)
                        </label>
                        @error('rangeRate')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="btn-group w-50 ms-auto">
                    <button type="button" wire:click.prevent="resetForm()" class="btn btn-outline-light">Reset</button>
                    <button type="submit" class="btn btn-info">{{ $formType === 'create' ? 'Save' : 'Update' }}</button>
                </div>

            </div>

        </form>
    </div>

    <div class="collapse show" id="productRangeList">
        <ul class="list-group list-group-flushed">
            @foreach($productRanges as $key => $productRange)
            <li class="list-group-item rounded-0">

                <div class="d-flex">

                    <div class="flex-fill d-flex align-items-center font-quick">
                        <span style="width: 20px">{{ $loop->iteration }}</span>
                        <p class="mb-0 text-capitalize fw-bold">
                            {{ $productRange->name }} | Rs. {{ $productRange->rate }}</p>
                    </div>

                    <div class="btn-group">
                        <button 
                            class="btn border-dark py-1 px-3
                            {{ $productRange->active ? 'btn-success' : 'btn-danger' }}" 
                            type="button"
                            wire:click.prevent="stockout({{ $productRange->id }})">
                            @if ($productRange->active)
                                <i class="bi bi-cart-check"></i>
                            @else
                                <i class="bi bi-cart-x"></i>
                            @endif
                        </button>
                        <button 
                            class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                            wire:click.prevent="clone({{ $productRange->id }})">
                                <i class="bi bi-copy"></i>
                            </button>
                        <button class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                            wire:click.prevent="edit({{$productRange->id}})">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                            wire:click.prevent="delete({{$productRange->id}})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                </div>
            </li>
            @endforeach
        </ul>
    </div>

</div>