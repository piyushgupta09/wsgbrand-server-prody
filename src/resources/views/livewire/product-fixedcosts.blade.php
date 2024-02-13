<div class="card border-0">
    @if (isset($fixedcosts) && $fixedcosts->isNotEmpty())
        <div class="collapse show" id="productOverheadList">
            <div class="card-body p-0">
                <ul class="list-group list-group-flushed">
                    @foreach ($fixedcosts as $key => $fixedcost)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex">
                                <span class="pe-2">{{ $loop->iteration }}.</span>
                                <span class="fw-500">{{ $fixedcost->name }}</span>
                            </div>
                            <span class="fw-500">{{ $fixedcost->rate }}</span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>
