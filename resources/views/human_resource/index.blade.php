<!-- @php
    $template = Auth::user()->hasAnyRole(['Sch_Admin']) ? 'layouts.school.master' : 'layouts.master';
@endphp -->

@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Employes's List</h6>
    </div>
    <div class="card-body">
    
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        <label><strong>Permanant Station(as per salary) :</strong></label>
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
                <div class="col-lg-3">
                    <div class="form-group">
                        <label><strong>Filter by Status :</strong></label>
                        <select name="status" id="status" class="form-control form-control-sm">
                            <option value="">All</option> <!-- Show all statuses -->
                            <option value="Active">Active</option>
                            <option value="TrOut">TrOut</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Pension">Pension</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label><strong>Attachment :</strong></label><br>
                        <input type="checkbox" id="attachmentFilter"> Show Only Attachments
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <button id="clearFilters" class="btn btn-secondary btn-sm mt-4">Clear Filters</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @can('employee-create')
    <a href="{{route('employee.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Employee</a>
    <a href="{{route('employee.export')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Export"><i class="fa fa-file-excel-o"></i> Bulk Export</a>
    @endcan
        <div class="table-responsive">
            <table class="table table-bordered" id="employees">
                <thead>
                    <th>Id</th>
                    <th>Name</th>
                    <th style="width:300px;">Permanent Station (as per salary)</th>
                    <th style="width:300px;">Current Working Station</th>
                    <th>Designation</th>
                    <th>Cadre</th>
                    <th>Status</th>
                    <th>Updated at</th>
                    <th>Options</th>
                </thead>				
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var defaultStatus = localStorage.getItem('status') || ''; // Default to "All"

// Set default value in dropdown
$('#status').val(defaultStatus);

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
                d.cadre = localStorage.getItem('cadre') || $('#cadre').val();
                d.institute = localStorage.getItem('institute') || $('#institute').val();
                d.designation = localStorage.getItem('designation') || $('#designation').val();
                d.transferValidate = localStorage.getItem('transferValidate') || $('#transferValidate').val();
                d.status = localStorage.getItem('status') || $('#status').val(); // Send status filter
                d.search = localStorage.getItem('search') || $('input[type="search"]').val();
                d.attachment = $('#attachmentFilter').is(':checked') ? 1 : 0;
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'surname', name: 'surname'},
            {data: 'institute', name: 'institute.institute'},
            {data: 'working_station', name: 'working_station'},
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
            { data: 'updated_at', name: 'updated_at' },
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],

        // ✅ Add this block
        rowCallback: function(row, data, index) {
            // Compare rendered column text (institute vs working_station)
            if (data.institute !== data.working_station) {
                $(row).addClass('table-warning');  // Yellow highlight
            }
        }
        
    });
    // Restore filters from local storage
    $('#cadre').val(localStorage.getItem('cadre') || '');
    $('#designation').val(localStorage.getItem('designation') || '');
    $('#institute').val(localStorage.getItem('institute') || '');
    $('#transferValidate').val(localStorage.getItem('transferValidate') || '');
    $('#status').val(localStorage.getItem('status') || 'Active'); // Default to Active

    // Apply filter change
    function updateTable() {
        localStorage.setItem('cadre', $('#cadre').val());
        localStorage.setItem('designation', $('#designation').val());
        localStorage.setItem('institute', $('#institute').val());
        localStorage.setItem('transferValidate', $('#transferValidate').val());
        localStorage.setItem('status', $('#status').val());
        table.draw();
    }

    $('#cadre, #designation, #institute, #transferValidate, #status').change(updateTable);

    // Clear filters when needed
    $('#clearFilters').click(function () {
        localStorage.removeItem('cadre');
        localStorage.removeItem('designation');
        localStorage.removeItem('institute');
        localStorage.removeItem('transferValidate');
        localStorage.removeItem('status');
        $('#cadre, #designation, #institute, #transferValidate, #status').val('');
        table.draw();
    });

    // let column = table.column(2);
    // let user = @json(Auth::check() ? Auth::user()->roles->pluck('name')->implode(', ') : '');

    // if(user === 'Sch_Admin'){
    //     column.visible(false);
    // } else{
    //     column.visible(true);
    // }
    
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
    $('#attachmentFilter').change(function () {
        table.draw();
    });
});
</script>
@endpush
