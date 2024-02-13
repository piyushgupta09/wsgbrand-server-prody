<div class="card border-0 mb-3">

    <div class="card-header rounded-0 bg-dark py-2 d-flex justify-content-between align-items-center w-100">
        <button class="btn border-0 ps-0 text-white flex-fill text-start" type="button" 
            data-bs-toggle="collapse" data-bs-target="#matRangesList" 
            aria-expanded="true" aria-controls="matRangesList">
            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <i class="bi bi-chevron-down me-2"></i>
                    <span class="font-quick ls-1 fw-bold">Product Ranges</span>
                </div>
            </div>
        </button>
        <div class="">
            <button 
                class="btn border-0 text-white font-quick ls-1 fw-bold" 
                type="button" wire:click='reloadData'>
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <button 
                class="btn border-0 text-white font-quick ls-1 fw-bold" 
                type="button" wire:click="toggleForm">
                <i class="bi bi-plus-lg"></i>
            </button>
        </div>
    </div>

    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-transition.delay.100ms class="card-body p-0 text-bg-secondary">

        <div class="row m-0 py-3">

            {{-- Product Range Width --}}
            <div class="col-md-4 mb-3">
                <div class="form-floating">
                    <input type="number" id="matRangeWidth" class="form-control" wire:model="materialRangeWidth" required>
                    <label for="matRangeWidth" class="font-quick text-dark font-normal">Width</label>
                    @error('materialRangeWidth')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Product Range Length --}}
            <div class="col-md-4 mb-3">
                <div class="form-floating">
                    <input type="text" id="matRangeLength" class="form-control" wire:model="materialRangeLength" required>
                    <label for="matRangeLength" class="font-quick text-dark font-normal">Length</label>
                    @error('materialRangeLength')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Product Range Rate --}}
            <div class="col-md-4 mb-3">
                <div class="form-floating">
                    <input type="text" id="matRangeRate" class="form-control" wire:model="materialRangeRate" required>
                    <label for="matRangeRate" class="font-quick text-dark font-normal">Rate</label>
                    @error('materialRangeRate')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            @php
                $actionName = $materialRangeId ? 'Update' : 'Save';
            @endphp
            <div class="btn-group w-50 ms-auto">
                <button type="button" wire:click.prevent="resetForm()" class="btn btn-outline-light">Reset</button>
                <button type="button" wire:click.prevent="save()" class="btn btn-info">{{ $actionName }}</button>
            </div>
            
        </div>

    </div>

    <div class="collapse show" id="matRangesList">
        <ul class="list-group list-group-flushed">
            @foreach($matRanges as $key => $matRange)
                <li class="list-group-item rounded-0">
    
                    <div class="d-flex">
                        
                        <div class="flex-fill d-flex align-items-center font-quick mb-2">
                            <span style="width: 20px">{{ $loop->iteration }}</span>
                            <p class="mb-0">Width:&nbsp;<strong>{{ $matRange->width }}</strong> inch</p>
                            <span class="px-2"> | </span>
                            <p class="mb-0">Lenght:&nbsp;<strong>{{ $matRange->length }}</strong></p>
                            <span class="px-2"> | </span>
                            <p class="mb-0">Rate:&nbsp;<strong>{{ $matRange->rate }}</strong></p>
                        </div>
    
                        <div class="btn-group">
                            <button 
                                class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                wire:click.prevent="edit({{ $matRange->id }})">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button 
                                class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                wire:click.prevent="delete({{ $matRange->id }})">
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
