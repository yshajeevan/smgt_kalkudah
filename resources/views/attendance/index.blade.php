@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">@if($uname != 'Sch_Admin' && $currentPath != 'schoolatten') Zonal Attendance @else {{ $institute }} @endif</h6>
        @can('attendance-create')
            @if($uname != 'Sch_Admin')
                <a href="{{route('submitform.index',4)}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add Atendance"><i class="fas fa-plus"></i> Add Attendance</a>
            @else
                <a href="{{route('attendance.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add Atendance"><i class="fas fa-plus"></i> Add Attendance</a>
            @endif
        @endcan
    </div>
    <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="from_date" id="from_date" class="form-control input-daterange1" placeholder="From Date" autocomplete="off">
                </div>
                <!--<div class="col-md-4">-->
                <!--    <input type="text" name="to_date" id="to_date" class="form-control input-daterange2" placeholder="To Date" autocomplete="off">-->
                <!--</div>-->
                <div class="col-md-2">
                    <button type="button" name="filter" id="filter" class="btn btn-primary">Filter</button>
                    <button type="button" name="refresh" id="refresh" class="btn btn-default">Refresh</button>
                </div>
                @if($uname != 'Sch_Admin' && $currentPath != 'schoolatten')
                <div class="col-md-2">
                    <div class="nbox float-right">
                        {{$countatten}}
                    </div>
                </div>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="attendance">
                        <thead>
                            @if($currentPath == 'attendance-schools')
                            <th>S/N</th>
                            <th>Institute</th>
                            @endif
                            @if($currentPath != 'attendance-schools')
                            <th>Date</th>
                            @endif
                            <th>Principal</th>
                            <th>Total Students</th>
                            <th>Presented Studnts</th>
                            <th>%</th>
                            <th>Total Teachers</th>
                            <th>Presented Teachers</th>
                            <th>%</th>
                            <th>Total DOs</th>
                            <th>Presented DOs</th>
                            <th>%</th>
                            <th>Total Non-Acedamic</th>
                            <th>Presented Non-Acedamic</th>
                            <th>%</th>
                        </thead>				
                </table>
            </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />

<style>
td {
text-align:center;
}
div.nbox {
  position: relative;
  /*top: 5px;*/
  /*right: 15px;*/
  width: 30px;
  height: 30px;
  border: 3px solid #73AD21;
  text-align: center;
}

</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.3.1/js/dataTables.fixedHeader.min.js"></script>
<script>
$(document).ready(function () {
    $('.input-daterange1').datepicker({
        todayBtn:'linked',
        format:'yyyy-mm-dd',
        autoclose:true
    });

    $('.input-daterange2').datepicker({
        todayBtn:'linked',
        format:'yyyy-mm-dd',
        autoclose:true
    });

    var table = $('#attendance').DataTable({
        processing: true,
        serverSide: true,
        stateSave:true,
        // responsive: true,
        paging: false,
        ordering: false,
        dom: '<"dt-buttons"Bf><"clear">lirtp',
        autoWidth: true,
        buttons: [
				'colvis',
				'copyHtml5',
                'csvHtml5',
				'excelHtml5',
                'pdfHtml5',
				'print'
			    ],
		columnDefs: [
            {
                targets: 1,
                className: 'dt-body-left'
            }
          ],
        ajax: {
          url: "{{ route('attendance.index') }}",
          data: function (d) {
                _token: "{{csrf_token()}}"
                d.search = $('input[type="search"]').val(),
                d.from_date = $('#from_date').val(),
                d.to_date = $('#to_date').val(),
                d.insid = {!! json_encode($insid, JSON_HEX_TAG) !!}
                d.currentPath = {!! json_encode($currentPath, JSON_HEX_TAG) !!}
                
            }
        },
        columns: [
            @if($currentPath == 'attendance-schools')
            {data: 'DT_RowIndex',orderable: false, searchable: false},
            {data: 'institute', name: 'institute.institute'},
            @endif
            @if($currentPath != 'attendance-schools')
            {data: 'created_at', name: 'created_at'},
            @endif
            {data: 'principal', name: 'principal'},
            {data: 'totstu', name: 'totstu'},
            {data: 'prstu', name: 'prstu'},
            {data: 'percstu', name: 'percstu'},
            {data: 'tottea', name: 'tottea'},
            {data: 'prtea', name: 'prtea'},
            {data: 'perctea', name: 'perctea'},
            {data: 'tottrainee', name: 'tottrainee'},
            {data: 'prtrainee', name: 'prtrainee'},
            {data: 'perctrainee', name: 'perctrainee'},
            {data: 'totnonacademic', name: 'totnonacademic'},
            {data: 'prnonacademic', name: 'prnonacademic'},
            {data: 'percnonacademic', name: 'percnonacademic'},
        ]
    });
    new $.fn.dataTable.FixedHeader( table );
    
    $('#filter').click(function(){
        var from_date = $('#from_date').val();
        // var to_date = $('#to_date').val();
        if(from_date != ''){
            table.draw();
        }
        else{
            alert('Please select a date');
        }
    });

    $('#refresh').click(function(){ 
        $('#from_date').val('');
        // $('#to_date').val('');
        table.draw();
    });     
});

</script>
@endpush
