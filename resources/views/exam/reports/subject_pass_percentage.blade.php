{{-- resources/views/exam/reports/subject_pass_percentage.blade.php --}}
@extends('layouts.master')

@section('main-content')
<div class="p-4">
    <h2 class="mb-4 fw-bold text-center">Subject wise Pass % (≥35)</h2>

    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
        <select id="examSelect" class="form-select" style="width:260px;">
            <option value="">-- Select Exam --</option>
            @foreach($exams as $index => $exam)
                <option value="{{ $exam->id }}" {{ $index === 0 ? 'selected' : '' }}>
                    {{ $exam->name }} - {{ $exam->year }}
                </option>
            @endforeach
        </select>

        <select id="compareExamSelect" class="form-select" style="width:320px;">
            <option value="">-- Select Exam (or Zonal) to Compare --</option>
            @foreach($exams as $exam)
                <option value="{{ $exam->id }}">
                    {{ $exam->name }} - {{ $exam->year }}
                </option>
            @endforeach
        </select>

        <button id="compareBtn" class="btn btn-outline-primary" disabled>Compare</button>

        <!-- Loading spinner -->
        <div id="loadingSpinner" style="display:none; margin-left:10px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
        </div>
    </div>

    <!-- Chart -->
    <div id="chartContainer" style="height:600px;">
        <canvas id="zonalChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
let zonalChart;
let compareMode = false;

/* ---------- chart helpers (unchanged) ---------- */
function adjustCompareHeight(subjectCount){
    const container = document.getElementById('chartContainer');
    const barHeight = 50; // pixels per subject
    const minHeight = 400; // minimum height
    const newHeight = Math.max(minHeight, subjectCount * barHeight);
    container.style.height = newHeight + 'px';
    container.style.overflowY = subjectCount * barHeight > minHeight ? 'auto' : 'hidden';
}

function renderChart(canvasId, subjects, data, title){
    const ctx = document.getElementById(canvasId).getContext('2d');
    const colorPalette = ['#36a2eb','#ff6384','#4bc0c0','#ffcd56','#9966ff','#ff9f40'];
    const backgroundColors = subjects.map((sub,i)=>{
        const base = colorPalette[i % colorPalette.length];
        const grad = ctx.createLinearGradient(0,0,400,0);
        grad.addColorStop(0, base);
        grad.addColorStop(1, shadeColor(base, 60));
        return grad;
    });

    return new Chart(ctx,{
        type:'bar',
        data:{ labels: subjects, datasets:[{label:title, data:subjects.map(s=>data[s]??0), backgroundColor:backgroundColors }]},
        options:{
            responsive:true,
            maintainAspectRatio:false,
            plugins:{
                legend:{display:false},
                title:{display:true,text:title,color:'#000',font:{size:18,weight:'bold'},padding:{top:20,bottom:20}},
                datalabels:{anchor:'end',align:'end',color:'#000',font:{weight:'bold',size:14},formatter:v=>v+'%'}
            },
            scales: {
                y: {beginAtZero:true, max:100, ticks:{color:'#000', font:{size:14,weight:'bold'}, callback:v=>v+'%'}},
                x: {ticks:{color:'#000', font:{size:14,weight:'bold'}, maxRotation:90, minRotation:90}}
            }
        },
        plugins:[ChartDataLabels]
    });
}

function renderCompareChart(subjects, schoolName, schoolData, zonalLabel, zonalData){
    adjustCompareHeight(subjects.length);

    const ctx = document.getElementById('zonalChart').getContext('2d');
    return new Chart(ctx,{
        type:'bar',
        data:{
            labels: subjects,
            datasets:[
                {label: schoolName, data: subjects.map(s=>schoolData[s]??0), backgroundColor:'#36a2eb'},
                {label: zonalLabel, data: subjects.map(s=>zonalData[s]??0), backgroundColor:'#ff6384'}
            ]
        },
        options:{
            indexAxis:'y',
            responsive:true,
            maintainAspectRatio:false,
            plugins:{
                legend:{display:true, position:'top', labels:{color:'#000', font:{size:14, weight:'bold'}}},
                datalabels:{anchor:'end', align:'end', color:'#000', font:{weight:'bold',size:14}, formatter:v=>v+'%'},
                title:{display:true, text:`${schoolName} vs ${zonalLabel}`, color:'#000', font:{size:18, weight:'bold'}, padding:{top:20,bottom:20}}
            },
            scales: {
                x: {beginAtZero:true, max:100, ticks:{color:'#000', font:{size:16,weight:'bold'}, callback:v=>v+'%'}},
                y: {ticks:{color:'#000', font:{size:16,weight:'bold'}}}
            }
        },
        plugins:[ChartDataLabels]
    });
}

