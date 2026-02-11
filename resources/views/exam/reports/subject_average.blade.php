{{-- resources/views/exam/reports/subject_average.blade.php --}}
@extends('layouts.master')

@section('main-content')
<div class="p-4">
    <h2 class="mb-4 fw-bold text-center">Subject wise Average</h2>

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
                <option value="{{ $exam->id }}">{{ $exam->name }} - {{ $exam->year }}</option>
            @endforeach
        </select>

        <button id="compareBtn" class="btn btn-outline-primary" disabled>Compare</button>

        <!-- Loading spinner -->
        <div id="loadingSpinner" style="display:none; margin-left:10px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
        </div>
    </div>

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

/* ---------- helpers (unchanged chart styling) ---------- */
function shadeColor(color, percent) {
    let R = parseInt(color.substring(1,3),16);
    let G = parseInt(color.substring(3,5),16);
    let B = parseInt(color.substring(5,7),16);
    R = parseInt(R + (255 - R) * percent / 100);
    G = parseInt(G + (255 - G) * percent / 100);
    B = parseInt(B + (255 - B) * percent / 100);
    return `rgb(${R},${G},${B})`;
}

function adjustCompareHeight(subjectCount){
    const container = document.getElementById('chartContainer');
    const barHeight = 50; // pixels per subject
    const minHeight = 400; // minimum height
    const newHeight = Math.max(minHeight, subjectCount * barHeight);
    container.style.height = newHeight + 'px';
    container.style.overflowY = subjectCount * barHeight > minHeight ? 'auto' : 'hidden';
}

/* ---------- charts ---------- */
function renderSchoolChart(subjects, schoolName, schoolData) {
    const ctx = document.getElementById('zonalChart').getContext('2d');
    const colorPalette = ['#36a2eb','#ff6384','#4bc0c0','#ffcd56','#9966ff','#ff9f40'];
    const backgroundColors = subjects.map((sub,i)=>colorPalette[i % colorPalette.length]);

    return new Chart(ctx,{
        type:'bar',
        data:{
            labels: subjects,
            datasets:[{
                label: schoolName,
                data: subjects.map(s=>schoolData[s]??0),
                backgroundColor: backgroundColors
            }]
        },
        options:{
            responsive:true,
            maintainAspectRatio:false,
            plugins:{
                legend: { display: false },
                datalabels:{
                    anchor:'end', align:'end', color:'#000', font:{weight:'bold',size:14},
                    formatter:v=>v+''
                },
                title:{
                    display:true, text: schoolName, color:'#000', font:{size:18,weight:'bold'}, padding:{top:20,bottom:20}
                }
            },
            indexAxis:'x', // vertical bars
            scales:{
                y:{beginAtZero:true, ticks:{callback:v=>v, color:'#000', font:{size:14,weight:'bold'}}},
                x:{ticks:{color:'#000', font:{size:14,weight:'bold'}}}
            }
        },
        plugins:[ChartDataLabels]
    });
}

function renderCompareChart(subjects, schoolName, schoolData, zonalLabel, zonalData) {
    adjustCompareHeight(subjects.length); // adjust height for compare chart

    const ctx = document.getElementById('zonalChart').getContext('2d');
    return new Chart(ctx,{
        type:'bar',
        data:{
            labels: subjects,
            datasets:[
                {label: schoolName, data: subjects.map(s=>Number(schoolData[s]??0),), backgroundColor:'#36a2eb'},
                {label: zonalLabel, data: subjects.map(s=>Number(zonalData[s]??0),), backgroundColor:'#ff6384'}
            ]
        },
        options:{
            indexAxis:'y', // horizontal bars
            responsive:true,
            maintainAspectRatio:false,
            plugins:{
                legend:{
                    display:true,
                    position:'top',
                    labels:{color:'#000', font:{size:14, weight:'bold'}}
                },
                datalabels:{
                    anchor:'end', align:'end', color:'#000', font:{weight:'bold',size:14},
                    formatter:v=>v+''
                },
                title: {
                    display: true,
                    text: `${schoolName} vs ${zonalLabel}`,
                    color: '#000',
                    font: { size: 18, weight: 'bold' },
                    padding: { top: 20, bottom: 20 }
                }
            },
            scales:{
                x:{beginAtZero:true, ticks:{color:'#000', font:{size:16,weight:'bold'}}},
                y:{ticks:{color:'#000', font:{size:16,weight:'bold'}}}
            }
        },
        plugins:[ChartDataLabels]
    });
}

/* ---------- data fetching ---------- */
function fetchExamData(examId){
    return fetch(`{{ route('reports.average.data') }}?exam_id=${examId}`)
        .then(res => res.json());
}

/* ---------- select helpers ---------- */
// prevent selecting same numeric exam in both selects (zonal:... is distinct)
function syncSelectOptions(primaryId, secondaryId){
    const primary = document.getElementById(primaryId);
    const secondary = document.getElementById(secondaryId);
    const primaryVal = primary.value;

    // enable all first
    Array.from(secondary.options).forEach(opt => opt.disabled = false);

    if(primaryVal && !primaryVal.startsWith('zonal:')){
        const match = Array.from(secondary.options).find(opt => opt.value === primaryVal);
        if(match) match.disabled = true;
    }
}

