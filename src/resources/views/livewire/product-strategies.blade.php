<div class="">

    @php
        // set counter
        $counter = 0;
    @endphp

    @foreach ($decisions as $decision)

        @if ($decision['active'])

            @php
                // increment counter
                $counter++;
            @endphp

            <div class="card rounded-0 mb-2">
                <div class="card-header d-flex text-bg-secondary justify-content-between">
                    <div class="d-flex flex-column">
                        <span class="fw-bold ls-1">{{ $counter }}. {{ $decision['name'] }}</span>
                        <span class="small ps-3 ls-1 lh-1">{{ $decision['details'] }}</span>
                    </div>
                    <button class="btn btn-light px-3 btn-sm font-title ls-1 fw-bold" 
                        wire:click="saveDecision('{{ $decision['type'] }}')">
                        Save
                    </button>
                </div>
                <div class="card-body px-3 pb-2">
                    <div class="row">
                        @foreach ($decisionGroups as $group)
                            <div class="col-md-6 px-2">
                                <div class="form-floating mb-2">
                                    <select class="form-select" id="floatingSelect{{ $group['slug'] }}" 
                                        wire:model="selected.{{ $decision['type'] }}.{{ $group['slug'] }}"
                                        aria-label="{{ $group['name'] }} option selection">
                                        <option value="">Select {{ $group['name'] }}</option>
                                        @foreach ($group['items'] as $item)
                                            <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <label for="floatingSelect{{ $group['slug'] }}" class="text-uppercase text-dark fw-bold ls-1">{{ $group['name'] }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        @endif

    @endforeach

</div>
