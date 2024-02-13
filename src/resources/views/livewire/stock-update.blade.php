<div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="updateStock">
        <div class="card card-body rounded-0">        
            <div class="form-group">
                <label for="product" class="form-label">Updated Stock Quantity</label>
                <input type="number" wire:model.lazy="stockQuantity" class="form-control form-control-sm @error('stock') is-invalid @enderror" id="stock" placeholder="Enter Stock Quantity">
                @error('stockQuantity')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="d-flex w-100 mt-3 justify-content-between">
                <button type="reset" class="btn btn-sm w-25 btn-outline-dark">Reset</button>
                <button type="submit" class="btn btn-sm w-25 btn-success">Submit</button>
            </div>
        </div>
    </form>
</div>
