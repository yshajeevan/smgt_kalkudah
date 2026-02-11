@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4 fw-bold">Student Unit-wise Performance</h2>

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-4">
            <select id="examSelect" class="form-select">
                <option value="">-- Select Exam --</option>
                @foreach($exams as $e)
                    <option value="{{ $e->id }}">{{ $e->name }} ({{ $e->year }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select id="subjectSelect" class="form-select">
                <option value="">-- Select Subject --</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}">{{ $s->cadre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <button id="loadStudentBtn" class="btn btn-success">Load Unit Analysis</button>
        </div>
    </div>

    <!-- Graphs -->
    <div id="chartsContainer"></div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$('#loadStudentBtn').click(function(){
    let exam = $('#examSelect').val();
    let subject = $('#subjectSelect').val();

    if(!exam || !subject){ alert("Select exam & subject!"); return; }

    $.post("{{ route('reports.student.unit.data') }}", {
        _token: "{{ csrf_token() }}",
        exam_id: exam,
        subject_id: subject
    }, function(data){
        let container = $("#chartsContainer");
        container.empty(); // clear old charts

        // loop through each unit
        data.units.forEach((u, idx) => {
            // inside your loop
            let canvasId = "chart_unit_" + idx;

            // calculate height dynamically (e.g., 30px per student)
            let chartHeight = u.student_percents.length * 7; 

            container.append(`
                <div class="card mb-4">
                    <div class="card-header fw-bold">Unit: ${u.syllabus} (${u.competency})</div>
                    <div class="card-body">
                        <canvas id="${canvasId}" height="${chartHeight}"></canvas>
                    </div>
                </div>
            `);

            new Chart(document.getElementById(canvasId), {
                type: 'bar',
                data: {
                    labels: data.students, // Y-axis = students
                    datasets: [{
                        label: "% Performance",
                        data: u.student_percents,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)'
                    }]
                },
                options: {
                    indexAxis: 'y', // horizontal bar (Y=students)
                    responsive: true,
                    plugins: {
                        title: { display: true, text: `Performance in ${u.syllabus}` },
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: (val)=> val+"%",
                                font: { size: 13, weight:'bold' }
                            }
                        },
                        y: {
                            ticks: { font: { size: 12, weight:'bold' } }
                        }
                    }
                }
            });
        });
    });
});
</script>
@endpush
