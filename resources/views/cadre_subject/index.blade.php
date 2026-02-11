@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
     </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Cadre Subjects</h6>
        <a href="{{ route('cadre-subject.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Add Cadre Subject</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="cadre-subject-table" class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cadre</th>
                        <th>Cadre Code</th>
                        <th>Category</th>
                        <th>Subject Number</th>
                        <th>Category 2</th>
                        <th>App Cadre</th>
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
        $('#cadre-subject-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('cadre-subject.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'cadre', name: 'cadre' },
                { data: 'cadre_code', name: 'cadre_code' },
                { data: 'category', name: 'category' },
                { data: 'subject_number', name: 'subject_number' },
                { data: 'category2', name: 'category2' },
                { data: 'app_cadre', name: 'app_cadre' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    });
</script>
@endpush
