@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4" style="font-weight:bold; color:#000;">Subject wise Analysis</h2>

    <div class="row mb-3">
        <div class="col-md-4">
            <select id="examSelect" class="form-select">
                <option value="">-- Select Exam --</option>
                @foreach($exams as $exam)
                    <option value="{{ $exam->id }}"
                        {{ $latestExam && $latestExam->id == $exam->id ? 'selected' : '' }}>
                        {{ $exam->name }} - {{ $exam->year }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select id="subjectSelect" class="form-select">
                <option value="">-- Select Subject --</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}"
                        {{ $defaultSubject && $defaultSubject->id == $subject->id ? 'selected' : '' }}>
                        {{ $subject->cadre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <button id="printBtn" class="btn btn-primary">Print</button>
        </div>
    </div>

    <div id="loadingSpinner" style="display:none; margin-bottom:15px;">
        <span class="spinner-border spinner-border-sm"></span> Loading...
    </div>

    <table class="table table-bordered table-striped" id="resultTable" style="display:none;">
        <thead class="table-dark">
            <tr>
                <th>S/N</th>
                <th>Schools</th>
                <th>A</th>
                <th>B</th>
                <th>C</th>
                <th>S</th>
                <th>W</th>
                <th>No Sat</th>
                <th>No Pass</th>
                <th>Pass %</th>
                <th>Adjusted Pass %</th>
                <th>Average</th>
                <th>Rank</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@push('styles')
<style>
#resultTable th {
    font-size: 16px;
    font-weight: bold;
    color: #fff; /* white text for headers */
    background-color: #343a40; /* dark header background */
}

#resultTable td {
    font-size: 16px;
    font-weight: bold;
    color: #000; /* black for all data */
}
.highlight-school {
    background-color: #d4edda !important; /* light green */
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const examSelect = document.getElementById('examSelect');
    const subjectSelect = document.getElementById('subjectSelect');
    const table = document.getElementById('resultTable');
    const tbody = table.querySelector('tbody');

    const highlightSchoolName = "ABC School"; // <-- change to the school you want to highlight

    function loadData(examId = examSelect.value, subjectId = subjectSelect.value) {
        if (!examId || !subjectId) return;

        document.getElementById('loadingSpinner').style.display = 'block';
        table.style.display = 'none';

        fetch(`{{ route('reports.school.subject.analysis.data') }}?exam_id=${examId}&subject_id=${subjectId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('loadingSpinner').style.display = 'none';
            tbody.innerHTML = '';

            data.sort((a, b) => b.adjusted_percentage - a.adjusted_percentage);

            const highlightSchoolName = "{{ $currentSchoolName }}";

            data.forEach((row, i) => {
                const adjustedPass = row.sat < 20 
                    ? `<span style="color:red;">${row.adjusted_percentage}%</span>` 
                    : `${row.adjusted_percentage}%`;

                // Trim and compare case-insensitive
                const isCurrentSchool = row.school?.trim().toLowerCase() === highlightSchoolName.trim().toLowerCase();
                const tr = document.createElement('tr');
                if (isCurrentSchool) tr.classList.add('highlight-school');

                tr.innerHTML = `
                    <td>${i+1}</td>
                    <td>${row.school}</td>
                    <td>${row.A}</td>
                    <td>${row.B}</td>
                    <td>${row.C}</td>
                    <td>${row.S}</td>
                    <td>${row.W}</td>
                    <td>${row.sat}</td>
                    <td>${row.pass}</td>
                    <td>${row.percentage}%</td>
                    <td>${adjustedPass}</td>
                    <td>${row.average}</td>
                    <td>${row.rank}</td>
                `;
                tbody.appendChild(tr);
            });

            table.style.display = 'table';
        })
        .catch(err => {
            console.error(err);
            document.getElementById('loadingSpinner').style.display = 'none';
            alert("Error loading data");
        });
    }

    function loadSubjects(examId) {
        fetch(`{{ route('reports.exam.subjects') }}?exam_id=${examId}`)
        .then(res => res.json())
        .then(subjects => {
            subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';
            subjects.forEach((sub, i) => {
                subjectSelect.innerHTML += `<option value="${sub.id}" ${i === 0 ? 'selected' : ''}>${sub.cadre}</option>`;
            });

            if (subjects.length > 0) {
                subjectSelect.value = subjects[0].id;
                loadData(examId, subjects[0].id);
            }
        });
    }

    examSelect.addEventListener('change', function() {
        if (this.value) loadSubjects(this.value);
    });

    subjectSelect.addEventListener('change', function() {
        loadData();
    });

    if (examSelect.value && subjectSelect.value) {
        loadData(examSelect.value, subjectSelect.value);
    }

    document.getElementById('printBtn').addEventListener('click', function() {
        const container = document.querySelector('.container').cloneNode(true);
        const printWindow = window.open('', '', 'width=1200,height=800');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Subject-wise Analysis</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        table { width: 100%; border-collapse: collapse !important; }
                        th, td { border: 1px solid #000 !important; font-size:14px; font-weight:bold; color:#000; }
                        .table-striped tbody tr:nth-of-type(odd) { background-color: #f2f2f2 !important; }
                        .table-dark th { background-color: #343a40 !important; color: #fff !important; }
                        .highlight-school { background-color: #d4edda; }
                        @media print {
                            body { -webkit-print-color-adjust: exact; }
                            thead { display: table-header-group; }
                            tr { page-break-inside: avoid; page-break-after: auto; }
                        }
                        select { pointer-events: none; border: none; background: transparent; font-weight:bold; }
                        h2 { color: #000 !important; }
                    </style>
                </head>
                <body>${container.innerHTML}</body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    });
});
</script>
@endpush
