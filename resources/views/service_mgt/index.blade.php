@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">@if($pageid == 1) User's Pending Processes @elseif ($pageid == 2) Zonal Pending Processes @elseif ($pageid == 3) Completed Process @else Holding Process @endif</h6>
        @can('process-create')
            <a href="{{route('process.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Process</a>
        @endcan
    </div>
    <div class="card-body">
      <div class="table">
            <table class="table table-bordered" id="process">
                <thead>
                    <th class="all">Id</th>
                    <th class="all">Employee</th>
                    <th>Designation</th>
                    <th style="width:250px">Service</th>
                    @if($pageid != 3)
                    <th>CRES</th>
                    @else
                    <th>Institute</th>
                    @endif
                    <th>Progress</th>
                    <th style="width:50px">Time Scale</th>
                    <th class="all">Action</th>
                </thead>				
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet" />
<style>
@media (max-width: 767px){
    .pagination .paginate_button:not(.previous):not(.next):not(.first):not(.last){
        display: none;
    }
}
.text-wrap{
    white-space:normal;
}
.width-200{
    width:150px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script>
    $(document).ready(function () {
        $('#process').DataTable({
            "processing": true,
            "serverSide": true,
            "oLanguage": {
              "oPaginate": {
                "sNext": '<i class="fas fa-angle-double-right"></i>',
                "sPrevious": '<i class="fas fa-angle-double-left"></i>'
              }
            },
            "pagingType": $(window).width() < 768 ? "full" : "simple_numbers",
            "columnDefs": [{
            targets: 5,
            render: $.fn.dataTable.render.percentBar('round','#fff', '#FF9CAB', '#FF0033', '#FF9CAB', 0, 'solid')
            }],
            responsive: {
		    },
	
            "ajax":{
                     "url": "{{ url('getprocess',$pageid) }}",
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "id" },
                { "data": "surname",
                    render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-200'>" + data + "</div>";
                    } 
                },
                { "data": "designation" },
                { "data": "service" },
                { "data": "cres" },
                { "data": "progress" },
                { "data": "emogi",
                    render: function( data, type, full, meta ) {
                        return "<img src=\"/backend/img/" + data + "\" width='30px'>";
                    }
                },
                { "data": "options" }
            ]
        });
    });
</script>
@endpush
