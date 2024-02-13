<div class="card border-0">
    <div class="card-header rounded-0 bg-dark py-2 d-flex justify-content-between align-items-center w-100">
        <div class="d-flex align-items-center flex-fill">
            <span class="font-quick text-white ls-1 fw-bold">Product Actions</span>
        </div>
        <div class="btn-group border border-secondary">
            @foreach ($productStatus as $status)    
                <button 
                    class="btn border-0 text-white font-quick ls-1 fw-bold text-capitalize
                    {{ $product->status == $status ? ($product->status == 'live' ? 'btn-success' : 'btn-danger') : 'btn-outline-secondary' }} }}" 
                    type="button" wire:click="updateStatus('{{ $status }}')">
                    {{ $status }}
                </button>
            @endforeach
        </div>
    </div>
</div>