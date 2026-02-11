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
        @can('service-create')
        <a href="{{route('service.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Service</a>
        @endcan
      <div class="table-responsive">
               <table class="table table-bordered" id="employees">
                    <thead>
                           <th>Id</th>
                           <th>Service</th>
                           <th>Branch</th>
                           <th>Total Process</th>
                           <th>Allocated Time</th>
                           <th>Options</th>
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
        dom: '<"dt-buttons"Bf><"clear">lirtp',
        paging: true,
        autoWidth: true,
        buttons: [
				'colvis',
				'copyHtml5',
                'csvHtml5',
				'excelHtml5',
                'pdfHtml5',
				'print'
			    ],
        ajax: {
          url: "{{ route('services.index') }}",
          data: function (d) {
                _token: "{{csrf_token()}}",
                d.search = $('input[type="search"]').val()
            }
        },
        columns: [
            {data: 'DT_RowIndex',orderable: false, searchable: false},
            {data: 'service', name: 'service'},
            {data: 'branch', name: 'branch'},
            {data: 'countres', name: 'countres'},
            {data: 'timeallocated', name: 'timeallocated'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

});
</script>
@endpush
