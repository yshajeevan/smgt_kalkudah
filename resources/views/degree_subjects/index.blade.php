@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
     </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Degree Subjects</h6>
        <a href="{{ route('deg-subjects.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Add Subject</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="deg-subjects-table" class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
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
        $('#deg-subjects-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('deg-subjects.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'degreesub', name: 'degreesub' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    });
</script>
@endpush
