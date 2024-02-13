<div class="card border-0">

    @include('prody::includes.form-toggle-button')

    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-transition.delay.100ms class="card-body p-0 text-bg-secondary">

        <div class="row m-0 py-3">

            <div class="col-md-6">
                <div class="form-group">
                    <label for="productMeasurekeysInput" class="form-label">Measurekey</label>
                    <input list="productMeasurekeys" id="productMeasurekeysInput" class="form-control" wire:model="measurekey" required />
                    <datalist id="productMeasurekeys">
                        @foreach ($measurekeys as $measure_key)
                            <option>{{ $measure_key->name }} | {{ $measure_key->unit }}</option>
                        @endforeach
                    </datalist>
                    @error('measurekey')
                        <span class="bg-white d-block mt-1 text-danger">{{ $message }}</span>
                    @enderror
                    @if ($measurekey_info)
                        <span>{{ $measurekey_info }}</span>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="productMeasurevalsInput" class="form-label">Measureval</label>
                    <input list="productMeasurevals" id="productMeasurevalsInput" class="form-control" wire:model="measureval" required />
                    <datalist id="productMeasurevals">
                        @foreach ($measurekey_vals as $measure_val)
                            <option>{{ $measure_val->value }}</option>
                        @endforeach
                    </datalist>
                    @error('measureval')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @if ($measureval_info)
                        <span>{{ $measureval_info }}</span>
                    @endif
                </div>
            </div>            

            <div class="btn-group w-50 ms-auto mt-3">
                <button type="button" wire:click.prevent="resetForm()" class="btn btn-outline-light">Reset</button>
                @if ($formType == 'create')
                    <button type="button" wire:click.prevent="store()" class="btn btn-info">Save</button>
                @else
                    <button type="button" wire:click.prevent="update()" class="btn btn-info">Update</button>
                @endif
            </div>
            
        </div>

    </div>

    <div class="collapse show" id="productMeasurebutes">
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
    </div>

    @include('panel::includes.livewire-alert')

</div>
