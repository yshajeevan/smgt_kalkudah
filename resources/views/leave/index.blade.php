@extends('layouts.master')

@section('main-content')

<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            <div id="ajaxMessage"></div>
        </div>
    </div>
    <div class="card-header py-3 d-flex justify-content-between">
        <h5 class="m-0 font-weight-bold text-primary">Leave Dashboard</h5>

        <a href="{{ route('leave.pending.notes') }}" class="btn btn-danger btn-sm">
            Leave Notes Pending
        </a>
    </div>

    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="leaveTable" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Casual Taken</th>
                        <th>Casual Balance</th>
                        <th>Medical Taken</th>
                        <th>Medical Balance</th>
                        <th>No Note</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>
</div>


<!-- Add Leave Modal -->
<div class="modal fade" id="leaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Leave</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="employee_id">
                <input type="hidden" id="employee_name">

                <div class="form-group">
                    <label>Leave Type</label>
                    <select class="form-control" id="leave_type">
                        <option value="Casual">Casual</option>
                        <option value="Medical">Medical</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" id="from_date">
                </div>

                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" id="to_date">
                </div>

                <div class="form-group">
                    <label>No of Days</label>
                    <input type="number" class="form-control" id="days">
                </div>

                <div class="form-group">
                    <label>Leave Note Submitted?</label>
                    <select class="form-control" name="leave_note" id="leave_note">
                        <option value="">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="saveLeave">
                    Save Leave
                </button>

                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>



<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Leave History</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Days</th>
                                <th>Leave Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="historyBody"></tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- Edit Model -->
 <div class="modal fade" id="editLeaveModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Leave</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="edit_id">

                <div class="form-group">
                    <label>Leave Type</label>
                    <select class="form-control" id="edit_leave_type">
                        <option value="Casual">Casual</option>
                        <option value="Medical">Medical</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" class="form-control" id="edit_from_date">
                </div>

                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" class="form-control" id="edit_to_date">
                </div>

                <div class="form-group">
                    <label>Days</label>
                    <input type="number" step="0.5" class="form-control" id="edit_days">
                </div>

                <div class="form-group">
                    <label>Leave Note</label>
                    <select class="form-control" id="edit_leave_note">
                        <option value="">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" id="updateLeave">
                    Update
                </button>

                <button class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

@endsection



@push('scripts')

