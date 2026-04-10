@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">

    <!-- 🔹 HEADER -->
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            @if($currentPath == 'zonalattendance')
                Zonal Student Attendance
            @elseif($currentPath == 'attendance-schools')
                School wise Student Attendance
            @else
                {{ $institute }}
            @endif
        </h6>

        @can('attendance-create')
            @if($uname != 'Sch_Admin')
                <a href="{{route('submitform.index',4)}}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Attendance
                </a>
            @else
                <a href="{{route('attendance.create')}}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Attendance
                </a>
            @endif
        @endcan
    </div>

    <!-- 🔹 BODY -->
    <div class="card-body">

        @include('layouts.notification')

        <!-- 🔹 FILTER BAR -->
        <div class="row mb-3 align-items-end">

            <!-- Date -->
            <div class="col-md-3">
                <label>Date</label>
                <input type="text" id="from_date" class="form-control input-daterange1" placeholder="Select Date">
            </div>

            <!-- School -->
            <div class="col-md-3">
                <label>School</label>
                <select id="school_filter" class="form-control">
                    <option value="">All Schools</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->institute }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Class -->
            <div class="col-md-4">
                <label>Class / Stream</label>
                <select id="class_filter" class="form-control">
                    <option value="">All</option>

                    <option disabled>──────── Grades ────────</option>
                    <option value="1_5">Grade 1-5</option>
                    <option value="6_9">Grade 6-9</option>
                    <option value="10_11">Grade 10-11</option>

                    <option disabled>──────── Combined ────────</option>
                    <option value="secondary">Secondary (6-11)</option>

                    <option disabled>──────── A/L 1st Year ────────</option>
                    <option value="arts_1st">Arts</option>
                    <option value="com_1st">Commerce</option>
                    <option value="physc_1st">Physical</option>
                    <option value="biosc_1st">Bio</option>
                    <option value="etech_1st">ETech</option>
                    <option value="btech_1st">BTech</option>

                    <option disabled>──────── A/L 2nd Year ────────</option>
                    <option value="arts_2nd">Arts</option>
                    <option value="com_2nd">Commerce</option>
                    <option value="biosc_2nd">Bio</option>
                    <option value="physc_2nd">Physical</option>
                    <option value="etech_2nd">ETech</option>
                    <option value="btech_2nd">BTech</option>

                    <option disabled>──────── Summary ────────</option>
                    <option value="al_1">A/L 1st Year (All)</option>
                    <option value="al_2">A/L 2nd Year (All)</option>
                </select>
            </div>

            <!-- Clear Button -->
            <div class="col-md-2">
                <button id="refresh" class="btn btn-outline-secondary w-100">
                    Clear
                </button>
            </div>
        </div>

        <!-- 🔹 TOP INFO -->
        @if($uname != 'Sch_Admin' && $currentPath != 'schoolatten')
        <div class="row mb-2">
            <div class="col-md-12 text-right">
                <span class="badge badge-success p-2">
                    Total Entries Today: {{$countatten}}
                </span>
            </div>
        </div>
        @endif

        <!-- 🔹 TABLE -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="attendance">
                <thead>
                    <tr>
                        @if($currentPath == 'attendance-schools')
                            <th>S/N</th>
                            <th>Institute</th>
                        @endif

                        @if($currentPath != 'attendance-schools')
                            <th>Date</th>
                        @endif

                        <th>Principal</th>
                        <th>Total Students</th>
                        <th>Presented Students</th>
                        <th>%</th>

                        @if($currentPath == 'attendance-schools')
                            <th>Rank</th>
                        @endif

                        <th>Total Teachers</th>
                        <th>Presented Teachers</th>
                        <th>%</th>
                    </tr>
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
    text-align: center;
    vertical-align: middle;
}

.table th {
    text-align: center;
}

label {
    font-weight: 600;
    font-size: 13px;
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

    // ✅ Select2
    $('#school_filter, #class_filter').select2({
        width: '100%',
        allowClear: true
    });

    var table = $('#attendance').DataTable({
        processing: true,
        serverSide: true,
        stateSave:true,
        paging: false,
        ordering: false,

        ajax: {
            url: "{{ route('attendance.index') }}",
            data: function (d) {
                d.from_date = $('#from_date').val();
                d.insid = {!! json_encode($insid) !!};
                d.currentPath = {!! json_encode($currentPath) !!};
                d.school_id = $('#school_filter').val();
                d.class_filter = $('#class_filter').val();
            }
        },

        columns: [
            @if($currentPath == 'attendance-schools')
                {data: 'DT_RowIndex', orderable:false, searchable:false},
                {data: 'institute_name'},
            @endif

            @if($currentPath != 'attendance-schools')
                {data: 'created_at'},
            @endif

            {data: 'principal'},
            {data: 'totstu'},
            {data: 'prstu'},
            {data: 'percstu'},

            @if($currentPath == 'attendance-schools')
                {data: 'rank'},
            @endif

            {data: 'tottea'},
            {data: 'prtea'},
            {data: 'perctea'},
        ]
    });

    new $.fn.dataTable.FixedHeader(table);

    // 🔄 Events
    $('#refresh').click(function () {
        $('#from_date').val('');
        $('#school_filter').val(null).trigger('change');
        $('#class_filter').val(null).trigger('change');
        table.draw();
    });

    $('#from_date, #school_filter, #class_filter').change(function () {
        table.draw();
    });

});
</script>
@endpush