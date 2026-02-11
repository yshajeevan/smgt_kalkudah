@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4 fw-bold">Exam Item Analysis</h2>

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
            </select>
        </div>
        <div class="col-md-4">
            <button id="loadBtn" class="btn btn-primary">Load Analysis</button>
        </div>
    </div>

    <!-- Question-wise Table -->
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered" id="analysisTable">
                <thead class="table-dark">
                    <tr>
                        <th>Q.No</th>
                        <th>Competency</th>
                        <th>Syllabus</th>
                        <th>Total</th>
                        <th>Correct</th>
                        <th>Partial</th>
                        <th>Wrong</th>
                        <th>Not Attempted</th>
                        <th>% Correct</th>
                        <th>Difficulty Index</th>
                        <th>Discrimination Index</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Charts -->
    <div class="card mb-4"><div class="card-body"><canvas id="analysisChart" height="120"></canvas></div></div>
    <div class="card mb-4"><div class="card-body"><canvas id="syllabusChart" height="120"></canvas></div></div>
    <div class="card mb-4"><div class="card-body"><canvas id="competencyChart" height="120"></canvas></div></div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
let qChart, sChart, cChart;

// Dynamic subjects on exam change
$('#examSelect').change(function(){
    let exam = $(this).val();
    $('#subjectSelect').empty().append('<option value="">-- Select Subject --</option>');
    if(!exam) return;

    $.post("{{ route('reports.item.analysis.subjects') }}", {
        _token: "{{ csrf_token() }}",
        exam_id: exam
    }, function(subjects){
        subjects.forEach(s=>{
            $('#subjectSelect').append(`<option value="${s.id}">${s.cadre}</option>`);
        });
    });
});

// Load item analysis
$('#loadBtn').click(function(){
    let exam = $('#examSelect').val();
    let subject = $('#subjectSelect').val();

    if(!exam || !subject){ alert("Select exam & subject!"); return; }

    $.post("{{ route('reports.item.analysis.data') }}", {
        _token: "{{ csrf_token() }}",
        exam_id: exam,
        subject_id: subject
    }, function(data){
        let tbody = $('#analysisTable tbody');
        tbody.empty();

        let qLabels=[], qCorrect=[], qDiff=[], qDisc=[];
        let syllabusAgg={}, competencyAgg={};

        data.forEach(row=>{
            tbody.append(`<tr>
                <td>${row.question_no}</td>
                <td>${row.competency}</td>
                <td>${row.syllabus}</td>
                <td>${row.total}</td>
                <td>${row.correct}</td>
                <td>${row.partial}</td>
                <td>${row.wrong}</td>
                <td>${row.not_attempted}</td>
                <td>${row.correct_percent}%</td>
                <td>${row.difficulty_index}%</td>
                <td>${row.discrimination_index ?? ''}</td>
            </tr>`);

            qLabels.push("Q"+row.question_no);
            qCorrect.push(row.correct_percent);
            qDiff.push(row.difficulty_index);
            qDisc.push(row.discrimination_index ?? 0);

            if(!syllabusAgg[row.syllabus]) syllabusAgg[row.syllabus]={count:0,diff:0,disc:0};
            syllabusAgg[row.syllabus].count++;
            syllabusAgg[row.syllabus].diff += row.difficulty_index;
            syllabusAgg[row.syllabus].disc += (row.discrimination_index ?? 0);

            if(!competencyAgg[row.competency]) competencyAgg[row.competency]={count:0,diff:0,disc:0};
            competencyAgg[row.competency].count++;
            competencyAgg[row.competency].diff += row.difficulty_index;
            competencyAgg[row.competency].disc += (row.discrimination_index ?? 0);
        });

        if(qChart) qChart.destroy();
        if(sChart) sChart.destroy();
        if(cChart) cChart.destroy();

        const axisOptions = {
            ticks: { beginAtZero:true, color:"black", font:{size:14,weight:"bold"}, callback: v=>v+"%" }
        };

        // Question-wise chart
        qChart = new Chart(document.getElementById('analysisChart'),{
            type:'bar',
            data:{labels:qLabels,datasets:[
                {label:"% Correct", data:qCorrect, backgroundColor:'rgba(54,162,235,0.7)'},
                {label:"Difficulty Index", data:qDiff, backgroundColor:'rgba(75,192,192,0.7)'},
                {label:"Discrimination Index", data:qDisc, backgroundColor:'rgba(255,99,132,0.7)'}
            ]},
            options:{
                responsive:true,
                plugins:{
                    legend:{display:true},
                    title:{display:true,text:"Question-wise Analysis"},
                    datalabels:{
                        anchor:'end', align:'top', formatter:val=>val+"%", font:{weight:'bold',size:14,color:'black'}
                    }
                },
                scales:{
                    y:{beginAtZero:true,max:100,ticks:{callback:v=>v+"%",font:{size:14,weight:'bold'},color:'black'}},
                    x:{ticks:{font:{size:14,weight:'bold'},color:'black'}}
                }
            },
            plugins: [ChartDataLabels]
        });

        // Syllabus chart
        let syllabusLabels=[], syllabusDiff=[], syllabusDisc=[];
        for(let s in syllabusAgg){
            syllabusLabels.push(s);
            syllabusDiff.push((syllabusAgg[s].diff/syllabusAgg[s].count).toFixed(2));
            syllabusDisc.push((syllabusAgg[s].disc/syllabusAgg[s].count).toFixed(2));
        }
        sChart = new Chart(document.getElementById('syllabusChart'),{
            type:'bar',
            data:{labels:syllabusLabels,datasets:[
                {label:"Difficulty %", data:syllabusDiff, backgroundColor:'rgba(75,192,192,0.7)'},
                {label:"Discrimination", data:syllabusDisc, backgroundColor:'rgba(255,99,132,0.7)'}
            ]},
            options:{
                responsive:true,
                plugins:{legend:{display:true},title:{display:true,text:"Syllabus-wise Analysis"}, datalabels:{anchor:'end',align:'top',formatter:val=>val+"%", font:{weight:'bold',size:14,color:'black'}}},
                scales:{y:{beginAtZero:true,max:100,ticks:{callback:v=>v+"%",font:{size:14,weight:'bold'},color:'black'}}, x:{ticks:{font:{size:14,weight:'bold'},color:'black'}}}
            }
        });

        // Competency chart
        let compLabels=[], compDiff=[], compDisc=[];
        for(let c in competencyAgg){
            compLabels.push(c);
            compDiff.push((competencyAgg[c].diff/competencyAgg[c].count).toFixed(2));
            compDisc.push((competencyAgg[c].disc/competencyAgg[c].count).toFixed(2));
        }
        cChart = new Chart(document.getElementById('competencyChart'),{
            type:'bar',
            data:{labels:compLabels,datasets:[
                {label:"Difficulty %", data:compDiff, backgroundColor:'rgba(75,192,192,0.7)'},
                {label:"Discrimination", data:compDisc, backgroundColor:'rgba(255,99,132,0.7)'}
            ]},
            options:{
                responsive:true,
                plugins:{legend:{display:true},title:{display:true,text:"Competency-wise Analysis"}, datalabels:{anchor:'end',align:'top',formatter:val=>val+"%", font:{weight:'bold',size:14,color:'black'}}},
                scales:{y:{beginAtZero:true,max:100,ticks:{callback:v=>v+"%",font:{size:14,weight:'bold'},color:'black'}}, x:{ticks:{font:{size:14,weight:'bold'},color:'black'}}}
            }
        });
    });
});
</script>
@endpush
