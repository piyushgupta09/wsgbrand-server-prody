<div class="">

    @if ($needToGenerateStock)
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <span><span class="fw-bold">Warning:</span> You need to generate stock for this product.</span>
            <button class="btn py-1 btn-warning" wire:click="generateStocks">
                Generate Stock
            </button>
        </div>
    @endif

    <div class="card rounded-0 border-bottom-0">
        <div class="d-flex w-100">
            <img src="{{ $product->getImage() }}" class="w-100p">
            <div class="flex-fill card-body border-start">
                <div class="d-flex flex-column justify-content-between h-100">
                    <div class="d-flex flex-column flex-fill">
                        <span>{{ $product->name }}</span>
                        <span>#{{ $product->code }}</span>
                    </div>
                    <div style="width: fit-content; min-width: 100px"
                        class="p-2 fw-bold text-end {{ $product->stock && $product->stock->quantity ? 'text-bg-success' : 'text-bg-danger' }}">
                        {{ $product->stock ? $product->stock->quantity . ' pcs' : 'No Stock' }}
                    </div>
                </div>
            </div>
            @if (auth()->user()->email == 'pg.softcode@gmail.com')
                <button class="btn border-0 btn-danger font-quick ls-1 fw-bold text-capitalize" wire:click="deleteStocks">
                    Delete Stocks
                </button>
            @endif
        </div>
    </div>

    <table class="table table-striped">
        <thead class="table-secondary">
            <tr>
                <th style="width: 200px">#</th>
                @foreach ($product->productRanges as $productRange)
                    <th>{{ $productRange->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($product->productOptions as $productOption)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="wh-30 rounded shadow"
                                style="background-color: {{ $productOption->code }}"></span>
                            <span class="px-2 fw-bold text-capitalize">{{ $productOption->name }}</span>
                        </div>
                    </td>
                    @foreach ($product->productRanges as $productRange)
                        <td>{{ $this->getStockItemQuantity($productRange->id, $productOption->id) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
