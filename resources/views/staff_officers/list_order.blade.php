@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Set Staff List Order</h6>
    </div>
    <div class="card-body">
        <ul id="sortable" class="list-group">
            @foreach ($staff as $item)
                <li class="list-group-item" data-id="{{ $item->id }}">
                    <span class="font-weight-bold">{{ $item->name }}</span>
                    <span class="text-muted float-right">Order: {{ $item->list_order }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(document).ready(function () {
        // Enable sortable on the list
        $("#sortable").sortable({
            update: function (event, ui) {
                let order = [];
                // Loop through each list item to get the order
                $('#sortable li').each(function (index, element) {
                    order.push({
                        id: $(element).data('id'),
                        position: index + 1
                    });
                });

                // Send the order to the server
                $.ajax({
                    url: "{{ route('staff.list_order_store', 'list_order') }}", // Route to update order
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        order: order
                    },
                    success: function (response) {
                        if (response.success) {
                            alert('List order updated successfully!');
                        } else {
                            alert('Something went wrong while updating the list order.');
                        }
                    },
                    error: function () {
                        alert('Error in saving the new order.');
                    }
                });
            }
        });
    });
</script>
@endpush
