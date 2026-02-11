@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4" style="margin-bottom:30px; font-weight:bold; color:#000;">Exam Attendance Distribution</h2>

    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
        <select id="examSelect" class="form-select" style="width:260px;">
            <option value="">-- Select Exam --</option>
            @foreach($exams as $index => $exam)
                <option value="{{ $exam->id }}" {{ $index === 0 ? 'selected' : '' }}>
                    {{ $exam->name }} - {{ $exam->year }}
                </option>
            @endforeach
        </select>

        <!-- Loading spinner -->
        <div id="loadingSpinner" style="display:none; margin-left:10px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
        </div>
    </div>

    <div class="card shadow p-4">
        <div style="height:600px;">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
let attendanceChart;

// Load data from backend
function loadAttendanceData(examId) {
    if (!examId) return;

    document.getElementById('loadingSpinner').style.display = 'inline-block';

    fetch(`{{ route('reports.exam.attendance.data') }}?exam_id=${examId}`)
    .then(res => res.json())
    .then(res => {
        document.getElementById('loadingSpinner').style.display = 'none';

        if (attendanceChart) attendanceChart.destroy();

        const ctx = document.getElementById('attendanceChart').getContext("2d");

        const labels = Object.keys(res);
        const dataObjects = Object.values(res); // [{percent,count}, ...]
        const chartValues = dataObjects.map(v => v.percent); // numeric values for chart

        const colorPalette = [
            '#ff6384','#36a2eb','#ffcd56','#4bc0c0',
            '#9966ff','#ff9f40','#66bb6a','#d32f2f',
            '#0288d1','#7b1fa2'
        ];

        attendanceChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: labels,
        datasets: [{
            data: chartValues,
            backgroundColor: labels.map((_, i) => colorPalette[i % colorPalette.length]),
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    color: '#000',
                    font: { size: 14, weight: 'bold' },
                    generateLabels: (chart) => {
                        // Show both percent and count in legend
                        return chart.data.labels.map((label, i) => {
                            const obj = dataObjects[i];
                            return {
                                text: `${label}: ${obj.percent}% (${obj.count})`,
                                fillStyle: chart.data.datasets[0].backgroundColor[i],
                                strokeStyle: '#fff',
                                lineWidth: 2,
                                hidden: false,
                                index: i
                            };
                        });
                    }
                }
            },
            title: {
                display: true,
                text: 'Student Attendance by Number of Subjects',
                color: '#000',
                font: { size: 18, weight: 'bold' },
                padding: { top: 20, bottom: 20 }
            },
            datalabels: { display: false } // hide labels on chart
        }
    },
    plugins: [ChartDataLabels]
});

    })
    .catch(err => {
        document.getElementById('loadingSpinner').style.display = 'none';
        console.error(err);
        alert("Error loading attendance data");
    });
}


// Auto-load most recent exam
document.addEventListener("DOMContentLoaded", function () {
    const examSelect = document.getElementById('examSelect');
    if (examSelect.value) {
        loadAttendanceData(examSelect.value);
    }
});

// On dropdown change
document.getElementById('examSelect').addEventListener('change', function() {
    loadAttendanceData(this.value);
});
</script>
@endpush
