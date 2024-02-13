<div class="card border-0 mb-3">

    <div class="card-header rounded-0 bg-dark py-2 d-flex justify-content-between align-items-center w-100">
        <button class="btn border-0 ps-0 text-white flex-fill text-start" type="button" 
            data-bs-toggle="collapse" data-bs-target="#materialOptionsList" 
            aria-expanded="true" aria-controls="materialOptionsList">
            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <i class="bi bi-chevron-down me-2"></i>
                    <span class="font-quick ls-1 fw-bold">Material Options</span>
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
{{-- 
            <p class="font-robot ls-1">
                @if ($formType == 'create')
                    Create New Color
                @else
                    Edit Color
                @endif
            </p> --}}

            {{-- Material Option Name --}}
            <div class="col-6 mb-3">
                <div class="form-floating">
                    <input type="text" id="materialOptionName" class="form-control" wire:model.lazy="materialOptionName" required>
                    <label for="materialOptionName" class="font-quick text-dark font-normal">Fabric Color Name</label>
                    @error('materialOptionName')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Material Option Color Code --}}
            <div class="col-6 mb-3">
                <div class="form-floating">
                    <input type="text" id="materialOptionCode" class="form-control" wire:model.lazy="materialOptionCode" required>
                    <label for="materialOptionCode" class="font-quick text-dark font-normal">Fabric Color Code</label>
                    <span style="font-size: 0.75rem" class="">Use hex color code, ex: #ea0021</span>
                    @error('materialOptionCode')
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

                    @if ($materialOptionImages)
                        @foreach ($materialOptionImages as $image)
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
                <input type="file" id="materialOptionImages" class="form-control" wire:model="materialOptionImages" multiple required>
                <small>Upload the Fabric images for this color only</small>

                {{-- Errors --}}
                @error('materialOptionImages.*')
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

    <div class="collapse show" id="materialOptionsList">
        <ul class="list-group list-group-flushed">
            @foreach($matOptions as $key => $materialOption)
                <li class="list-group-item rounded-0">
    
                    <div class="d-flex">
                        
                        <div class="flex-fill d-flex align-items-center font-quick mb-2">
                            <span style="width: 20px">{{ $loop->iteration }}</span>
                            <div class="me-3 wh-35 rounded" style="background-color: {{ $materialOption->code }}"></div>
                            <p class="mb-0 text-capitalize fw-bold">{{ $materialOption->name }}</p>
                        </div>
    
                        <div class="btn-group">
                            <button 
                                class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseOptionImages{{ $key }}"
                                aria-expanded="false" aria-controls="collapseOptionImages{{ $key }}">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button 
                                class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                wire:click.prevent="edit({{ $materialOption->id }})">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            @if (!$material->stock)    
                                <button 
                                    class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                    wire:click.prevent="delete({{ $materialOption->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </div>
    
                    </div>

                    @php
                        if ($materialOption->images) {
                            $existingImages = json_decode($materialOption->images);
                        }
                    @endphp
                    
                    <div class="collapse" id="collapseOptionImages{{ $key }}">
                        <div class="d-flex">
                            @if (!empty($materialOption->getMedia($materialOption->getMediaCollectionName())))
                                @foreach ($materialOption->getMedia($materialOption->getMediaCollectionName()) as $media)
                                    <img src="{{ $media->getUrl('s100') }}" class="rounded me-2" width="100px" height="120px" style="object-fit: cover"/>
                                @endforeach
                            @endif
                            @if ($materialOption->images)
                                @foreach ($existingImages as $imageUrl)
                                    <img src="{{ $imageUrl }}" class="rounded me-2" width="100px" height="120px" style="object-fit: cover"/>
                                @endforeach 
                            @endif  
                        </div>
                    </div>
                    
                </li>
            @endforeach
        </ul>
    </div>

    @include('panel::includes.livewire-alert')

</div>
