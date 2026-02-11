@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
     </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Building Repair Categories</h6>
        <a href="{{ route('building-repair-categories.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Add Repair Category</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="repair-categories-table" class="table table-bordered data-table">
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
        $('#repair-categories-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('building-repair-categories.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    });
</script>
@endpush
