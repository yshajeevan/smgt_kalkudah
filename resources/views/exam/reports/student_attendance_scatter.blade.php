@extends('layouts.master')

@section('main-content')
<div class="p-4">
    <h2 class="mb-4" style="margin-bottom:30px; font-weight:bold; color:#000;">Student Attendance Scatter Plot</h2>

    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
        <select id="examSelect" class="form-select" style="width:260px;">
            <option value="">-- Select Exam --</option>
            @foreach($exams as $index => $exam)
                <option value="{{ $exam->id }}" {{ $index === 0 ? 'selected' : '' }}>
                    {{ $exam->name }} - {{ $exam->year }}
                </option>
            @endforeach
        </select>

        <div id="loadingSpinner" style="display:none; margin-left:10px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow p-4 mb-3">
                <div style="height:600px;">
                    <canvas id="attendanceScatterChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow p-4 mb-3">
                <h5 style="font-weight:bold; color:#000;">Attendance Summary</h5>
                <ul id="attendanceSummary" style="list-style:none; padding-left:0; font-size:16px; color:#000;">
                    <li>Above 75%: <span id="above75">0</span></li>
                    <li>50% - 75%: <span id="between50_75">0</span></li>
                    <li>25% - 49%: <span id="between25_49">0</span></li>
                    <li>Below 25%: <span id="below25">0</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let scatterChart;

function loadAttendanceScatter(examId) {
    if (!examId) return;

    document.getElementById('loadingSpinner').style.display = 'inline-block';

    fetch(`{{ route('reports.student.attendance.data') }}?exam_id=${examId}`)
        .then(res => res.json())
        .then(res => {
            document.getElementById('loadingSpinner').style.display = 'none';

            if (!res.data) {
                console.error("Backend response missing `data`:", res);
                return;
            }

            if (scatterChart) scatterChart.destroy();

            const dataPoints = res.data.map(s => ({
                x: s.student_name,
                y: s.percentage
            }));

            const summary = res.summary ?? { above_75:0, between_50_75:0, between_25_49:0, below_25:0 };
            document.getElementById('above75').innerText = summary.above_75;
            document.getElementById('between50_75').innerText = summary.between_50_75;
            document.getElementById('between25_49').innerText = summary.between_25_49;
            document.getElementById('below25').innerText = summary.below_25;

            const ctx = document.getElementById('attendanceScatterChart').getContext("2d");

            scatterChart = new Chart(ctx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Student Attendance %',
                        data: dataPoints,
                        backgroundColor: 'red',
                        borderColor: 'black',
                        borderWidth: 1.5,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'category',
                            title: { display: true, text: 'Students', color: '#000', font: { size: 16, weight: 'bold' } },
                            ticks: { maxRotation: 90, minRotation: 45, autoSkip: false, color:'#000', font:{size:12} }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: { display: true, text: 'Attendance %', color:'#000', font:{ size:16, weight:'bold'} },
                            ticks: { color:'#000', font:{ size:14, weight:'bold'}, callback: v => v + '%' }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => `${ctx.raw.x}: ${ctx.raw.y.toFixed(2)}%` } }
                    }
                }
            });
        })
        .catch(err => {
            document.getElementById('loadingSpinner').style.display = 'none';
            console.error(err);
            alert("Error loading attendance scatter data");
        });
}


// Auto-load most recent exam
document.addEventListener("DOMContentLoaded", function () {
    const examSelect = document.getElementById('examSelect');
    if (examSelect.value) {
        loadAttendanceScatter(examSelect.value);
    }
});

// On dropdown change
document.getElementById('examSelect').addEventListener('change', function() {
    loadAttendanceScatter(this.value);
});
</script>
@endpush
