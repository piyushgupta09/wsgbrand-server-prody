<div class="card border-0 rounded-0">

    <div class="card-header rounded-0 bg-dark py-2 d-flex justify-content-between align-items-center w-100">
        <div class="d-flex align-items-center justify-content-between w-100">
            <button class="flex-fill btn border-0 ps-0 text-white flex-fill text-start" type="button"
                data-bs-toggle="collapse" data-bs-target="#costManagment" aria-expanded="true"
                aria-controls="costManagment">
                <div class="d-flex align-items-center">
                    <i class="bi bi-chevron-down me-2"></i>
                    <span class="font-quick ls-1 fw-bold">Cost Managment</span>
                </div>
            </button>
            <div class="btn-group">
                <button 
                    class="btn border-0 text-white font-quick ls-1 fw-bold" 
                    type="button" wire:click="toggleForm">
                    <i class="bi bi-plus-lg"></i>
                </button>
                <button class="btn border-0 text-white font-quick ls-1 fw-bold text-capitalize
                    {{ $decisionLocked ? 'btn-success' : 'btn-danger' }}" type="button" wire:click="lockDecision">
                    {{ $decisionLocked ? 'Locked' : 'Pending' }}
                </button>
            </div>
        </div>
    </div>

    <div class="collapse {{ $decisionLocked ? '' : 'show' }}" id="costManagment">
        <div class="card card-body rounded-0">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim perferendis quasi sequi totam debitis magni recusandae quaerat tempora quo adipisci nisi repellendus obcaecati accusamus minus explicabo sapiente, ab blanditiis nemo.
        </div>
    </div>
</div>