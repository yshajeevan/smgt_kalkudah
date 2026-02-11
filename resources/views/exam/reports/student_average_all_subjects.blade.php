{{-- resources/views/reports/student_subject_awards.blade.php --}}
@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4" style="font-weight:bold; color:#000;">Student's Subject wise Marks</h2>

    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
        <!-- Exam dropdown -->
        <select id="examSelect" class="form-select" style="width:260px;">
            <option value="">-- Select Exam --</option>
            @foreach($exams as $exam)
                <option value="{{ $exam->id }}">{{ $exam->name }} - {{ $exam->year }}</option>
            @endforeach
        </select>

        <!-- Student dropdown -->
        <select id="studentSelect" class="form-select" style="width:300px;" disabled>
            <option value="">-- Select Student --</option>
        </select>

        <!-- Print Button -->
        <button id="printBtn" class="btn btn-primary mb-3">
            <i class="bi bi-printer"></i> Print
        </button>

        <!-- Continuous Print Button -->
        <button id="continuousPrintBtn" class="btn btn-success mb-3">
            <i class="bi bi-printer-fill"></i> Continuous Print
        </button>

        <div id="loadingSpinner" style="display:none; margin-left:10px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div style="height:650px; padding-right:150px;">
                <canvas id="studentChart" style="width:100% !important;"></canvas>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <h4 style="font-weight:bold; color:#000;">Subject wise Awards</h4>
            <table class="table table-bordered" id="awardsTable" style="font-size:16px; font-weight:bold; color:#000;">
                <thead>
                    <tr>
                        <th style="text-align:left;">Subject</th>
                        <th>Marks</th>
                        <th>Award</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="3" class="text-center">Select student to view awards</td></tr>
                </tbody>
            </table>

            <!-- Best Improvement table will be inserted here dynamically if response contains improvementAwards -->
            <div id="improvementWrapper"></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
@media print {
    @page { size: A4 portrait; margin: 15mm; }
    body { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; }
    #printBtn, #continuousPrintBtn, h2 { display: none !important; }
}

/* Simple print-ready table styles included for continuous print page */
.print-table { width:100%; border-collapse:collapse; margin-top:10px; }
.print-table th, .print-table td { border:1px solid #000; padding:6px; font-size:13px; }
.print-table th:first-child, .print-table td:first-child { text-align:left; }
.chart-img { width:100%; max-height:450px; object-fit:contain; display:block; margin:12px 0; }
</style>
@endpush

@push('scripts')
<!-- Required libs -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
Chart.register(ChartDataLabels);

// color palette (kept simple / grayscale as before)
const COLOR_PALETTE = [
    '#000000','#444444','#666666','#888888',
    '#aaaaaa','#222222','#555555','#999999',
    '#333333','#777777','#bbbbbb','#111111'
];

// ---------------- Chart helpers ----------------
function createChartConfig(exams, marksData, opts = { forPrint: false }) {
    const subjects = Object.keys(Object.values(marksData || {})[0] || {});
    const datasets = exams.map((exam, di) => ({
        label: exam,
        data: subjects.map(sub => marksData[exam]?.[sub] ?? 0),
        backgroundColor: subjects.map((_, idx) => COLOR_PALETTE[idx % COLOR_PALETTE.length]),
        borderColor: '#000',
        borderWidth: 1
    }));

    return {
        type: 'bar',
        data: { labels: subjects, datasets },
        options: {
            responsive: !opts.forPrint,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { color: '#000', font: { size: 16, weight: 'bold' } } },
                datalabels: {
                    anchor: 'end', align: 'end',
                    color:'#000',
                    font: { weight:'bold', size:16 },
                    formatter: v => Math.round(v)
                }
            },
            scales: {
                y: { beginAtZero:true, min:0, max:100,
                    ticks:{ stepSize:10, color:'#000', font:{size:16, weight:'bold'} },
                    grid:{ color:'#00000020' }
                },
                x: { ticks:{ color:'#000', font:{size:16, weight:'bold'}, autoSkip:false, minRotation:20, maxRotation:45 },
                    grid:{ color:'#00000020' }
                }
            },
            animation: opts.forPrint ? { duration: 400 } : false
        }
    };
}

let studentChart;
function renderStudentChart(canvasId, exams, data) {
    if(studentChart) studentChart.destroy();
    const ctx = document.getElementById(canvasId).getContext('2d');
    studentChart = new Chart(ctx, createChartConfig(exams, data, { forPrint: false }));
    studentChart.update();
}

// produce chart image suitable for print (returns dataURL)
function chartImageFromData(marksData, opts = { width: 1200, height: 800 }) {
    return new Promise((resolve, reject) => {
        try {
            const DPR = window.devicePixelRatio || 1;
            const canvas = document.createElement('canvas');
            canvas.style.width = opts.width + 'px';
            canvas.style.height = opts.height + 'px';
            canvas.width = Math.round(opts.width * DPR);
            canvas.height = Math.round(opts.height * DPR);
            const ctx = canvas.getContext('2d');
            ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
            const exams = Object.keys(marksData || {});
            const cfg = createChartConfig(exams, marksData, { forPrint: true });
            cfg.options.animation = cfg.options.animation || {};
            cfg.options.animation.onComplete = () => {
                try {
                    resolve(canvas.toDataURL('image/png'));
                } catch (err) { reject(err); }
                if(chartInstance) chartInstance.destroy();
            };
            const chartInstance = new Chart(ctx, cfg);
        } catch (err) { reject(err); }
    });
}

