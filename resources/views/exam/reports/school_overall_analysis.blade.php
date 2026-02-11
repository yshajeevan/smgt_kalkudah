@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4 fw-bold text-center">Overall School Ranking</h2>

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
    </div>

    <div class="mb-3">
        <button id="printBtn" class="btn btn-primary">Print</button>
    </div>

    <div id="loadingSpinner" style="display:none; margin-bottom:15px;">
        <span class="spinner-border spinner-border-sm"></span> Loading...
    </div>

    <table class="table table-bordered table-striped" id="resultTable" style="display:none;">
        <thead class="table-dark">
            <tr>
                <th>Rank</th>
                <th>School</th>
                <th>Total Students</th>
                <th>Pass</th>
                <th>Fail</th>
                <th>Pass %</th>
                <th>Adjusted Pass %</th>
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
    color: #fff; /* white headers */
    background-color: #343a40;
}
#resultTable td {
    font-size: 16px;
    font-weight: bold;
    color: #000; /* black data */
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
    const table = document.getElementById('resultTable');
    const tbody = table.querySelector('tbody');
    const currentSchoolId = {{ $currentSchoolId ?? 'null' }};

    function loadData(examId = examSelect.value) {
        if (!examId) return;

        document.getElementById('loadingSpinner').style.display = 'block';
        table.style.display = 'none';

        fetch(`{{ route('reports.school.overall.analysis.data') }}?exam_id=${examId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('loadingSpinner').style.display = 'none';
            tbody.innerHTML = '';

            // Sort by adjusted pass % if needed
            data.sort((a,b) => b.adjusted_percentage - a.adjusted_percentage);


            const highlightSchoolName = "{{ $currentSchoolName }}";

            data.forEach((row) => {
                const isCurrentSchool = row.school?.trim().toLowerCase() === highlightSchoolName.trim().toLowerCase();
                const tr = document.createElement('tr');
                if (isCurrentSchool) tr.classList.add('highlight-school');

                tr.innerHTML = `
                    <td>${row.rank}</td>
                    <td>${row.school}</td>
                    <td>${row.total}</td>
                    <td>${row.pass}</td>
                    <td>${row.fail}</td>
                    <td>${row.percentage}%</td>
                    <td>${row.adjusted_percentage}%</td>
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

    examSelect.addEventListener('change', function() {
        loadData(this.value);
    });

    if (examSelect.value) loadData(examSelect.value);

    document.getElementById('printBtn').addEventListener('click', function() {
        const container = document.querySelector('.container').cloneNode(true);

        const printWindow = window.open('', '', 'width=1200,height=800');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Overall School Ranking</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        table { width: 100%; border-collapse: collapse !important; }
                        th, td { border: 1px solid #000 !important; }
                        #resultTable th { font-size:14px; font-weight:bold; color:#fff; background-color:#343a40; }
                        #resultTable td { font-size:14px; font-weight:bold; color:#000; }
                        .highlight-school { background-color: #d4edda !important; }
                        .table-striped tbody tr:nth-of-type(odd) { background-color: #f2f2f2 !important; }
                        @media print {
                            body { -webkit-print-color-adjust: exact; }
                            thead { display: table-header-group; }
                            tr { page-break-inside: avoid; page-break-after: auto; }
                        }
                        select { pointer-events: none; border: none; background: transparent; font-weight: bold; }
                        h2 { color: #000 !important; }
                    </style>
                </head>
                <body>
                    ${container.innerHTML}
                </body>
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