<script>
$(document).ready(function () {

    var table = $('#leaveTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('leave.data') }}",

        columns: [
            { data: 'id' },
            { data: 'name_with_initial_e' },
            { data: 'designation' },
            { data: 'casual_taken' },
            { data: 'casual_balance' },
            { data: 'medical_taken' },
            { data: 'medical_balance' },
            { data: 'note_pending' },
            { data: 'action', orderable: false, searchable: false }
        ],

        rowCallback: function (row, data) {

            if (parseInt(data.casual_balance) <= 2) {
                $('td:eq(4)', row).addClass('bg-danger text-white');
            } else if (parseInt(data.casual_balance) <= 5) {
                $('td:eq(4)', row).addClass('bg-warning');
            }

            if (parseInt(data.medical_balance) <= 2) {
                $('td:eq(6)', row).addClass('bg-danger text-white');
            } else if (parseInt(data.medical_balance) <= 5) {
                $('td:eq(6)', row).addClass('bg-warning');
            }

            if (parseInt(data.note_pending) > 0) {
                $('td:eq(7)', row).addClass('bg-dark text-white');
            }
        }
    });



    /* Add Leave Button */
    $(document).on('click', '.addLeave', function (e) {

        e.stopPropagation();

        let id   = $(this).data('id');
        let name = $(this).data('name');

        $('#employee_id').val(id);
        $('#employee_name').val(name);

        $('#leaveModal').modal('show');

    });



    /* Save Leave */
    $('#saveLeave').click(function () {

        $.ajax({
            url: "{{ route('leave.store') }}",
            type: "POST",

            data: {
                _token: "{{ csrf_token() }}",
                employee_id: $('#employee_id').val(),
                leave_type: $('#leave_type').val(),
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                days: $('#days').val(),
                leave_note: $('#leave_note').val()
            },

            success: function (res) {

                let empName = $('#employee_name').val();

                $('#leaveModal').modal('hide');

                table.ajax.reload(null, false);

                $('#ajaxMessage').html(`
                    <div class="alert alert-success alert-dismissable fade show">
                        <button class="close" data-dismiss="alert">×</button>
                        Leave saved successfully for <strong>${empName}</strong>.
                    </div>
                `);

                setTimeout(function () {
                    $('.alert').fadeOut();
                }, 4000);
            }
        });

    });



    /* History Button */
    $(document).on('click', '.historyBtn', function (e) {

        e.stopPropagation();

        let id = $(this).data('id');

        loadHistory(id);

    });



    function loadHistory(id) {

        $.get('/leave-history/' + id, function (res) {

            let html = '';

            $.each(res, function (key, row) {

                html += `
                    <tr>
                        <td>${row.leave_type}</td>
                        <td>${row.from_date}</td>
                        <td>${row.to_date}</td>
                        <td>${row.days}</td>
                        <td>${row.leave_note ? row.leave_note : 'No'}</td>
                        <td>
                            <button class="btn btn-sm btn-primary editLeave"
                                data-id="${row.id}">
                                Edit
                            </button>

                            <button class="btn btn-sm btn-danger deleteLeave"
                                data-id="${row.id}">
                                Delete
                            </button>
                        </td>
                    </tr>
                `;
            });

            $('#historyBody').html(html);

            $('#historyModal').modal('show');
        });
    }

    function calculateLeaveDays() {

    let from = $('#from_date').val();
    let to   = $('#to_date').val();

    if (from !== '' && to !== '') {

        let fromDate = new Date(from);
        let toDate   = new Date(to);

        let diffTime = toDate.getTime() - fromDate.getTime();
        let diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

        let totalDays = 1;

        if (diffDays === 0) {
            totalDays = 0.5;
        }
        else if (diffDays === 1) {
            totalDays = 1;
        }
        else {
            totalDays = diffDays;
        }

        $('#days').val(totalDays);
    }
}


/* From Date Change */
$('#from_date').change(function () {

    let selected = new Date($(this).val());

    selected.setDate(selected.getDate() + 1);

    let yyyy = selected.getFullYear();
    let mm   = String(selected.getMonth() + 1).padStart(2, '0');
    let dd   = String(selected.getDate()).padStart(2, '0');

    $('#to_date').val(yyyy + '-' + mm + '-' + dd);

    calculateLeaveDays();
});


/* To Date Change */
$('#to_date').change(function () {
    calculateLeaveDays();
});

$(document).on('click', '.editLeave', function(){

    let id = $(this).data('id');

    $.get('/leave-edit/' + id, function(res){

        $('#edit_id').val(res.id);
        $('#edit_leave_type').val(res.leave_type);
        $('#edit_from_date').val(res.from_date);
        $('#edit_to_date').val(res.to_date);
        $('#edit_days').val(res.days);
        $('#edit_leave_note').val(res.leave_note);

        $('#editLeaveModal').modal('show');

    });

});

$('#updateLeave').click(function(){

    let id = $('#edit_id').val();

    $.ajax({
        url:'/leave-update/' + id,
        type:'POST',

        data:{
            _token:"{{ csrf_token() }}",
            leave_type: $('#edit_leave_type').val(),
            from_date: $('#edit_from_date').val(),
            to_date: $('#edit_to_date').val(),
            days: $('#edit_days').val(),
            leave_note: $('#edit_leave_note').val()
        },

        success:function(res){

            $('#editLeaveModal').modal('hide');
            $('#historyModal').modal('hide');

            $('#ajaxMessage').html(`
                <div class="alert alert-success alert-dismissable fade show">
                    <button class="close" data-dismiss="alert">×</button>
                    ${res.message}
                </div>
            `);

            $('#leaveTable').DataTable().ajax.reload(null,false);

        }
    });

});

$(document).on('click', '.deleteLeave', function(){

    let id = $(this).data('id');

    if(confirm('Are you sure to delete this leave record?')){

        $.ajax({
            url: '/leave-delete/' + id,
            type: 'DELETE',

            data: {
                _token: "{{ csrf_token() }}"
            },

            success: function(res){

                if(res.status){

                    $('#ajaxMessage').html(`
                        <div class="alert alert-success alert-dismissable fade show">
                            <button class="close" data-dismiss="alert">×</button>
                            ${res.message}
                        </div>
                    `);

                    $('#historyModal').modal('hide');

                    $('#leaveTable').DataTable().ajax.reload(null,false);

                } else {

                    alert(res.message);
                }
            }
        });

    }

});

});
</script>

@endpush