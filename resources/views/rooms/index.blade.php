@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">   
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
     </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Rooms</h6>
        <a href="{{ route('rooms.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Add Room</a>
    </div>
    <div class="card-body">
        @if(isset($buildingName) && isset($instituteName))
            <div class="alert-info p-2">
                Showing rooms for building: <strong>{{ $buildingName }}</strong> (Institute: <strong>{{ $instituteName }}</strong>)
            </div>
        @elseif(isset($buildingName))
            <div class="alert-info p-2">
                Showing rooms for building: <strong>{{ $buildingName }}</strong>
            </div>
        @endif
        <div class="table-responsive">
            <table id="rooms-table" class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Building</th>
                        <th>Room Type</th>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Status</th>
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
        let buildingId = "{{ request()->get('building_id') }}";

        $('#rooms-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('rooms.index') }}",
                data: function(d) {
                    d.building_id = buildingId; // Pass building_id as part of the request
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'building', name: 'building' },
                { data: 'room_type', name: 'room_type' },
                { data: 'name', name: 'name' },
                { data: 'size', name: 'size' },
                { data: 'is_available', name: 'is_available' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    });
</script>
@endpush