// ---------------- Select2 setup & fetching students ----------------
$(document).ready(function() {
    $('#examSelect').select2({ placeholder: '-- Select Exam --', width: '260px' });
    $('#studentSelect').select2({ placeholder: '-- Select Student --', width: '300px' });

    $('#examSelect').on('change', function() {
        let examId = $(this).val();
        $('#studentSelect').empty().trigger('change').prop('disabled', true);
        if(!examId) return;
        $('#loadingSpinner').show();
        $.ajax({
            url: "{{ route('reports.getStudentsByExam') }}",
            data: { exam_id: examId },
            success: function(students) {
                $('#loadingSpinner').hide();
                students.sort((a, b) => a.id - b.id);
                $('#studentSelect').prop('disabled', false).append(new Option('-- Select Student --', ''));
                students.forEach(stu => {
                    $('#studentSelect').append(new Option(`${stu.name} (${stu.id})`, stu.id));
                });
                $('#studentSelect').trigger('change');
            },
            error: function() {
                $('#loadingSpinner').hide();
                alert('Error fetching students');
            }
        });
    });

    $('#studentSelect').on('change', loadStudentData);
    $('#examSelect').on('change', loadStudentData);
});

// ---------------- Load student data, awards & improvement ----------------
function loadStudentData() {
    const studentId = $('#studentSelect').val();
    const examId = $('#examSelect').val();
    if(!studentId || !examId) return;

    $('#loadingSpinner').show();

    Promise.all([
        fetch(`{{ route('reports.student.average.allsubject.marks.data') }}?student_id=${studentId}&exam_id=${examId}`).then(r=>r.json()),
        fetch(`{{ route('reports.student.awards') }}?student_id=${studentId}&exam_id=${examId}`).then(r=>r.json())
    ])
    .then(([marksData, awardsRes]) => {
        $('#loadingSpinner').hide();

        // Render chart: marksData expected shape: { "Exam name - year": { "Subject": marks, ... }, ... }
        const exams = Object.keys(marksData || {});
        renderStudentChart('studentChart', exams, marksData);

        // Populate Subject wise Awards table
        let tbody = $('#awardsTable tbody').empty();
        if (awardsRes.studentAwards && awardsRes.studentAwards.length) {
            awardsRes.studentAwards.forEach(a => {
                let icon = a.award === 'Gold' ? '🥇 (Gold)' :
                           a.award === 'Silver' ? '🥈 (Silver)' :
                           a.award === 'Bronze' ? '🥉 (Bronze)' : '💪 Keep Trying';
                tbody.append(`<tr>
                    <td style="text-align:left;">${a.subject}</td>
                    <td style="text-align:center;">${a.mark}</td>
                    <td style="text-align:center; font-size:18px;">${icon}</td>
                </tr>`);
            });
        } else {
            tbody.append(`<tr><td colspan="3" class="text-center">No awards data</td></tr>`);
        }

        // Populate Best Improvement table (create if not exists)
        $('#improvementWrapper').empty();
        if (awardsRes.improvementAwards && awardsRes.improvementAwards.length) {
            let impHtml = `
                <h4 style="font-weight:bold; color:#000; margin-top:20px;">Best Improvement</h4>
                <table class="table table-bordered" id="improvementTable" style="font-size:16px; font-weight:bold; color:#000;">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Subject</th>
                            <th>Previous</th>
                            <th>Current</th>
                            <th>Improvement</th>
                            <th>Award</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>`;
            $('#improvementWrapper').append(impHtml);

            const impTbody = $('#improvementTable tbody');
            awardsRes.improvementAwards.forEach(i => {
                let awardIcon = i.award === 'Gold' ? '🥇 Gold' :
                                i.award === 'Silver' ? '🥈 Silver' :
                                i.award === 'Bronze' ? '🥉 Bronze' : '💪 Keep Trying';
                const prev = (i.previous_mark !== null && i.previous_mark !== undefined) ? i.previous_mark : '-';
                const curr = (i.current_mark !== null && i.current_mark !== undefined) ? i.current_mark : '-';
                const delta = (i.improvement !== null && i.improvement !== undefined) ? (i.improvement > 0 ? '+' + Number(i.improvement).toFixed(2) : Number(i.improvement).toFixed(2)) : '-';
                impTbody.append(`<tr>
                    <td style="text-align:left;">${i.subject}</td>
                    <td style="text-align:center;">${prev}</td>
                    <td style="text-align:center;">${curr}</td>
                    <td style="text-align:center;">${delta}</td>
                    <td style="text-align:center; font-size:18px;">${awardIcon}</td>
                </tr>`);
            });
        }
    })
    .catch(err => {
        $('#loadingSpinner').hide();
        console.error(err);
        alert("Error loading data");
    });
}

