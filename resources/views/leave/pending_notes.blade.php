@extends('layouts.master')

@section('main-content')

<div class="card shadow">

    <div class="card-header py-3 d-flex justify-content-between">
        <h5 class="m-0 font-weight-bold text-primary">Employees Leave Notes Not Submitted</h5>

        <a href="{{ route('leave.index') }}" class="btn btn-info btn-sm">
            Dashboard
        </a>
    </div>


    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="pendingTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Pending Notes</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($data as $row)
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->name_with_initial_e }}</td>
                        <td>
                            <span class="badge badge-danger p-2 showDates"
                                  style="cursor:pointer;"
                                  data-id="{{ $row->id }}"
                                  data-name="{{ $row->name_with_initial_e }}">
                                {{ $row->pending }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="datesModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalTitle">
                    Pending Leave Dates
                </h5>

                <button class="close text-white" data-dismiss="modal">
                    ×
                </button>
            </div>

            <div class="modal-body">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Days</th>
                        </tr>
                    </thead>

                    <tbody id="datesBody"></tbody>
                </table>

            </div>

        </div>
    </div>
</div>

@endsection



@push('scripts')

<script>
$(document).ready(function(){

    $('#pendingTable').DataTable();



    $(document).on('click', '.showDates', function(){

        let id   = $(this).data('id');
        let name = $(this).data('name');

        $('#modalTitle').text(name + ' - Pending Leave Notes');

        $.get('/leave-pending-dates/' + id, function(res){

            let html = '';

            $.each(res, function(k,row){

                html += `
                    <tr>
                        <td>${row.leave_type}</td>
                        <td>${row.from_date}</td>
                        <td>${row.to_date}</td>
                        <td>${row.days}</td>
                    </tr>
                `;
            });

            $('#datesBody').html(html);

            $('#datesModal').modal('show');

        });

    });

});
</script>

@endpush