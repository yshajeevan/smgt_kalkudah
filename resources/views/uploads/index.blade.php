@extends('layouts.master')

@section('main-content')
<div class="card">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">PDFs</h6>
        <a href="{{ route(request()->segment(1) . '.create') }}" class="btn btn-sm btn-primary float-right">Add PDF</a>
    </div>
    <div class="card-body">
        <div class="form-group row">
            <div class="col-md-3">
                <label for="type_filter">Type</label>
                <select id="type_filter" class="form-control">
                    <option value="">All</option>
                    <option value="circular">Circulars</option>
                    <option value="guideline">Guidelines</option>
                    <option value="form">Forms and Applications</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="released_year_filter">Released Year</label>
                <select id="released_year_filter" class="form-control">
                    <option value="">All</option>
                    @for ($year = 1950; $year <= date('Y'); $year++)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label for="releasedby_filter">Released By</label>
                <select id="releasedby_filter" class="form-control">
                    <option value="">All</option>
                    <option value="pubad">PUBAD</option>
                    <option value="moe">MOE</option>
                    <option value="pmoe_ep">PMOE-EP</option>
                    <option value="pde_ep">PDE-EP</option>
                    <option value="zone">Zone</option>
                    <option value="others">Others</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table id="pdfs" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Description</th>
                        <th>Name</th>
                        <th>Released Year</th>
                        <th>Released By</th>
                        <th>View File</th>
                        <th>Is_website</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    var table = $('#pdfs').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('upload.index') }}",
            type: 'GET',
            data: function (d) {
                d.type = $('#type_filter').val();
                d.released_year = $('#released_year_filter').val();
                d.releasedby = $('#releasedby_filter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'description', name: 'description' },
            { data: 'name', name: 'name' },
            { data: 'released_year', name: 'released_year' },
            { data: 'releasedby', name: 'releasedby' },
            { data: 'view', name: 'view', orderable: false, searchable: false },
            { data: 'is_website', name: 'is_website', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });

    // Trigger refresh on filter change
    $('.form-control').on('change', function () {
        table.ajax.reload();
    });
});
</script>
@endsection
