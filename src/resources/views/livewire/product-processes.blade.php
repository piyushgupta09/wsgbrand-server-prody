<div class="card border-0">

    <div class="card-header rounded-0 bg-dark py-2 d-flex justify-content-between align-items-center w-100">
        <div class="d-flex align-items-center flex-fill">
            <button class="btn border-0 ps-0 text-white" type="button" data-bs-toggle="collapse"
                data-bs-target="#productProcessesList" aria-expanded="true" aria-controls="productProcessesList">
                <i class="bi bi-chevron-down me-2"></i>
                <span class="font-quick ls-1 fw-bold">Factory Process & Cost</span>
            </button>
        </div>
        <div class="">
            <button class="btn border-0 ps-0 text-white" type="button" data-bs-toggle="modal"
                data-bs-target="#previewCostsheet">
                <i class="bi bi-printer me-2"></i>
            </button>
            <button class="btn border-0 text-white font-quick ls-1 fw-bold" type="button" wire:click='reloadData'>
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <button class="btn border-0 text-white font-quick ls-1 fw-bold" type="button" wire:click="toggleForm">
                <span x-data="{ show: @entangle('showForm') }">
                    <i x-show="show" class="bi bi-x-lg"></i>
                </span>
                <span x-data="{ show: @entangle('showForm').defer }">
                    <i x-show="!show" class="bi bi-plus-lg"></i>
                </span>
            </button>
        </div>
    </div>

    <div x-data="{ show: @entangle('showForm') }" x-show="show" x-transition.delay.100ms>
        <form wire:submit.prevent="{{ $formType === 'create' ? 'store' : 'update' }}">
            <div class="card-body p-0 text-bg-secondary">
                <div class="row m-0 py-3">
                    <p class="font-robot ls-1">Process Details</p>

                    {{-- Enter Process Stage --}}
                    <div class="col-md-4 mb-3">
                        <div class="form-floating">
                            <input type="text" id="processStage" class="form-control" wire:model="stage" required>
                            <label for="processStage" class="font-quick text-dark font-normal">Stage</label>
                            <small></small>Enter the stage of the process (e.g., Cutting, Stitching).</small>
                            @error('stage')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Select Nature --}}
                    <div class="col-md-4 mb-3">
                        <div class="form-floating">
                            <select class="form-select" id="processNature" wire:model="nature" required
                                aria-label="Choose nature">
                                <option value="0">Indirect</option>
                                <option value="1">Direct</option>
                            </select>
                            <label for="processNature" class="font-quick text-dark font-normal">Nature</label>
                            <small>Select the nature of the process (Direct or Indirect).</small>
                            @error('nature')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Process Name --}}
                    <div class="col-md-4 mb-3">
                        <div class="form-floating">
                            <input type="text" id="processName" class="form-control" wire:model="name" required>
                            <label for="processName" class="font-quick text-dark font-normal">Name</label>
                            <small>Specify the name of the process (e.g., Layer Cutting).</small>
                            @error('name')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Process Cost --}}
                    <div class="col-md-4 mb-3">
                        <div class="form-floating">
                            <input type="text" id="processCost" class="form-control" wire:model="cost" required>
                            <label for="processCost" class="font-quick text-dark font-normal">Cost</label>
                            <small>Enter the cost of this process in INR.</small>
                            @error('cost')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Process Time --}}
                    <div class="col-md-4 mb-3">
                        <div class="form-floating">
                            <input type="text" id="processTime" class="form-control" wire:model="time" required>
                            <label for="processTime" class="font-quick text-dark font-normal">Time</label>
                            <small>Input the time required to complete this process in minutes.</small>
                            @error('time')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Process Order --}}
                    <div class="col-md-4 mb-3">
                        <div class="form-floating">
                            <input type="number" id="processOrder" class="form-control" wire:model="order" required>
                            <label for="processOrder" class="font-quick text-dark font-normal">Order</label>
                            <small>Set the order of this process in the production sequence.</small>
                            @error('order')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Instructions --}}
                    <div class="col-12 mb-3">
                        <div class="form-floating">
                            <textarea id="processInstructions" class="form-control" wire:model="instructions"
                                style="height: 100px;"></textarea>
                            <label for="processInstructions"
                                class="font-quick text-dark font-normal">Instructions</label>
                            <small>Provide any specific instructions to complete this process.</small>
                            @error('instructions')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Description --}}
                    <div class="col-12 mb-3">
                        <div class="form-floating">
                            <textarea id="processDescription" class="form-control" wire:model="description"
                                style="height: 100px;"></textarea>
                            <label for="processDescription" class="font-quick text-dark font-normal">Description</label>
                            <small>Describe the process for internal understanding or documentation.</small>
                            @error('description')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Enter Special Note --}}
                    <div class="col-12 mb-3">
                        <div class="form-floating">
                            <textarea id="processSpecialNote" class="form-control" wire:model="specialNote"
                                style="height: 100px;"></textarea>
                            <label for="processSpecialNote" class="font-quick text-dark font-normal">Special
                                Note</label>
                            <small>Add any special notes or considerations for this process.</small>
                            @error('specialNote')
                            <span style="font-size: 0.75rem" class="text-bg-danger error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="btn-group w-50 ms-auto">
                        <button type="button" wire:click.prevent="resetForm()"
                            class="btn btn-outline-light">Reset</button>
                        <button type="submit" class="btn btn-info">{{ $formType === 'create' ? 'Save' : 'Update'
                            }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @if (isset($processes) && $processes->isNotEmpty())
        <div class="collapse show" id="productProcessesList">
            <div class="card-body p-0">
                <ul class="list-group list-group-flushed">
                    @foreach ($processes as $key => $process)
                    <li class="list-group-item rounded-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="pe-2">{{ $process->order }}.</span>
                                <strong>{{ $process->name }}</strong> - {{ $process->stage }}
                                ({{ $process->nature ? 'Direct' : 'Indirect' }})
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-outline-dark border-secondary py-1 px-3" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseProcessDetail{{ $key }}"
                                    aria-expanded="false" aria-controls="collapseProcessDetail{{ $key }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-dark" wire:click="edit({{ $process->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-dark" wire:click="delete({{ $process->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="collapse" id="collapseProcessDetail{{ $key }}">
                            <div class="card-body">
                                <p><strong>Cost:</strong> {{ $process->cost }} INR</p>
                                <p><strong>Time:</strong> {{ $process->time }} minutes</p>
                                <p><strong>Instructions:</strong> {{ $process->instructions }}</p>
                                <p><strong>Description:</strong> {{ $process->description }}</p>
                                <p><strong>Special Note:</strong> {{ $process->special_note }}</p>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if ($product->productMaterials && $product->productProcesses && $overheads)
        <div class="modal fade" id="previewCostsheet" tabindex="-1" aria-labelledby="previewCostsheetLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <h3 class="modal-title fs-5" id="previewCostsheetLabel">{{ $product->name }} Costsheet -  ₹ {{ number_format($totalCost + $overheads->sum('cost')) }}</h3>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn lh-1" wire:click="printout">
                                <i class="bi bi-printer fs-5"></i>
                            </button>
                            <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                                <i class="bi bi-x-lg fs-5"></i>
                            </button>
                        </div>
                    </div>
                    <div class="modal-body p-0">

                        {{-- Material Cost --}}
                        <table class="table table-borderless mb-4">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3 text-start">#</th>
                                    <th class="text-start">Material Name</th>
                                    <th class="text-start">Position</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Rate</th>
                                    <th class="pe-3 text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->productMaterials as $productMaterial)
                                    @php
                                        $pomr = $productMaterial->product->pomrs->where('product_material_id', $productMaterial->id)->first();
                                    @endphp
                                    @if($pomr)
                                        <tr>
                                            <td class="ps-3 text-start">{{ $loop->iteration }}</td>
                                            <td class="text-start">{{ $productMaterial->material->name }}</td>
                                            <td class="text-start">{{ $pomr->name }}</td>
                                            <td class="text-end">{{ number_format($pomr->quantity, 2) }} {{ $pomr->unit }}</td>
                                            <td class="text-end">{{ number_format($pomr->cost) }}</td>
                                            <td class="pe-3 text-end">{{ number_format($pomr->quantity * $pomr->cost) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th class="ps-3 text-start">A.</th>
                                    <th class="text-start" colspan="4">Total Material Cost</th>
                                    <th class="pe-3 text-end">₹ {{ number_format($totalMaterialCost) }}</th>
                                </tr>    
                            </tfoot>                       
                        </table>
                        
                        {{-- Process Cost --}}
                        <table class="table table-borderless mb-4">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3 text-start">#</th>
                                    <th class="text-start">Process Name</th>
                                    <th class="text-start">Stage</th>
                                    <th class="text-end">Nature</th>
                                    <th class="text-end">Time</th>
                                    <th class="pe-3 text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $processes = $product->productProcesses->sortBy('order');
                                @endphp
                                @foreach($processes as $process)
                                <tr>
                                    <td class="ps-3 text-start">{{ $process->order }}</td>
                                    <td class="text-start">{{ $process->name }}</td>
                                    <td class="text-start">{{ $process->stage }}</td>
                                    <td class="text-end">{{ $process->nature ? 'Direct' : 'Indirect' }}</td>
                                    <td class="text-end">{{ $process->time }} min</td>
                                    <td class="pe-3 text-end">{{ number_format($process->cost) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th class="ps-3 text-start">B.</th>
                                    <th class="text-start" colspan="4">Total Process Cost</th>
                                    <th class="pe-3 text-end">₹ {{ number_format($totalProcessCost) }}</th>
                                </tr> 
                            </tfoot>
                        </table>

                        {{-- Overhead Cost --}}
                        <table class="table table-borderless mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3 text-start">#</th>
                                    <th class="text-start">Overhead Name</th>
                                    <th class="text-start">Stage</th>
                                    <th class="text-end">Capacity</th>
                                    <th class="text-end">Ratio</th>
                                    <th class="pe-3 text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overheads as $productOverhead)
                                <tr>
                                    <td class="ps-3 text-start">{{ $loop->iteration }}</td>
                                    <td class="text-start">{{ $productOverhead->overhead->name }}</td>
                                    <td class="text-start">{{ $productOverhead->overhead->stage }}</td>
                                    <td class="text-end">{{ $productOverhead->overhead->capacity }}</td>
                                    <td class="text-end">{{ $productOverhead->ratio }}</td>
                                    <td class="pe-3 text-end">{{ number_format($productOverhead->cost) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th class="ps-3 text-start">C.</th>
                                    <th class="text-start" colspan="4">Total Overhead Cost</th>
                                    <th class="pe-3 text-end">₹ {{ number_format($overheads->sum('cost')) }}</th>
                                </tr> 
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

@section('scripts')
    <script>
        window.addEventListener('print-costsheet', event => {
            var printContents = document.getElementById('previewCostsheet').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        });
    </script>
@endsection
