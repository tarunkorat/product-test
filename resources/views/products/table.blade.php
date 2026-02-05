<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Datetime</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @php $grand = 0; @endphp
        @foreach ($products as $p)
            @php
                $total = $p['quantity'] * $p['price'];
                $grand += $total;
            @endphp
            <tr>
                <td>{{ $p['name'] }}</td>
                <td>{{ $p['quantity'] }}</td>
                <td>{{ $p['price'] }}</td>
                <td>{{ $p['submitted_at'] }}</td>
                <td>{{ number_format($total, 2) }}</td>
                <td>
                    <button class="btn btn-sm btn-warning edit" data-id="{{ $p['id'] }}">Edit</button>
                    <button class="btn btn-sm btn-danger delete" data-id="{{ $p['id'] }}">Delete</button>
                </td>
            </tr>
        @endforeach
        <tr class="fw-bold">
            <td colspan="4">Grand Total</td>
            <td>{{ number_format($grand, 2) }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
