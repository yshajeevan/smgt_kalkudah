@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4 text-center fw-bold">G.C.E O/L Diagnosis Exam {{ $exam->year ?? '' }}</h2>
    <h5 class="text-center">Zonal Education Office, Batticaloa West</h5>

    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap no-print">
        <select id="examSelect" class="form-select" style="width:260px;">
            <option value="">-- Select Exam --</option>
            @foreach($exams as $ex)
                <option value="{{ $ex->id }}" {{ ($exam->id ?? '') == $ex->id ? 'selected' : '' }}>
                    {{ $ex->name }} - {{ $ex->year }}
                </option>
            @endforeach
        </select>

        <button id="printBtn" class="btn btn-primary ms-3">Print</button>
        <div id="loadingSpinner" style="display:none; margin-left:10px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
        </div>
    </div>

    <div id="studentReports"></div>
</div>
@endsection

@push('styles')
<style>
/* (keep your existing CSS unchanged) */
.student-report {
    border: 1px solid #000;
    padding: 15px;
    margin-bottom: 30px;
    font-size: 14px;
    background: #fff;
    display: block;
    overflow: auto;
}
.student-report::after {
    content: "";
    display: table;
    clear: both;
}
.subject-table {
    width: 70%;
    float: left;
    border-collapse: collapse;
    margin-top: 10px;
}
.subject-table th, .subject-table td {
    border: 1px solid #000;
    padding: 5px 8px;
    text-align: center;
}
.subject-table th:first-child,
.subject-table td:first-child {
    text-align: left;
}
.summary-box {
    border: 1px solid #000;
    padding: 5px;
    width: 25%;
    float: right;
    margin-top: 10px;
    box-sizing: border-box;
}
.tamil-section {
    border: 1px solid #000;
    padding: 8px;
    margin-top: 15px;
    min-height: 80px;
    clear: both;
}
@page { size: A4; margin: 12mm; }
@media print {
    .no-print,
    #examSelect,
    #printBtn,
    nav, header, footer, .navbar, .sidebar { display: none !important; }
    html, body { background: #fff !important; }
    .student-report {
        page-break-after: always;
        page-break-inside: avoid;
        break-after: page;
        break-inside: avoid;
        overflow: visible !important;
        display: block !important;
    }
    .student-report:last-child { page-break-after: auto !important; }
    .subject-table { width: 100% !important; float: none !important; }
    .summary-box { float: none !important; width: 100% !important; margin-top: 10px; }
}
</style>
@endpush

@push('scripts')
<script>
/*
  Logic:
  - Use Blade-passed $exams list in JS to determine all exams with id <= selectedId
  - For those exams (ascending id order), fetch data from backend for each exam_id
  - Combine per-student subject marks across exams and build table with dynamic columns:
      Subjects | (Marks1) | (Target1) | (Marks2) | (Target2) | ...
  - Summary computed using the LAST exam (i.e., the selected exam)
*/

const ALL_EXAMS = @json($exams);

function computeGradeFromMark(mark, is_absent) {
    if(is_absent) return {grade:'AB', display:'AB'};
    const m = parseFloat(mark);
    if (isNaN(m)) return {grade:'', display: mark};
    let grade='';
    if (m < 35) grade = 'W';
    else if (m < 50) grade = 'S';
    else if (m < 65) grade = 'C';
    else if (m < 75) grade = 'B';
    else grade = 'A';
    return {grade, display: m};
}

function fetchExamData(examId){
    return fetch(`{{ route('reports.student.marks.print.data') }}?exam_id=${examId}`)
        .then(res => {
            if(!res.ok) throw new Error('Fetch failed for exam '+examId);
            return res.json();
        });
}

function buildReportsForExams(examsToShow, responses){
    // responses: array of response objs matching examsToShow same index
    // Build a map of students keyed by unique id (prefer stu.id else stu.name)
    const studentsMap = new Map();
    const schoolName = (responses[0] && responses[0].schoolName) ? responses[0].schoolName : '';

    responses.forEach((res, idx) => {
        const examId = examsToShow[idx].id;
        // each res.students is array; ensure exists
        (res.students || []).forEach(stu => {
            const key = stu.id ?? stu.reg_no ?? stu.name; // fallback
            if(!studentsMap.has(key)){
                studentsMap.set(key, {
                    id: stu.id ?? null,
                    name: stu.name ?? '',
                    subjectsByExam: {}, // examId -> array of subjects {name, value, is_absent}
                });
            }
            const ent = studentsMap.get(key);
            ent.subjectsByExam[examId] = {};
            // store by subject name for easy union
            (stu.subjects || []).forEach(s => {
                ent.subjectsByExam[examId][s.name] = { value: s.value, is_absent: !!s.is_absent };
            });
        });
    });

    // For any students missing in some exam responses, ensure they still appear (union across responses)
    // (Already handled if presence differs — but if some exam has student not present others won't have entry; that's ok.)

    // Now render HTML for each student
    const container = document.getElementById('studentReports');
    container.innerHTML = '';

    // Determine the "latest" exam id for summary: choices: the highest id in examsToShow (selected exam)
    const latestExamId = Math.max(...examsToShow.map(e=>e.id));

    for(const [key, stu] of studentsMap){
        // Build union of subjects across the examsToShow for this student (preserve order by first exam)
        const subjectsOrder = [];
        const seen = new Set();
        examsToShow.forEach(ex => {
            const subjObj = stu.subjectsByExam[ex.id] || {};
            Object.keys(subjObj).forEach(sn => { if(!seen.has(sn)){ seen.add(sn); subjectsOrder.push(sn); }});
        });

        // compute summary based on latestExamId for this student
        const summary = {A:0,B:0,C:0,S:0,W:0};
        const latestSubjects = stu.subjectsByExam[latestExamId] || {};
        Object.keys(latestSubjects).forEach(sn => {
            const s = latestSubjects[sn];
            if(s.is_absent){
                // skip absent from summary counts or treat separately? original code used 'AB' and didn't count to summary
            } else {
                const g = computeGradeFromMark(s.value, false).grade;
                if(g && summary.hasOwnProperty(g)) summary[g]++;
            }
        });

        // Build tbody rows for subjects
        const rowsHtml = subjectsOrder.map(subName => {
            // For each exam create a cell with mark (and grade in parentheses) — if absent show AB
            const cells = examsToShow.map(ex => {
                const subjObj = stu.subjectsByExam[ex.id] || {};
                const valObj = subjObj[subName];
                if(!valObj) return `<td></td><td></td>`; // empty mark + empty target
                if(valObj.is_absent){
                    return `<td>AB</td><td></td>`;
                } else {
                    const mg = computeGradeFromMark(valObj.value, false);
                    return `<td>${mg.display}${mg.grade ? ' ('+mg.grade+')' : ''}</td><td></td>`;
                }
            }).join('');

            return `<tr>
                        <td>${subName}</td>
                        ${cells}
                    </tr>`;
        }).join('');

        // Build table headers dynamically: Subjects + for each exam => Marks Obtained (<exam.name>) then Target for next exam
        const ths = examsToShow.map(ex => {
            return `<th>Marks Obtained <br> (${ex.name} - ${ex.year})</th><th>Target for next exam</th>`;
        }).join('');

        const reportHtml = `
            <div class="student-report">
                <h6 style="font-size:16px; font-weight:700; color:#000;">
                    <strong>Name of the Student:</strong> ${stu.name}
                </h6>
                <h6 style="font-size:16px; font-weight:700; color:#000;">
                    <strong>Name of the School:</strong> ${schoolName}
                </h6>

                <table class="subject-table">
                    <thead>
                        <tr>
                            <th>Subjects</th>
                            ${ths}
                        </tr>
                    </thead>
                    <tbody>
                        ${rowsHtml}
                    </tbody>
                </table>

                <div class="summary-box">
                    <strong>Summary (Latest Exam)</strong><br>
                    A: ${summary.A}<br>
                    B: ${summary.B}<br>
                    C: ${summary.C}<br>
                    S: ${summary.S}<br>
                    W: ${summary.W}
                </div>

                <div class="tamil-section">
                    <h6 style="font-weight:bold;">உறுதி மொழி</h6>
                    <div style="border:1px solid #000; padding:10px; margin-bottom:15px; line-height:1.8;">
                        மேற்படி இலக்கினை அடைவதற்காக, நான் உறுதியான மனப்பாங்குடனும், 
                        முழு ஆர்வத்துடனும், வீட்டிலும் பாடசாலையிலும் விருப்பத்துடன் கற்றலில் ஈடுபடுவே​னெனவும்
                        பாடசாலைக்கும் பாடசாலையால் நடத்தப்படும் மேலதிக வகுப்புகளுக்கும் ஒழுங்காக வருகை தருவதுடன் 
                        பாடசாலை அதிபருக்கும் ஆசிரியர்களுக்கும் நன்மதிப்புள்ள மாணவ/மாணவியாக செயல்படுவேனெனவும் 
                        உறுதியளிக்கின்றேன்.
                    </div>
                    <br><br><br>
                    <div style="display:flex; justify-content:space-between; margin:20px 0;">
                        <div>மாணவர் கையொப்பம்</div>
                        <div>பெற்றோர் கையொப்பம்</div>
                    </div>

                    <p style="margin-top:30px;">
                        "நம்பிக்கையுடனும் உறுதியுடனும் செயற்படுவோமானால் நாம் எதையும் அடைய முடியும்!"
                    </p>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', reportHtml);
    } // end for students
}

document.getElementById('examSelect').addEventListener('change', function () {
    const selectedId = Number(this.value);
    if (!selectedId) return;

    document.getElementById('loadingSpinner').style.display = 'inline-block';

    // Determine exams to show: all exams with id <= selectedId, sorted ascending by id
    const examsToShow = ALL_EXAMS
        .filter(e => Number(e.id) <= selectedId)
        .sort((a,b)=> Number(a.id) - Number(b.id));

    if(examsToShow.length === 0){
        document.getElementById('loadingSpinner').style.display = 'none';
        return;
    }

    // Fetch data for all examsToShow in parallel
    const fetchPromises = examsToShow.map(ex => fetchExamData(ex.id));
    Promise.all(fetchPromises)
        .then(responses => {
            document.getElementById('loadingSpinner').style.display = 'none';
            buildReportsForExams(examsToShow, responses);
        })
        .catch(err => {
            document.getElementById('loadingSpinner').style.display = 'none';
            console.error(err);
            alert("Error loading data");
        });
});

// Print button (unchanged)
document.getElementById('printBtn').addEventListener('click', function() {
    const reportsHtml = document.getElementById('studentReports').innerHTML;
    if (!reportsHtml) return alert('No reports to print.');

    const printWindow = window.open('', '', 'height=900,width=1200');
    // collect first <style> only (your original approach). If you need all styles, adjust accordingly.
    const styleContent = document.querySelector('style') ? document.querySelector('style').innerHTML : '';
    printWindow.document.write(`
        <html>
        <head>
            <title>Student Reports</title>
            <style>${styleContent}</style>
        </head>
        <body>${reportsHtml}</body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
});
</script>
@endpush
