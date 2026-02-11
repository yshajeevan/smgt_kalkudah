@php
    $template = Auth::user()->hasAnyRole(['Sch_Admin']) ? 'layouts.school.master' : 'layouts.master';
@endphp

@extends($template)

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
    @if(Auth::user()->roles->pluck('name')->implode(', ') != 'Sch_Admin')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        <label><strong>Institute :</strong></label>
                        <select name="institute" id="institute" class="form-control form-control-sm">
                            <option value="">--Select Institute--</option>
                            @foreach ($institutes as $institute)
                            <option value="{{$institute->id}}" {{(isset($employee) && $employee->institute_id == $institute->id)  ? 'selected' : ''}}>{{$institute->institute}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label><strong>Filter by Designation :</strong></label>
                        <select name="designation" id="designation" class="form-control form-control-sm">
                            <option value="">--Select Designation--</option>
                            @foreach ($designations as $designation)
                            <option value="{{$designation->id}}" {{(isset($employee) && $employee->designation_id == $designation->id)  ? 'selected' : ''}}>{{$designation->designation}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label><strong>Filter by Cadre :</strong></label>
                        <select name="cadre" id="cadre" class="form-control form-control-sm">
                            <option value="">--Select Cadre Subject--</option>
                            @foreach ($cadresubs as $cadresub)
                            <option value="{{$cadresub->id}}" {{(isset($employee) && $employee->cadresubject_id == $cadresub->id)  ? 'selected' : ''}}>{{$cadresub->cadre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label><strong>Transfer Validation :</strong></label>
                        <select name="transferValidate" id="transferValidate" class="form-control form-control-sm">
                            <option value="">--Select Transfer Validation--</option>
                            <option value="transfered">Transfered</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @can('employee-create')
    <a href="{{route('employee.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Employee</a>
    <a href="{{route('employee.export')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Export"><i class="fa fa-file-excel-o"></i> Bulk Export</a>
    @endcan
        <div class="table-responsive">
            <table class="table table-bordered" id="employees">
                <thead>
                    <th>Id</th>
                    <th>Name</th>
                    <th style="width:300px;">Institute</th>
                    <th>Designation</th>
                    <th>Cadre</th>
                    <th>Status</th>
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
				'excelHtml5',
                'pdfHtml5',
				'print'
			    ],
        ajax: {
          url: "{{ route('employee.index') }}",
          data: function (d) {
                _token: "{{csrf_token()}}",
                d.cadre = $('#cadre').val(),
                d.institute = $('#institute').val(),
                d.designation = $('#designation').val(),
                d.transferValidate = $('#transferValidate').val(),
                d.search = $('input[type="search"]').val()
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'surname', name: 'surname'},
            {data: 'institute', name: 'institute.institute'},
            {data: 'designation', name: 'designation.designation'},
            {data: 'cadresubject', name: 'cadresubject.cadre'},
            {data: 'status', name: 'status',
                render: function (data, type, full, meta) {
                    if(data == 'Active'){
                        return "<p class='badge rounded-pill bg-success text-light'>" + data + "</p>";
                    } else {
                        return "<p class='badge rounded-pill bg-danger text-light'>" + data + "</p>";
                    }
                }
            },
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
        
        
    });
    let column = table.column(2);
    let user = '{{Auth::user()->roles->pluck('name')->implode(', ')}}';
    if(user === 'Sch_Admin'){
        column.visible(false);
    } else{
        column.visible(true);
    }
    
    $('#cadre').change(function(){
        table.draw(); 
    });
    $('#designation').change(function(){
        table.draw(); 
    });
    $('#institute').change(function(){
        table.draw(); 
    });
    $('#transferValidate').change(function(){
        table.draw(); 
    });
});
</script>
@endpush
