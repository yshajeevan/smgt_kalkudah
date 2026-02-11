@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">   
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
     </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Room Types</h6>
        <a href="{{ route('room-types.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Add Room Type</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="room-types-table" class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#room-types-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('room-types.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    });
</script>
@endpush
