<div>
    <label for="inputproductc-name" class="form-label">Size Charts</label>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                @foreach ($sizes as $size)
                    @if ($loop->first)
                        @foreach ($size->sizeCharts as $chart)
                            <th scope="col">
                                {{ $chart->name }}
                            </th>
                        @endforeach
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($sizes as $size)
                <tr>
                    @foreach ($size->sizeCharts as $chart)
                        @if ($loop->first)
                            <td>{{ $chart->size->name }}</td>
                        @endif
                        <td>{{ $chart->value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
