@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
     </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Degrees</h6>
        <a href="{{ route('degrees.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Add Degree</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="degrees-table" class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Degree</th>
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
        $('#degrees-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('degrees.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'degree', name: 'degree' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    });
</script>
@endpush
