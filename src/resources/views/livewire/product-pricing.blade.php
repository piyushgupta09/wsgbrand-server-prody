<div class="">
    <form>
        <ul class="list-group">
            {{-- @if ($model->vendor)
                <li class="list-group-item rounded-0">
                    <div class="row">
                        <label for="vendorPrice" class="col-sm-2 font-title">
                            <span class="pe-2 fw-500">{{ $startCount + 1 }}.</span>
                            <span class="fw-bold">Vendor Price</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="Enter relevant price" id="vendorPrice" wire:model="vendorPrice" required>
                        </div>
                    </div>
                </li>
            @endif

            @if ($model->factory)
                <li class="list-group-item rounded-0">
                    <div class="row">
                        <label for="factoryPrice" class="col-sm-2 font-title">
                            <span class="pe-2 fw-500">{{ $startCount + 2 }}.</span>
                            <span class="fw-bold">Factory Price</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="Enter relevant price" id="factoryPrice" wire:model="factoryPrice"
                                required>
                        </div>
                    </div>
                </li>
            @endif

            @if ($model->market)
                <li class="list-group-item rounded-0">
                    <div class="row">
                        <label for="marketPrice" class="col-sm-2 font-title">
                            <span class="pe-2 fw-500">{{ $startCount + 3 }}.</span>
                            <span class="fw-bold">Market Price</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="Enter relevant price" id="marketPrice" wire:model="marketPrice"
                                required>
                        </div>
                    </div>
                </li>
            @endif --}}

            @if ($model->ecomm)
                <li class="list-group-item rounded-0">
                    <div class="row">
                        <label for="ecommPrice" class="col-sm-2 font-title">
                            <span class="pe-2 fw-500">1.</span>
                            <span class="fw-bold">Ecommerce Price</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="Enter relevant price" id="ecommPrice" wire:model="ecommPrice" required>
                        </div>
                    </div>
                </li>
            @endif

            @if ($model->retail)
                <li class="list-group-item rounded-0">
                    <div class="row">
                        <label for="retailPrice" class="col-sm-2 font-title">
                            <span class="pe-2 fw-500">2.</span>
                            <span class="fw-bold">Retail Price</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="Enter relevant price" id="retailPrice" wire:model="retailPrice"
                                required>
                        </div>
                    </div>
                </li>
            @endif

            @if ($model->inbulk)
                <li class="list-group-item rounded-0">
                    <div class="row">
                        <label for="inbulkPrice" class="col-sm-2 font-title">
                            <span class="pe-2 fw-500">3.</span>
                            <span class="fw-bold">Inbulk Price</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="Enter relevant price" id="inbulkPrice" wire:model="inbulkPrice"
                                required>
                        </div>
                    </div>
                </li>
            @endif

            @if ($model->offline)
                <li class="list-group-item rounded-0">
                    <div class="row">
                        <label for="offlinePrice" class="col-sm-2 font-title">
                            <span class="pe-2 fw-500">4.</span>
                            <span class="fw-bold">Offline Price</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" placeholder="Enter relevant price" id="offlinePrice" wire:model="offlinePrice"
                                required>
                        </div>
                    </div>
                </li>
            @endif
        </ul>
    </form>
</div>
