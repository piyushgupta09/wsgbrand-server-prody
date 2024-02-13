<div class="container-fluid">
    
    <div class="text-bg-dark px-3 py-2">
        <div class="d-flex justify-content-between align-items-center">
            <span>Add New Product to Collection</span>
            <button class="btn text-white" type="button" data-bs-toggle="collapse"
                data-bs-target="#addNewProductToCollection" aria-expanded="true"
                aria-controls="addNewProductToCollection">
                <i class="bi bi-plus-lg"></i>
            </button>
        </div>
    </div>

    <div class="collapse show" id="addNewProductToCollection">
        <form wire:submit.prevent="store">
            <div class="card card-body rounded-0">        
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="product" class="form-label">Products</label>
                            <select id="product" class="form-select" wire:model="selectedProduct" required>
                                <option value="">Select Product</option>
                                @foreach ($allProducts as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->code }} | {{ $value->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedProduct')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="productOption" class="form-label">Product Options (Colors)</label>
                            <select id="productOption" class="form-select" wire:model="selectedProductOption" required>
                                <option value="">Select Primary Product (Color)</option>
                                @if ($productOptions->isNotEmpty())
                                    @foreach ($productOptions as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('selectedProductOption')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
        
                <div class="d-flex w-100 mt-3 justify-content-between">
                    <button type="reset" class="btn btn-sm w-25 btn-outline-dark">Reset</button>
                    <button type="submit" class="btn btn-sm w-25 btn-success">Submit</button>
                </div>
            </div>
        </form>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if ($cProducts->isEmpty())
        <p class="lead p-3 text-center">This collection has no products added yet</p>
    @else    
        <table class="table table-sm">
            <thead class="table-dark">
                <tr>
                    <th class="small text-start ps-2">S. No.</th>
                    <th class="small text-start">Product Name</th>
                    <th class="small text-start">Option Name (Color)</th>
                    <th class="small text-end pe-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cProducts as $value)
                    <tr>
                        <td class="text-start ps-3">{{ $loop->iteration }}</td>
                        <td class="text-start text-capitalize">#{{ $value->product->code }} | {{ $value->product->name }}</td>
                        <td class="text-start text-capitalize">{{ $value->productOption->name }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm"  wire:click.prevent="delete({{ $value->id }})">
                                <i class="bi bi-trash fs-5 text-danger"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>
