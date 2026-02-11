@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4" style="margin-bottom:30px; font-weight:bold; color:#000;">Student Marks - {{ $instituteName }}</h2>

    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
        <select id="examSelect" class="form-select" style="width:260px;">
            @foreach($exams as $exam)
                <option value="{{ $exam->id }}" {{ $exam->id == $recentExamId ? 'selected' : '' }}>
                    {{ $exam->name }} - {{ $exam->year }}
                </option>
            @endforeach
        </select>

        <select id="subjectSelect" class="form-select" style="width:260px;">
            <option value="">-- Select Subject --</option>
            @foreach($subjects as $subId => $subName)
                <option value="{{ $subId }}">{{ $subName }}</option>
            @endforeach
        </select>

        <button id="toggleNamesBtn" class="btn btn-outline-secondary">Toggle Student Names</button>

        <button id="printBtn" class="btn btn-outline-primary">Print</button>

        <div id="loadingSpinner" style="display:none; margin-left:10px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
        </div>
    </div>

    <div class="row">
        <div style="overflow-y:auto;">
            <canvas id="studentChart"></canvas>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3" style="height:600px;">
                <h5 class="mb-3" style="font-weight:bold;">Summary</h5>
                <ul class="list-group list-group-flush" id="summaryList">
                    <li class="list-group-item">Below 35 (W): <span id="countW">0</span></li>
                    <li class="list-group-item">35-49 (S): <span id="countS">0</span></li>
                    <li class="list-group-item">50-64 (C): <span id="countC">0</span></li>
                    <li class="list-group-item">65-74 (B): <span id="countB">0</span></li>
                    <li class="list-group-item">Above 74 (A): <span id="countA">0</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    @page {
        size: A4 portrait;
        margin: 15mm;
    }
    /* Hide controls when printing */
    .no-print, #toggleNamesBtn, #examSelect, #subjectSelect, #printBtn, #loadingSpinner {
        display: none !important;
    }
    /* Chart + Summary responsive for print */
    .row {
        display: block !important;
    }
    .col-md-8, .col-md-4 {
        width: 100% !important;
        max-width: 100% !important;
        display: block !important;
    }
    canvas {
        page-break-inside: avoid !important;
    }
    .card {
        page-break-inside: avoid !important;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
let studentChart;
let namesVisible = false; // initially blurred

function renderStudentChart(data, title){
    const studentCount = Object.keys(data).length;
    const canvas = document.getElementById('studentChart');

    // Dynamic height: 40px per student
    canvas.height = studentCount * 40;

    // Dynamic width: 30px per student, min 800px
    canvas.width = Math.max(800, studentCount * 30);

    // ...rest of your chart rendering code
    let sortedEntries = Object.entries(data).sort((a,b)=> b[1] - a[1]);
    const students = sortedEntries.map(e => e[0]);
    const marks = sortedEntries.map(e => e[1]);
    const colors = students.map((_, i) => `hsl(${i*30 % 360}, 70%, 50%)`);

    // summary counts
    let countW=0, countS=0, countC=0, countB=0, countA=0;
    marks.forEach(mark=>{
        if(mark<35) countW++;
        else if(mark<=49) countS++;
        else if(mark<=64) countC++;
        else if(mark<=74) countB++;
        else countA++;
    });
    document.getElementById('countW').textContent = countW;
    document.getElementById('countS').textContent = countS;
    document.getElementById('countC').textContent = countC;
    document.getElementById('countB').textContent = countB;
    document.getElementById('countA').textContent = countA;

    if(studentChart) studentChart.destroy();

    studentChart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: students,
            datasets: [{
                label: 'Marks',
                data: marks,
                backgroundColor: colors
            }]
        },
        options: {
            responsive: false, // important to respect dynamic width/height
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display:false },
                title: {
                    display:true,
                    text: title,
                    font:{ size:18, weight:'bold', color:'#000' },
                    padding:{ top:30, bottom:30 }
                },
                datalabels: {
                    anchor:'end',
                    align:'end',
                    color:'#000',
                    font:{ weight:'bold', size:12 },
                    formatter: function(value, context) {
                        const rank = context.dataIndex + 1;
                        return value + ' (' + rank + ')';
                    }
                }
            },
            scales: {
                x:{ beginAtZero:true, max:100, ticks:{ font:{ size:14, weight:'bold', color:'#000' } } },
                y:{ ticks:{ font:{ size:14, weight:'bold', color:'#000' },
                    callback: function(value){
                        const label = this.getLabelForValue(value) || '';
                        return namesVisible ? label : label.replace(/\S/g,'•');
                    }
                } }
            }
        },
        plugins:[ChartDataLabels]
    });
}



function loadStudentMarks(defaultSubjectId=null){
    const examId = document.getElementById('examSelect').value;
    const subjectSelect = document.getElementById('subjectSelect');
    let subjectId = subjectSelect.value;

    if(!subjectId && defaultSubjectId) {
        subjectId = defaultSubjectId;
        subjectSelect.value = defaultSubjectId;
    }
    if(!examId || !subjectId) return;

    document.getElementById('loadingSpinner').style.display = 'inline-block';

    fetch(`{{ route('reports.students.subject.marks.data') }}?exam_id=${examId}&subject_id=${subjectId}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById('loadingSpinner').style.display='none';
        renderStudentChart(data, 'Student Marks - School 44');
    })
    .catch(err => {
        document.getElementById('loadingSpinner').style.display='none';
        console.error(err);
        alert('Error loading student marks');
    });
}

// Toggle button
document.getElementById('toggleNamesBtn').addEventListener('click', function(){
    namesVisible = !namesVisible;
    if(studentChart) studentChart.update();
});

// Event listeners
document.getElementById('examSelect').addEventListener('change', ()=> loadStudentMarks());
document.getElementById('subjectSelect').addEventListener('change', ()=> loadStudentMarks());

// Initial load: select first subject by default
window.addEventListener('load', ()=>{
    const firstSubject = document.getElementById('subjectSelect').options[1]?.value;
    if(firstSubject) loadStudentMarks(firstSubject);
});

// Print button
document.getElementById('printBtn').addEventListener('click', function(){
    window.print();
});
</script>
@endpush