// preserve left order when unioning subjects, add missing from right afterwards
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

/* ---------- main load logic ---------- */
/*
 - leftExamId: numeric id for school's exam
 - rightOption: either numeric exam id (compare to zonal of that exam) or 'zonal:ID' meaning zonal of that exam ID
*/
function loadData(leftExamId, rightOption = null, compare=false){
    if(!leftExamId) return;
    document.getElementById('loadingSpinner').style.display='inline-block';

    function destroyChart(){ if(zonalChart){ try{ zonalChart.destroy(); }catch(e){} zonalChart = null; } }

    if(compare && rightOption){
        const isZonalOfLeft = (typeof rightOption === 'string' && rightOption.startsWith('zonal:'));
        const rightExamIdToFetch = isZonalOfLeft ? rightOption.split(':')[1] : rightOption;

        Promise.all([ fetchExamData(leftExamId), fetchExamData(rightExamIdToFetch) ])
        .then(([resLeft, resRight])=>{
            document.getElementById('loadingSpinner').style.display='none';
            destroyChart();

            // left: use resLeft.compare as school data (same shape as your original code expects)
            const schoolName = Object.keys(resLeft.compare)[0] || 'Your School';
            const schoolData = Object.values(resLeft.compare)[0] || {};

            // zonal data: use resRight.zonal (object where keys are schools and values are subject averages)
            // compute zonal average for each subject (mean of school averages)
            const zonalSource = resRight.zonal || {};
            // determine all subjects union
            const subjects = unionSubjects(schoolData, (Object.keys(zonalSource).length? zonalSource[Object.keys(zonalSource)[0]] : {} ));

            const zonalData = {};
            subjects.forEach(sub=>{
                let total = 0, cnt = 0;
                for(const schoolKey in zonalSource){
                    if(zonalSource[schoolKey] && zonalSource[schoolKey][sub] !== undefined){
                        total += Number(zonalSource[schoolKey][sub]);
                        cnt++;
                    }
                }
                zonalData[sub] = cnt>0 ? parseFloat((total/cnt).toFixed(2)) : 0;
            });

            const zonalLabel = isZonalOfLeft ? `Zonal Avg (Selected Exam)` : `Zonal Avg (Exam ${rightExamIdToFetch})`;

            zonalChart = renderCompareChart(subjects, schoolName, schoolData, zonalLabel, zonalData);
        })
        .catch(err=>{
            document.getElementById('loadingSpinner').style.display='none';
            console.error(err);
            alert('Error loading data');
        });
    } else {
        // simple school average chart for single exam
        fetchExamData(leftExamId)
        .then(res=>{
            document.getElementById('loadingSpinner').style.display='none';
            destroyChart();

            const schoolName = Object.keys(res.compare)[0] || 'Your School';
            const schoolData = Object.values(res.compare)[0] || {};
            const subjects = Object.keys(schoolData);

            document.getElementById('chartContainer').style.height = '600px';
            document.getElementById('chartContainer').style.overflowY = 'hidden';
            zonalChart = renderSchoolChart(subjects, schoolName, schoolData);
        })
        .catch(err=>{
            document.getElementById('loadingSpinner').style.display='none';
            console.error(err);
            alert('Error loading data');
        });
    }
}

/* ---------- UI wiring ---------- */
document.addEventListener("DOMContentLoaded",()=>{
    const examSelect = document.getElementById('examSelect');
    const compareExamSelect = document.getElementById('compareExamSelect');
    const compareBtn = document.getElementById('compareBtn');

    // Insert zonal option into compareExamSelect whenever examSelect changes
    function ensureZonalOptionForSelectedExam(){
        const selectedExamId = examSelect.value;
        // remove any existing zonal:... options
        Array.from(compareExamSelect.options).forEach(opt=>{
            if(opt.value && opt.value.startsWith('zonal:')) opt.remove();
        });

        if(selectedExamId){
            const examText = examSelect.options[examSelect.selectedIndex].text || `Exam ${selectedExamId}`;
            const zonalOpt = document.createElement('option');
            zonalOpt.value = `zonal:${selectedExamId}`;
            zonalOpt.text = `Zonal Avg - ${examText}`;
            // insert after placeholder (index 1)
            compareExamSelect.insertBefore(zonalOpt, compareExamSelect.options[1] || null);
        }
    }

    // initial setup
    ensureZonalOptionForSelectedExam();
    syncSelectOptions('examSelect','compareExamSelect');
    syncSelectOptions('compareExamSelect','examSelect');

    if(examSelect.value){
        loadData(examSelect.value, null, false);
        compareBtn.disabled = false;
    }

    function updateCompareBtnState(){
        const a = examSelect.value;
        const b = compareExamSelect.value;
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
