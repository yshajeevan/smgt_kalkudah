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
             @php
                $currentPath = request()->path();
            @endphp

            <div class="col-md-3">
                <label>Date</label>
                <input type="text" id="from_date" 
                    class="form-control input-daterange1"
                    value="{{ $currentPath == 'attendance-schools' ? \Carbon\Carbon::today()->format('Y-m-d') : '' }}">
            </div>
            
            <!-- School -->
            @if($currentPath == 'attendance-schools')
            <div class="col-md-3">
                <label>School</label>
                <select id="school_filter" class="form-control">
                    <option value="">All</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->institute }}</option>
                    @endforeach
                </select>
            </div>
            @endif

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
        @if($currentPath == 'attendance-schools')
        <div class="row mb-2">
            <div class="col-md-12 text-right">
                <span class="badge badge-success p-2">
                    Total Entries: <span id="countBox">{{$countatten}}</span>
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

                        <th>Total Students</th>
                        <th>Presented Students</th>
                        <th>%</th>

                        @if($currentPath != 'zonalattendance')
                        <th>Principal</th>
                        @endif

                        @if($currentPath == 'zonalattendance')
                            <th>Total Schools</th>
                            <th>Principal Present</th>
                            <th>%</th>
                        @endif

                        @if($currentPath == 'attendance-schools')
                            <th>Rank</th>
                        @endif

                        <th>Total Teachers</th>
                        <th>Presented Teachers</th>
                        <th>%</th>
                    </tr>
                </thead>
            </table>
            <div class="mt-4">
                <canvas id="attendanceChart" height="100"></canvas>
            </div>
        </div>

    </div>
</div>
@endsection


@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />

<style>

.form-control,
.select2-container .select2-selection--single {
    height: 38px !important;
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 4px;
}

/* Fix select2 alignment */
.select2-container--default .select2-selection--single {
    display: flex;
    align-items: center;
}

/* Fix select2 arrow height */
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

/* Button same height */
#refresh {
    height: 38px;
}

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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function () {

    let currentPath = "{{ request()->path() }}";

    $('.input-daterange1').datepicker({
        todayBtn:'linked',
        format:'yyyy-mm-dd',
        autoclose:true,
        todayHighlight: true
    });

    if(currentPath === 'attendance-schools'){
        $('.input-daterange1').datepicker('setDate', new Date());
    }

    // ✅ Select2
    $('#school_filter, #class_filter').select2({
        width: '100%',
        placeholder: "All",
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

            {data: 'totstu'},
            {data: 'prstu'},
            {data: 'percstu'},

            @if($currentPath != 'zonalattendance')
                {data: 'principal'},
            @endif

            @if($currentPath == 'attendance-schools')
                {data: 'rank'},
            @endif

            @if($currentPath == 'zonalattendance')
                {data: 'total_schools'},
                {data: 'principal_present'},
                {data: 'principal_perc'},
            @endif

            {data: 'tottea'},
            {data: 'prtea'},
            {data: 'perctea'},
        ]
    });

    new $.fn.dataTable.FixedHeader(table);

    $('#from_date').change(function () {
        let selectedDate = $(this).val();

        // ✅ Reload DataTable
        table.draw();

        // ✅ AJAX call
        $.ajax({
            url: "{{ route('schools.by.date') }}",
            type: "GET",
            data: { date: selectedDate },

            success: function (response) {

                // 🔹 Update schools dropdown
                let schoolSelect = $('#school_filter');

                schoolSelect.empty();
                schoolSelect.append('<option value="">All Schools</option>');

                response.schools.forEach(function (school) {
                    schoolSelect.append(
                        `<option value="${school.id}">${school.institute}</option>`
                    );
                });

                // 🔹 Refresh Select2
                schoolSelect.trigger('change.select2');

                // 🔹 Update count
                $('#countBox').text(response.count);
            }
        });

    });

    // 🔄 Events
    $('#refresh').click(function () {
        $('#from_date').val('');
        $('#school_filter').val(null).trigger('change');
        $('#class_filter').val(null).trigger('change');
        table.draw();
        loadChart();
    });

    $('#school_filter, #class_filter').change(function () {
        table.draw();
        loadChart();
    });

    loadChart();
});


let chart;

function loadChart() {

    $.ajax({
        url: "{{ route('attendance.graph') }}",
        data: {
            from_date: $('#from_date').val(),
            insid: $('#school_filter').val(),
            currentPath: "{{ $currentPath }}",
            class_filter: $('#class_filter').val(),
        },
        success: function (res) {

            if (chart) chart.destroy();

            let ctx = document.getElementById('attendanceChart').getContext('2d');

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: res.labels,
                    datasets: [{
                        label: 'Student Attendance %',
                        data: res.student,
                        borderWidth: 2,
                        tension: 0.3,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
    });
}
</script>
@endpush