function shadeColor(color, percent) {
    let R = parseInt(color.substring(1,3),16);
    let G = parseInt(color.substring(3,5),16);
    let B = parseInt(color.substring(5,7),16);
    R = parseInt(R + (255 - R) * percent / 100);
    G = parseInt(G + (255 - G) * percent / 100);
    B = parseInt(B + (255 - B) * percent / 100);
    return `rgb(${R},${G},${B})`;
}

/* ---------- data fetching ---------- */
function fetchExamData(examId){
    return fetch(`{{ route('reports.pass.data') }}?exam_id=${examId}`)
        .then(res => res.json());
}

/* ---------- select helpers ---------- */
// primaryId selection will disable exact numeric matching option in secondary select
function syncSelectOptions(primaryId, secondaryId){
    const primary = document.getElementById(primaryId);
    const secondary = document.getElementById(secondaryId);
    const primaryVal = primary.value;

    // enable all first
    Array.from(secondary.options).forEach(opt => opt.disabled = false);

    // if primary selected is a numeric exam id (not zonal:...), disable that exact numeric in secondary
    if(primaryVal && !primaryVal.startsWith('zonal:')){
        const match = Array.from(secondary.options).find(opt => opt.value === primaryVal);
        if(match) match.disabled = true;
    }
}

// union subjects (preserve left order, add missing from right)
function unionSubjects(objA, objB){
    const set = new Set();
    const arr = [];
    if(objA){
        Object.keys(objA).forEach(k => { set.add(k); arr.push(k); });
    }
    if(objB){
        Object.keys(objB).forEach(k => {
            if(!set.has(k)){ set.add(k); arr.push(k); }
        });
    }
    return arr;
}

/* ---------- main loader ---------- */
/*
 - examIdLeft: numeric exam id for the school's exam (examSelect)
 - examIdRight: could be:
     - numeric exam id (compare to zonal of that numeric exam)
     - string "zonal:ID" meaning zonal of the *left* exam (ID equals left exam id)
*/
function loadData(examIdLeft, examIdRight = null, compare=false){
    if(!examIdLeft) return;
    document.getElementById('loadingSpinner').style.display='inline-block';

    // helper to destroy existing chart safely
    function destroyChart(){ if(zonalChart) { try{ zonalChart.destroy(); }catch(e){} zonalChart = null; } }

    if(compare && examIdRight){
        // Determine how to fetch zonal data:
        // - if examIdRight startsWith 'zonal:' -> use the exam id after colon to fetch zonal (this is zonal of left exam)
        // - else examIdRight is numeric -> fetch compare exam and use its 'compare' (zonal) part
        const isZonalOfLeft = (typeof examIdRight === 'string' && examIdRight.startsWith('zonal:'));
        const rightExamIdToFetch = isZonalOfLeft ? examIdRight.split(':')[1] : examIdRight;

        Promise.all([ fetchExamData(examIdLeft), fetchExamData(rightExamIdToFetch) ])
        .then(([resLeft, resRight])=>{
            document.getElementById('loadingSpinner').style.display='none';
            destroyChart();

            const schoolName = Object.keys(resLeft.school)[0];
            const schoolData = Object.values(resLeft.school)[0];

            // zonal label and data come from resRight.compare (endpoint returns compare => { 'Zonal Avg': {...} })
            const zonalLabelRaw = Object.keys(resRight.compare)[0] || 'Zonal Avg';
            const zonalData = Object.values(resRight.compare)[0] || {};

            // create user-facing label: if user chose zonal of left exam, show that explicitly
            const zonalLabel = isZonalOfLeft
                ? `${zonalLabelRaw} (Zonal of Selected Exam)`
                : `${zonalLabelRaw} (Exam ${rightExamIdToFetch})`;

            // union subjects so both sides align
            const subjects = unionSubjects(schoolData, zonalData);

            zonalChart = renderCompareChart(subjects, schoolName, schoolData, zonalLabel, zonalData);
        })
        .catch(err=>{
            document.getElementById('loadingSpinner').style.display='none';
            console.error(err);
            alert('Error loading data');
        });
    } else {
        // single exam school chart (no compare)
        fetchExamData(examIdLeft)
        .then(res=>{
            document.getElementById('loadingSpinner').style.display='none';
            destroyChart();

            const schoolName = Object.keys(res.school)[0];
            const schoolData = Object.values(res.school)[0];
            const subjects = Object.keys(schoolData);

            document.getElementById('chartContainer').style.height='600px';
            document.getElementById('chartContainer').style.overflowY='hidden';
            zonalChart = renderChart('zonalChart', subjects, schoolData, schoolName);
        })
        .catch(err=>{
            document.getElementById('loadingSpinner').style.display='none';
            console.error(err);
            alert('Error loading data');
        });
    }
}

