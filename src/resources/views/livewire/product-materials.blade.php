<div class="card bg-none border-0">

    @include('prody::includes.form-toggle-button')

    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-transition.delay.100ms class="card-body p-0 text-bg-secondary">

        <div class="row m-0 py-3">

            <p class="font-robot ls-1">Search Required Material</p>

            {{-- Enter Supplier Name --}}
            <div class="col-4 mb-3">
                <div class="form-floating">
                    <select class="form-select" id="floatingSelectSuppliers" required
                        wire:model="materialSupplier" required aria-label="Choose material supplier">
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    <label for="floatingSelectSuppliers" class="font-quick text-dark font-normal">Supplier Name</label>
                    @if ($supplierMaterialCount)
                        <small class="fw-bold font-title ps-2">
                            We have {{ $supplierMaterialCount }} types of materials from this supplier
                        </small>
                    @endif
                    @error('materialSupplier')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Enter Material Search --}}
            <div class="col-8 mb-3">
                
                @if ($materials->isEmpty())
                    
                    <div class="form-floating">
                        <input 
                            type="text" id="floatingSelectMaterialsEmpty" 
                            class="form-control text-danger" 
                            value="No Material available from selected supplier" disabled>
                        <label for="floatingSelectMaterialsEmpty" class="font-quick text-dark font-normal">Material Name</label>
                    </div>

                @else
                    <div class="form-floating">
                    
                        <input class="form-control" list="datalistMaterials" id="floatingSelectMaterials"
                            placeholder="Search Material Name" wire:model.lazy="selectedMaterial">

                        <datalist id="datalistMaterials">
                            @foreach ($materials as $record)
                                <option>{{ $record->sid }} | {{ $record->name }}</option>
                            @endforeach
                        </datalist>

                        <label for="floatingSelectMaterials" class="font-quick text-dark font-normal">Material Name</label>
                    </div>
                
                @endif

                <a class="btn btn-sm btn-light py-0" href="{{ route('materials.create') }}" target="_blank">
                    <i class="bi bi-plus-lg pe-2"></i> Add Material
                </a>
                <button class="btn btn-sm btn-light py-0" wire:click='checkSupplierNewMaterialCount'>
                    <i class="bi bi-repeat pe-2"></i> Check New
                </button>
                <button class="btn btn-sm btn-light py-0" wire:click='loadSupplierMaterials'>
                    <i class="bi bi-download pe-2"></i> Download
                </button>

            </div>

            <p class="font-robot ls-1">Create Required Material</p>
            
            {{-- Select Material Type --}}
            <div class="col-md-6 mb-3">
                <div class="form-floating">
                    <select class="form-select" id="productMaterialType" required
                        wire:model="materialType" required aria-label="Choose material type">
                        <option selected>Select Type</option>
                        @foreach ($types as $key => $material)
                            <option value="{{ $key }}">{{ $material }}</option>
                        @endforeach
                    </select>
                    <label for="productMaterialType" class="font-quick text-dark font-normal">Material Type</label>
                    @error('materialType')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Select Material Unit --}}
            <div class="col-md-6 mb-3">
                <div class="form-floating">
                    <input type="text" id="productMaterialUnit" class="form-control" wire:model.lazy="materialUnit">
                    <label for="productMaterialUnit" class="font-quick text-dark font-normal">Material Unit</label>            
                    @error('materialUnit')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            {{-- <div class="col-md-6 mb-3">
                <div class="form-floating">
                    <select class="form-select" id="productMaterialUnit" required
                        wire:model="materialUnit" required aria-label="Choose material unit">
                        <option selected>Select Type</option>
                        @foreach ($units as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <label for="productMaterialUnit" class="font-quick text-dark font-normal">Material Unit</label>
                    @error('materialUnit')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @enderror
                </div>
            </div> --}}

            {{-- Enter Material Name --}}
            <div class="col-md-6 mb-3">
                <div class="form-floating">
                    <input type="text" id="productMaterialName" class="form-control" wire:model.lazy="materialName" required>
                    <label for="productMaterialName" class="font-quick text-dark font-normal">Material Name</label>            
                    @error('materialName')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @else
                        <span style="font-size: 0.75rem">Cotton Lycra</span>
                    @enderror
                </div>
            </div>

            {{-- Enter Material Sid --}}
            <div class="col-md-3 mb-3">
                <div class="form-floating">
                    <input type="text" id="productMaterialCode" class="form-control" wire:model.lazy="materialSid" required>
                    <label for="productMaterialCode" class="font-quick text-dark font-normal">Material Code</label>
                    @error('materialSid')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @else
                        <span style="font-size: 0.75rem">Unique Code Number</span>
                    @enderror
                </div>
            </div>

            {{-- Enter Material Price --}}
            <div class="col-md-3 mb-3">
                <div class="form-floating">
                    <input type="text" id="productMaterialPrice" class="form-control" wire:model="materialPrice" required>
                    <label for="productMaterialPrice" class="font-quick text-dark font-normal">Material Price</label>
                    @error('materialPrice')
                        <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                    @else
                        <span style="font-size: 0.75rem">Buying or Market Price</span>
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

    @if (isset($attachedMaterials) && $attachedMaterials->isNotEmpty())     
        <div class="collapse show" id="productMaterialList">
            <ul class="list-group list-group-flushed">
                @foreach($attachedMaterials as $key => $attachedMaterial)
                    <li class="list-group-item rounded-0">
        
                        <div class="d-flex">
                            
                            <div class="flex-fill d-flex align-items-center font-quick">
                                <span style="width: 20px">{{ $loop->iteration }}</span>
                                <p class="mb-0 text-capitalize fw-bold d-flex align-items-center">
                                    {{ $attachedMaterial->material->category_type }} | {{ $attachedMaterial->material->name }} #{{ $attachedMaterial->material->sid }}
                                    @if ($attachedMaterial->grade)
                                        <span class="badge bg-dark ms-2 rounded-0">
                                            {{ $attachedMaterial->grade }}
                                        </span>
                                    @endif
                                </p>
                            </div>
        
                            <div class="btn-group">
                                <button 
                                    class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseProductMaterial{{ $key }}"
                                    aria-expanded="false" aria-controls="collapseProductMaterial{{ $key }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button 
                                    class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                    wire:click.prevent="delete({{$attachedMaterial->material->id}})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
        
                        </div>

                        <div class="collapse" id="collapseProductMaterial{{ $key }}">
                            <div class="d-flex flex-column">
                                <span>Unit: {{ $attachedMaterial->material->unit_name }}</span>
                                <span>Price: {{ $attachedMaterial->material->price }}</span>
                            </div>
                        </div>
                        
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
      
</div>