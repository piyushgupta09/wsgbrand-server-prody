<button class="btn btn-outline-dark border rounded-0 ls-1 fw-bold me-auto mb-2" 
    type="button" wire:click="toggleForm">
    <span x-data="{ show: @entangle('showForm') }">
        <i x-show="show" class="bi bi-x-lg"></i>
    </span>
    <span x-data="{ show: @entangle('showForm').defer }">
        <i x-show="!show" class="bi bi-plus-lg"></i>
    </span>
    <span class="ps-2">Add New</span>
</button>