/* ---------- wiring UI ---------- */
document.addEventListener("DOMContentLoaded",()=>{
    const examSelect = document.getElementById('examSelect');
    const compareExamSelect = document.getElementById('compareExamSelect');
    const compareBtn = document.getElementById('compareBtn');

    // Insert zonal option into compareExamSelect when examSelect changes
    function ensureZonalOptionForSelectedExam(){
        const selectedExamId = examSelect.value;
        // Remove any previous zonal:... option(s)
        Array.from(compareExamSelect.options).forEach(opt=>{
            if(opt.value && opt.value.startsWith('zonal:')) opt.remove();
        });

        if(selectedExamId){
            // Create a zonal option that references the selected exam's zonal
            const examText = examSelect.options[examSelect.selectedIndex].text || `Exam ${selectedExamId}`;
            const zonalOpt = document.createElement('option');
            zonalOpt.value = `zonal:${selectedExamId}`;
            zonalOpt.text = `Zonal Avg - ${examText}`;
            // place zonal option at the top (after placeholder)
            compareExamSelect.insertBefore(zonalOpt, compareExamSelect.options[1] || null);
        }
    }

    // initial zonal insertion + disable matching options
    ensureZonalOptionForSelectedExam();
    syncSelectOptions('examSelect','compareExamSelect');
    syncSelectOptions('compareExamSelect','examSelect');

    // initial load
    if(examSelect.value){
        loadData(examSelect.value,false);
    }

    function updateCompareBtnState(){
        const a = examSelect.value;
        const b = compareExamSelect.value;
        // enable only when left selected and compare selection exists and not the exact same numeric exam id
        const invalidSameNumeric = (a && b && !b.startsWith('zonal:') && a === b);
        compareBtn.disabled = !(a && b && !invalidSameNumeric);
    }

    examSelect.addEventListener('change', function(){
        ensureZonalOptionForSelectedExam();
        syncSelectOptions('examSelect','compareExamSelect');
        updateCompareBtnState();
        compareMode = false;
        compareBtn.textContent = 'Compare';
        loadData(this.value, null, false);
    });

    compareExamSelect.addEventListener('change', function(){
        syncSelectOptions('compareExamSelect','examSelect');
        updateCompareBtnState();
        if(compareMode){
            const left = examSelect.value;
            const right = compareExamSelect.value;
            if(left && right && !(left === right && !right.startsWith('zonal:'))){
                loadData(left, right, true);
            } else {
                compareMode = false;
                compareBtn.textContent = 'Compare';
            }
        }
    });

    // Compare toggle
    compareBtn.addEventListener('click', function(){
        const left = examSelect.value;
        const right = compareExamSelect.value;
        if(!left || !right) return alert('Please select an exam and a compare option.');

        // disallow selecting the exact same numeric exam in both selects
        if(!right.startsWith('zonal:') && left === right) return alert('Please select two different exams (or use the Zonal option).');

        compareMode = !compareMode;
        this.textContent = compareMode ? 'Hide Compare' : 'Compare';

        if(compareMode){
            loadData(left, right, true);
        } else {
            loadData(left, null, false);
        }
    });
});
</script>
@endpush
