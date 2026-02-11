@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">   
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
     </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Buildings</h6>
        <a href="{{ route('buildings.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Add Building</a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filter-institute">Filter by Institute</label>
                <select id="filter-institute" class="form-control">
                    <option value="">All Institutes</option>
                    @foreach($institutes as $institute)
                        <option value="{{ $institute->id }}">{{ $institute->institute }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
        <label for="filter-type">Filter by Type</label>
            <select id="filter-type" class="form-control">
                <option value="">All Types</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="filter-usage">Filter by Status</label>
            <select id="filter-usage" class="form-control">
                <option value="">All Usage</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
            <div class="col-md-3">
                <label for="filter-repairs">Show Buildings with Repairs</label>
                <select id="filter-repairs" class="form-control">
                    <option value="">All Buildings</option>
                    <option value="1">With Repairs</option>
                    <option value="0">Without Repairs</option>
                </select>
            </div>

        </div>
        <div class="table-responsive">
            <table id="buildings-table" class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Building Name</th>
                        <th>Institute</th>
                        <th>Size</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Usage</th>
                        <th>Constructed On</th>
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
        let table = $('#buildings-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
            url: "{{ route('buildings.index') }}",
                data: function (d) {
                    d.institute_id = $('#filter-institute').val(); // Institute filter
                    d.type_id = $('#filter-type').val();          // Type filter
                    d.usage = $('#filter-usage').val();           // Usage filter
                    d.has_repairs = $('#filter-repairs').val();   // Repairs filter
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name_with_repairs', name: 'name_with_repairs', orderable: false, searchable: true },
                { data: 'institute', name: 'institute' },
                { data: 'size', name: 'size' },
                { data: 'category', name: 'category' },
                { data: 'type', name: 'type' },
                { data: 'usage', name: 'usage' }, // Display Active/In-active
                { data: 'constructed_on', name: 'constructed_on' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        // Event listener for institute filter
        $('#filter-institute, #filter-type, #filter-usage, #filter-repairs').change(function() {
            table.draw();
        });
    });
</script>
@endpush