// ---------------- Simple print ----------------
$('#printBtn').on('click', () => {
    setTimeout(() => window.print(), 300);
});

// ---------------- Continuous print (all students) ----------------
$('#continuousPrintBtn').on('click', function() {
    let examId = $('#examSelect').val();
    if (!examId) { alert("Please select an exam first"); return; }
    $('#loadingSpinner').show();

    $.ajax({
        url: "{{ route('reports.getStudentsByExam') }}",
        data: { exam_id: examId },
        success: async function(students) {
            if (!students.length) { $('#loadingSpinner').hide(); alert("No students found"); return; }
            students.sort((a, b) => a.id - b.id);

            let win = window.open('', '', 'width=1000,height=800');
            win.document.write(`<html><head><title>Continuous Print</title>
                <style>
                    body{font-family:Arial;color:#000;}
                    h2,h3{font-weight:bold;}
                    table{width:100%;border-collapse:collapse;margin-top:10px;}
                    th,td{border:1px solid #000;padding:6px;font-size:13px;}
                    th:first-child, td:first-child { text-align:left; }
                    th,td:not(:first-child){ text-align:center; }
                    .chart-img{width:100%;max-height:450px;margin:15px 0;}
                    .page-break{page-break-after:always;margin-bottom:30px;}
                    .student-block{margin-bottom:40px;}
                </style></head><body>
                <h2>Exam: ${$('#examSelect option:selected').text()}</h2>`);

            for (let stu of students) {
                try {
                    // fetch marks & awards for student
                    let [marksData, awardsRes] = await Promise.all([
                        fetch(`{{ route('reports.student.average.allsubject.marks.data') }}?student_id=${stu.id}&exam_id=${examId}`).then(r=>r.json()),
                        fetch(`{{ route('reports.student.awards') }}?student_id=${stu.id}&exam_id=${examId}`).then(r=>r.json())
                    ]);

                    // chart image
                    let chartImg = await chartImageFromData(marksData, { width: 1200, height: 800 });

                    // awards html
                    let awardsHtml = (awardsRes.studentAwards || []).map(a => {
                        let icon = a.award === 'Gold' ? '🥇 Gold' :
                                   a.award === 'Silver' ? '🥈 Silver' :
                                   a.award === 'Bronze' ? '🥉 Bronze' : '💪 Keep Trying';
                        return `<tr><td style="text-align:left;">${a.subject}</td><td style="text-align:center;">${a.mark}</td><td style="text-align:center;">${icon}</td></tr>`;
                    }).join('');

                    // improvement html
                    let improvementHtml = '';
                    if (awardsRes.improvementAwards && awardsRes.improvementAwards.length) {
                        improvementHtml = (awardsRes.improvementAwards || []).map(i => {
                            let awardIcon = i.award === 'Gold' ? '🥇 Gold' :
                                           i.award === 'Silver' ? '🥈 Silver' :
                                           i.award === 'Bronze' ? '🥉 Bronze' : '💪 Keep Trying';
                            const prev = (i.previous_mark !== null && i.previous_mark !== undefined) ? i.previous_mark : '-';
                            const curr = (i.current_mark !== null && i.current_mark !== undefined) ? i.current_mark : '-';
                            const delta = (i.improvement !== null && i.improvement !== undefined) ? (i.improvement > 0 ? '+' + Number(i.improvement).toFixed(2) : Number(i.improvement).toFixed(2)) : '-';
                            return `<tr>
                                <td style="text-align:left;">${i.subject}</td>
                                <td style="text-align:center;">${prev}</td>
                                <td style="text-align:center;">${curr}</td>
                                <td style="text-align:center;">${delta}</td>
                                <td style="text-align:center;">${awardIcon}</td>
                            </tr>`;
                        }).join('');
                    }

                    win.document.write(`<div class="student-block">
                        <h3>${stu.name} (${stu.id})</h3>
                        <img src="${chartImg}" class="chart-img" />
                        <h4>Subject wise Awards</h4>
                        <table class="print-table"><thead><tr><th>Subject</th><th>Marks</th><th>Award</th></tr></thead>
                        <tbody>${awardsHtml}</tbody></table>
                        ${ improvementHtml ? `<h4 style="margin-top:12px;">Best Improvement</h4>
                            <table class="print-table"><thead><tr><th>Subject</th><th>Previous</th><th>Current</th><th>Improvement</th><th>Award</th></tr></thead>
                            <tbody>${improvementHtml}</tbody></table>` : '' }
                        </div><div class="page-break"></div>`);
                } catch(err) {
                    console.error("Error processing student", stu.id, err);
                }
            }

            $('#loadingSpinner').hide();
            win.document.write("</body></html>");
            win.document.close();
            win.focus();
            win.print();
        },
        error: function() {
            $('#loadingSpinner').hide();
            alert("Error fetching students");
        }
    });
});
</script>
@endpush
