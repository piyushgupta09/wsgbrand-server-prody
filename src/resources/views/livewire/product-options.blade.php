<div class="card border-0">

    @include('prody::includes.form-toggle-button')
    
    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-transition.delay.100ms class="card-body p-0 text-bg-secondary">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="row m-0 py-3">

            <p class="font-robot ls-1">
                @if ($formType == 'create')
                    Create New Option (Product Color)
                @else
                    Edit Option (Product Color)
                @endif
            </p>

            {{-- Product Option Name --}}
            <div class="col-6 mb-3">
                <div class="form-floating">
                    <input type="text" id="productOptionName" class="form-control" wire:model.lazy="productOptionName" required>
                    <label for="productOptionName" class="font-quick text-dark font-normal">Option Color Name</label>
                    @error('productOptionName')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Product Option Color Code --}}
            <div class="col-6 mb-3">
                <div class="form-floating">
                    <input type="text" id="productOptionCode" class="form-control" wire:model.lazy="productOptionCode" required>
                    <label for="productOptionCode" class="font-quick text-dark font-normal">Option Color Code</label>
                    <span style="font-size: 0.75rem" class="">Use hex color code, ex: #ea0021</span>
                    @error('productOptionCode')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Preview & Upload Images --}}
            <div class="mb-3">

                {{-- Preview --}}
                <div class="div mb-2">
                    @if ($existingImages)
                        @foreach ($existingImages as $image)
                            <img 
                                src="{{ $image->getUrl() }}"
                                alt="Existing Image Preview" 
                                width="70" height="100" 
                                style="object-fit: cover" 
                                class="me-2 rounded"
                                data-bs-toggle="modal"
                                data-bs-target="#optionImageDeleteModal{{ $image->id }}"
                            >

                            <div class="modal fade" id="optionImageDeleteModal{{ $image->id }}" 
                                tabindex="-1" aria-labelledby="optionImageDeleteModalLabel{{ $image->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <p class="text-danger">
                                                Are you sure you want to delete this image?
                                            </p>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-danger"
                                                    wire:click="deleteImage({{ $image->id }},{{ $image->model_id }})">Confirm Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        @endforeach
                    @endif            

                    @if ($productOptionImages)
                        @foreach ($productOptionImages as $image)
                            <img 
                                src="{{ $image->temporaryUrl() }}"
                                alt="New Image Preview" 
                                width="70" height="100" 
                                style="object-fit: cover" 
                                class="me-2 rounded"
                            >
                        @endforeach
                    @endif
                </div>

                {{-- Upload --}}
                <input type="file" id="productOptionImages" class="form-control" wire:model="productOptionImages" multiple required>
                <small>Upload the product images in respect of this color only</small>

                {{-- Errors --}}
                @error('productOptionImages.*')
                    <span class="text-bg-danger error">{{ $message }}</span>
                @enderror
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

    <div class="collapse show" id="productOptionsList">
        <ul class="list-group list-group-flushed">
            @foreach($proOptions as $key => $productOption)
                <li class="list-group-item rounded-0">
    
                    <div class="d-flex">
                        
                        <div class="flex-fill d-flex align-items-center font-quick mb-2">
                            <span style="width: 20px">{{ $loop->iteration }}</span>
                            <div class="me-3 w-35p h-100 rounded" style="background-color: {{ $productOption->code }}"></div>
                            <p class="mb-0 text-capitalize fw-bold">{{ $productOption->name }}</p>
                        </div>
    
                        <div class="btn-group">
                            <button 
                                class="btn border-dark py-1 px-3
                                {{ $productOption->active ? 'btn-success' : 'btn-danger' }}" 
                                type="button"
                                wire:click.prevent="stockout({{ $productOption->id }})">
                                @if ($productOption->active)
                                    <i class="bi bi-cart-check"></i>
                                @else
                                    <i class="bi bi-cart-x"></i>
                                @endif
                            </button>
                            <button 
                                class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseOptionImages{{ $key }}"
                                aria-expanded="false" aria-controls="collapseOptionImages{{ $key }}">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button 
                                class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                wire:click.prevent="clone({{ $productOption->id }})">
                                <i class="bi bi-copy"></i>
                            </button>
                            <button 
                                class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                wire:click.prevent="edit({{ $productOption->id }})">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button 
                                class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                wire:click.prevent="delete({{ $productOption->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
    
                    </div>
                    
                    <div class="collapse" id="collapseOptionImages{{ $key }}">
                        <div class="d-flex">
                            @if (!empty($productOption->getMedia($productOption->getMediaCollectionName())))
                                @foreach ($productOption->getMedia($productOption->getMediaCollectionName()) as $media)
                                    <img src="{{ $media->getUrl('s100') }}" class="rounded me-2" width="100px" height="120px" style="object-fit: cover"/>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                </li>
            @endforeach
        </ul>
    </div>
    
</div>