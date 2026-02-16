@extends('layouts.master')

@section('main-content')

<div class="card shadow mb-4">

    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>

    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">
            Professional Qualifications
        </h6>

        <a href="{{ route('prof-qualifications.create') }}"
           class="btn btn-primary btn-sm float-right">
            <i class="fas fa-plus"></i> Add Qualification
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="prof-qualifications-table"
                   class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Qualification</th>
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

    $('#prof-qualifications-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('prof-qualifications.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });

});
</script>
@endpush
