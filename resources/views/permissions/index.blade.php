@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>

    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Permissions List</h6>
        @can('permission-create')
        <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Add Permission">
            <i class="fas fa-plus"></i> Add Permission
        </a>
        @endcan
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="permissiontbl" class="table table-bordered data-table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th width="150px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dataTables_wrapper .dt-buttons {
        float: left;
        margin-bottom: 10px;
    }
</style>
@endpush

@push('scripts')
<script>
$(function() {
    var table = $('#permissiontbl').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('permissions.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        dom: '<"dt-buttons"Bf><"clear">lirtp',
        buttons: ['colvis', 'copyHtml5', 'csvHtml5', 'excelHtml5', 'pdfHtml5', 'print']
    });

    // Confirm delete
    $(document).on('submit', '.delete-form', function(e) {
        if (!confirm('Are you sure you want to delete this permission?')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
