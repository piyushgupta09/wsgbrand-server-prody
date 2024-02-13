<div class="card border-0">

    @include('prody::includes.form-toggle-button')

    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-transition.delay.100ms class="card-body p-0 text-bg-secondary">

        <div class="row m-0 py-3">

            <div class="col-md-6">
                <div class="form-group">
                    <label for="productAttrikeysInput" class="form-label">Attrikey</label>
                    <input list="productAttrikeys" id="productAttrikeysInput" class="form-control" wire:model="attrikey" required />
                    <datalist id="productAttrikeys">
                        @foreach ($attrikeys as $attri_key)
                            <option>{{ $attri_key->name }}</option>
                        @endforeach
                    </datalist>
                    @error('attrikey')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @if ($attrikey_info)
                        <span>{{ $attrikey_info }}</span>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="productAttrivalsInput" class="form-label">Attrival</label>
                    <input list="productAttrivals" id="productAttrivalsInput" class="form-control" wire:model="attrival" required />
                    <datalist id="productAttrivals">
                        @foreach ($attrikey_vals as $attri_val)
                            <option>{{ $attri_val->value }}</option>
                        @endforeach
                    </datalist>
                    @error('attrival')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @if ($attrival_info)
                        <span>{{ $attrival_info }}</span>
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

    <div class="collapse show" id="productAttributes">
        <div class="row g-2">
            @foreach($attributes as $attribute)
                <div class="col-md-6 col-xl-4">
                    <div class="input-group">
                        <span class="input-group-text fw-500 rounded-0"
                            style="width: 125px">
                            {{ $attribute->attrikey->name }}
                        </span>
                        <select class="form-select" disabled aria-label="Default select example">
                            <option selected>Open this select menu</option>
                            @foreach ($attribute->attrikey->attrivals as $item)
                                <option value="{{ $item->id }}"
                                    {{ $attribute->attrival->id == $item->id ? 'selected' : '' }}>
                                    {{ $item->value }}
                                </option>
                            @endforeach
                        </select>
                        <span class="input-group-text text-danger rounded-0"
                            wire:click='delete({{ $attribute->id }})'>
                            <i class="bi bi-trash"></i>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @include('panel::includes.livewire-alert')

</div>
