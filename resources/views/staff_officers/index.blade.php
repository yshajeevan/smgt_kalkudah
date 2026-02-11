@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
         <div class="col-md-12">
            @include('layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Staff</h6>
        <a href="{{ route('staff.list_order') }}" class="btn btn-success btn-sm float-right"><i class="fas fa-list"></i> Set Order</a>
        <a href="{{ route('staff.create') }}" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Add Staff</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="staff-table" class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Branch</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>WhatsApp</th>
                        <th>Image</th>
                        <th>Website</th>
                        <th>Order</th>
                        <th>Options</th>
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
    .img-circle {
    border-radius: 50%;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#staff-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('staff.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'designation', name: 'designation' },
            { data: 'branch', name: 'branch' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'whatsapp', name: 'whatsapp' },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'is_website', name: 'is_website' },
            { data: 'list_order', name: 'list_order' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });
});
</script>
@endpush
