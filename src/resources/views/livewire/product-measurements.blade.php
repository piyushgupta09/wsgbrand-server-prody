<div class="card border-0">

    @include('prody::includes.form-toggle-button')

    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-transition.delay.100ms class="card-body text-bg-secondary mb-3">

        <div class="form-floating mb-2">

            <select class="form-select" id="productMeasurekeysInput" 
                wire:model="measurekey" aria-label="Select Measurement Name"
                {{ $formType != 'create' ? 'disabled'  : '' }}>
                <option value="">Select Measurement Name</option>
                @foreach ($measurekeys as $measure_key)
                    <option value="{{ $measure_key->name }}">{{ $measure_key->name }} | {{ $measure_key->unit }}</option>
                @endforeach
            </select>
            <label for="productMeasurekeysInput">Measurement Name</label>

            @error('measurekey')
                <span class="text-bg-danger px-2 py-1">{{ $message }}</span>
            @enderror

            @if ($measurekey_info)
                <span>{{ $measurekey_info }}</span>
            @endif

        </div>

        <div class="row g-2">
            <div class="col-12 d-flex flex-column p-2">
                <span class="my-1 fw-bold">Measurement Values:</span>
                <div class="d-flex">
                    @foreach ($measurekey_vals as $measure_val)
                        <span class="me-2">{{ $loop->first ? '' : ' , ' }}{{ $measure_val->value }}</span>
                    @endforeach
                </div>
            </div>
            @foreach ($productRanges as $index => $productRange)
                <div class="col-5">
                    <input type="text" class="form-control" value="Size {{ $productRange->name }}" disabled />
                </div>
                <div class="col-7">
                    <input 
                        type="text" class="form-control"
                        id="productMeasurevalsInput{{ $productRange->id }}" 
                        wire:model="measureval.{{ $productRange->id }}" >
                    
                    @error('measureval.' . $productRange->id)
                        <span class="text-bg-danger px-2 py-1">{{ $message }}</span>
                    @enderror
                </div>
            @endforeach
        </div>   

        <div class="d-flex justify-content-end w-100">
            <div class="btn-group w-75 ms-auto mt-3">
                <button type="button" wire:click.prevent="resetForm()" class="btn btn-outline-light">Reset</button>
                @if ($formType == 'create')
                    <button type="button" wire:click.prevent="store()" class="btn btn-dark">Save</button>
                @else
                    <button type="button" wire:click.prevent="update()" class="btn btn-dark">Update</button>
                @endif
            </div>
        </div>

    </div>

    <div class="collapse show" id="productMeasurebutes">
        <table class="table">
            <thead>
                <tr>
                    <th>Measurement</th>
                    @foreach($productRanges as $range)
                        <th>{{ $range->name }}</th>
                    @endforeach
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($model->productMeasurements->groupBy('measurekey_id') as $measurekeyId => $groupedMeasurements)
                    @php
                        $measurekey = $measurekeys->firstWhere('id', $measurekeyId);
                    @endphp
                    @if($measurekey)
                        <tr>
                            <td>{{ $measurekey->name }}</td>
                            @foreach($productRanges as $range)
                                @php
                                    $measurementForRange = $groupedMeasurements->firstWhere('product_range_id', $range->id);
                                @endphp
                                <td>
                                    {{ optional($measurementForRange)->measureval->value ?? '-' }}
                                    {{ optional($measurekey)->unit ?? '' }}
                                </td>
                            @endforeach
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-dark"
                                        wire:click="edit({{ $measurekeyId }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                        wire:click="confirmDelete({{ $measurekeyId }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            
        </table>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($confirmingDelete)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,.5);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" wire:click="$set('confirmingDelete', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this measurement? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteConfirmed">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    

    {{-- <div class="collapse show" id="productMeasurebutes">
        <div class="row g-2">
            @foreach($measurements as $measurement)
                <div class="col-md-6 col-xl-4">
                    <div class="input-group">
                        <span class="input-group-text fw-500 rounded-0"
                            style="width: 125px">
                            {{ $measurement->measurekey->name }}
                        </span>
                        <select class="form-select" disabled aria-label="Default select example">
                            <option selected>Open this select menu</option>
                            @foreach ($measurement->measurekey->measurevals as $item)
                                <option value="{{ $item->id }}"
                                    {{ $measurement->measureval->id == $item->id ? 'selected' : '' }}>
                                    {{ $item->value }} {{ $measurement->measurekey->unit }}
                                </option>
                            @endforeach
                        </select>
                        <span class="input-group-text text-danger rounded-0"
                            wire:click='delete({{ $measurement->id }})'>
                            <i class="bi bi-trash"></i>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div> --}}

    @include('panel::includes.livewire-alert')

</div>
