@if ($productMaterials && $overheads && $consumables && $fixedcosts)
    <div id="previewCostsheet" class="card">
        <div class="card-header p-2 d-flex justify-content-between">
            <h3 class="modal-title fs-5" id="previewCostsheetLabel">
                #{{ $product->code }} - <span class="text-bg-success px-2 py-1">₹ {{ number_format($totalCost) }}/pc</span>
            </h3>
            <div class="d-flex align-items-center">
                <button type="button" class="btn lh-1" wire:click="printout">
                    <i class="bi bi-printer fs-5"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">

            {{-- Material Cost --}}
            <table class="table table-borderless mb-4">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3 text-start" style="width: 50px">#</th>
                        <th class="text-start">Material Name</th>
                        <th class="text-start">Position</th>
                        <th class="text-end">Quantity</th>
                        <th class="text-end">Rate</th>
                        <th class="pe-3 text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productMaterials as $productMaterial)
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
            
            {{-- Fixed Cost --}}
            <table class="table table-borderless mb-4">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3 text-start" style="width: 50px">#</th>
                        <th class="text-start">Fixed Cost</th>
                        <th class="text-start" colspan="3">Description</th>
                        <th class="pe-3 text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fixedcosts as $fixedcost)
                    <tr>
                        <td class="ps-3 text-start">{{ $loop->iteration }}</td>
                        <td class="text-start">{{ $fixedcost->name }}</td>
                        <td class="text-start" colspan="3">{{ $fixedcost->details }}</td>
                        <td class="pe-3 text-end">{{ number_format($fixedcost->rate) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th class="ps-3 text-start">B.</th>
                        <th class="text-start" colspan="4">Total Fixed Cost</th>
                        <th class="pe-3 text-end">₹ {{ number_format($fixedcosts->sum('rate')) }}</th>
                    </tr> 
                </tfoot>
            </table>

             {{-- Overhead Cost --}}
             <table class="table table-borderless mb-4">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3 text-start" style="width: 50px">#</th>
                        <th class="text-start">Overhead</th>
                        <th class="text-start">Stage</th>
                        <th class="text-end">Rate</th>
                        <th class="text-end">Consumption</th>
                        <th class="pe-3 text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overheads as $productOverhead)
                    <tr>
                        <td class="ps-3 text-start">{{ $loop->iteration }}</td>
                        <td class="text-start">{{ $productOverhead->overhead->name }}</td>
                        <td class="text-start">{{ $productOverhead->overhead->stage }}</td>
                        <td class="text-end">{{ $productOverhead->rate }}</td>
                        <td class="text-end">{{ $productOverhead->ratio }}</td>
                        <td class="pe-3 text-end">{{ number_format($productOverhead->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th class="ps-3 text-start">C.</th>
                        <th class="text-start" colspan="4">Total Overhead Cost</th>
                        <th class="pe-3 text-end">₹ {{ number_format($overheads->sum('amount')) }}</th>
                    </tr> 
                </tfoot>
            </table>

            {{-- Consumable Cost --}}
            <table class="table table-borderless mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3 text-start" style="width: 50px">#</th>
                        <th class="text-start">Consumable</th>
                        <th class="text-start">Description</th>
                        <th class="text-end">Rate</th>
                        <th class="text-end">Consumption</th>
                        <th class="pe-3 text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consumables as $productConsumable)
                    <tr>
                        <td class="ps-3 text-start">{{ $loop->iteration }}</td>
                        <td class="text-start">{{ $productConsumable->consumable->name }}</td>
                        <td class="text-start">{{ $productConsumable->consumable->description }}</td>
                        <td class="text-end">{{ $productConsumable->rate }}/{{ $productConsumable->consumable->unit }}</td>
                        <td class="text-end">{{ $productConsumable->ratio }}</td>
                        <td class="pe-3 text-end">{{ number_format($productConsumable->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th class="ps-3 text-start">D.</th>
                        <th class="text-start" colspan="4">Total Consumable Cost</th>
                        <th class="pe-3 text-end">₹ {{ number_format($consumables->sum('amount')) }}</th>
                    </tr> 
                </tfoot>
            </table>

        </div>
    </div>
@endif

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