<div class="row g-3">
    @foreach ($decisions as $decision)
        <div class="col-md-3">
            <button
                class="btn text-start w-100 {{ $decision['active'] ? 'btn-success' : 'btn-outline-dark' }}"
                wire:click="updateDecision('{{ $decision['type'] }}')">
                <div class="d-flex align-items-center">
                    <img src="{{ $decision['image'] }}" alt="" width="50" height="50">
                    <div class="d-flex flex-column ps-2 justify-content-center align-items-start">
                        <span class="fw-bold text-capitalize font-title">{{ $decision['name'] }}</span>
                        <span class="small text-capitalize max-line-1">{{ $decision['details'] }}</span>
                    </div>
                </div>
            </button>
        </div>
    @endforeach
</div>
