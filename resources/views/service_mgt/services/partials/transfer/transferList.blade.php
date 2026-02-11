@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Users List</h6>
    </div>
    <div class="card-body">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        <label><strong>Status :</strong></label>
                        <select name="status" id="status" class="form-control form-control-sm">
                            <option value="">--Select Status--</option>
                            <option value="0">Pending</option>
                            <option value="0">Completed</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @can('prpocess-create')
    <a href="{{route('process.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add Transfer"><i class="fas fa-plus"></i> Add Transfer</a>
    @endcan
        <div class="table-responsive">
            <table class="table table-bordered" id="employees">
                <thead>
                    <th>Id</th>
                    <th>ProcessID</th>
                    <th>Name</th>
                    <th>Transfer From</th>
                    <th>Transfer To</th>
                    <th>Effective Date</th>
                    <th>PF Officer</th>
                    <th>Is Approved</th>
                    <th>Is Printed</th>
                </thead>				
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    var table = $('#employees').DataTable({
        processing: true,
        serverSide: true,
        stateSave:true,
        paging: true,
        autoWidth: true,
        ajax: {
          url: "{{ route('transfer.index') }}",
          data: function (d) {
                _token: "{{csrf_token()}}"
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'process_id', name: 'process_id',render:function(data, type, row){
    return "<a href='/process/"+ row.process_id +"/edit'>" + row.process_id + "</a>"
}},
            {data: 'employee_id', name: 'employee.surname'},
            {data: 'transfer_from', name: 'institute1.institute'},
            {data: 'transfer_to', name: 'institute.institute'},
            {data: 'effect_from', name: 'effect_from'},
            {data: 'pfclerk', name: 'employee.institute1.pfclerk.name'},
            {data: 'is_approved', name: 'is_approved'},
            {data: 'is_printed', name: 'is_printed'},
        ]
            
    });
});
</script>
@endpush
