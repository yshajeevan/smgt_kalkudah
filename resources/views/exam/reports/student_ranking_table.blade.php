@extends('layouts.master')

@section('main-content')
<div class="p-4">
    <h2 class="mb-4" style="margin-bottom:30px; font-weight:bold; color:#000;">Student Ranking Report</h2>

    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
        <select id="examSelect" class="form-select" style="width:260px;">
            <option value="">-- Select Exam --</option>
            @foreach($exams as $exam)
                <option value="{{ $exam->id }}" {{ $loop->last ? 'selected' : '' }}>
                    {{ $exam->name }} - {{ $exam->year }}
                </option>
            @endforeach
        </select>
        <div class="mb-3">
            <button id="printBtn" class="btn btn-primary">Print</button>
        </div>

        <div id="loadingSpinner" style="display:none; margin-left:10px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
        </div>
    </div>

    <table class="table table-bordered table-striped" id="studentRankingTable">
        <thead>
            <tr id="tableHeader">
                <th>StuID</th>
                <th>StuName</th>
                <!-- Subject headers injected dynamically -->
                <th>Attendance %</th> <!-- New -->
                <th>Pass/Fail</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@push('styles')
<style>
/* Initially collapsed filter input */
.filter-select .select2-selection__rendered {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Optional: increase dropdown width on open */
.select2-dropdown {
    width: auto !important;
    min-width: 150px;
}

/* Hide filter row on print */
@media print {
    #filterRow { display: none !important; }
}


#studentRankingTable th,
#studentRankingTable td {
    font-size: 16px;       /* change font size as needed */
    font-weight: bold;     /* bold text */
    color: #000;           /* black text */
}

/* Keep vertical subject headers if needed */
.subjectHeader {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    text-align: left;
    vertical-align: bottom;
    padding: 2px 4px;
    height: 140px;
    min-width: 30px;
    font-size: 12px;  /* subject header can remain smaller */
    white-space: normal;
    word-break: break-word;
    line-height: 1.2;
}
</style>
@endpush

@push('scripts')
<script>
let dataTable;

document.getElementById('examSelect').addEventListener('change', function () {
    const examId = this.value;
    if (!examId) return;

    document.getElementById('loadingSpinner').style.display = 'inline-block';

    fetch(`{{ route('reports.student.ranks.table.data') }}?exam_id=${examId}`)
        .then(res => res.json())
        .then(res => {
            document.getElementById('loadingSpinner').style.display = 'none';
            const tbody = document.querySelector('#studentRankingTable tbody');
            const thead = document.querySelector('#tableHeader');

            // Remove old subject headers
            thead.querySelectorAll('.subjectHeader').forEach(th => th.remove());

            // Insert subject headers dynamically
            let totalCol = thead.querySelector('th:nth-child(3)'); // "Total" column (after StuName + subjects)
            res.subjects.forEach(sub => {
                const th = document.createElement('th');
                th.textContent = sub.name;
                th.classList.add('subjectHeader');
                thead.insertBefore(th, totalCol);
            });

            tbody.innerHTML = '';

            // Populate rows
            res.students.forEach(stu => {
                const tr = document.createElement('tr');
                let subjCells = '';

                res.subjects.forEach(sub => {
                    let val = stu.subjects[sub.id] ?? 'NaN';
                    subjCells += `<td>${val}</td>`;
                });

                // Attendance % cell
                const attendanceCell = `<td>${stu.attendance}%</td>`;

                // Pass/Fail badge
                const passFailBadge = stu.pass_fail === 'Fail'
                    ? `<span class="badge bg-danger" style="font-size:14px; color:#fff;">${stu.pass_fail}</span>`
                    : `<span class="badge bg-success" style="font-size:14px; color:#fff;">${stu.pass_fail}</span>`;

                // Build row
                tr.innerHTML = `
                    <td>${stu.id}</td>
                    <td>${stu.name}</td>
                    ${subjCells}
                    ${attendanceCell}  <!-- Use AFTER declaration -->
                    <td>${passFailBadge}</td>
                `;
                tbody.appendChild(tr);
            });


            // Destroy previous DataTable
            if (dataTable) {
                dataTable.destroy();
            }

            // Add filter row
            const filterRow = $('<tr id="filterRow"></tr>');
            $('#studentRankingTable thead tr#tableHeader th').each(function () {
                filterRow.append('<th></th>');
            });
            $('#studentRankingTable thead').append(filterRow);

            dataTable = $('#studentRankingTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                autoWidth: false,
                initComplete: function () {
                    let api = this.api();
                    const startCol = 2;
                    const endCol = 2 + res.subjects.length;
                    const lastCol = api.columns().count() - 1; // Pass/Fail column index

                    api.columns().every(function (colIdx) {
                        let column = this;

                        // Subject columns: multi-select filter
                        if (colIdx >= startCol && colIdx < endCol) {
                            let select = $('<select multiple="multiple" style="width:100%"></select>')
                                .appendTo($('#filterRow th').eq(colIdx).empty())
                                .on('change', function () {
                                    let vals = $(this).val();
                                    column.search(vals && vals.length ? vals.join('|') : '', true, false).draw();
                                });

                            column.data().unique().sort().each(d => select.append('<option value="' + d + '">' + d + '</option>'));
                            select.select2({ placeholder: "Filter", allowClear: true, width: '100%' });
                        }


                        // Pass/Fail column: single-select filter
                        if (colIdx === lastCol) {
                            let select = $('<select style="width:100%"><option value="">All</option></select>')
                                .appendTo($('#filterRow th').eq(colIdx).empty())
                                .on('change', function () {
                                    column.search(this.value).draw();
                                });

                            // Extract plain text (strip HTML) for unique values
                            column.data().unique().sort().each(d => {
                                // Strip HTML tags
                                let txt = $('<div>').html(d).text().trim();
                                if(txt) select.append('<option value="' + txt + '">' + txt + '</option>');
                            });

                            select.select2({ placeholder: "Filter", allowClear: true, width: 'resolve' });
                        }
                    });

                    // Student name search
                    $('#studentSearch').on('keyup', function () {
                        dataTable.column(1).search(this.value).draw();
                    });
                }
            });
        })
        .catch(err => {
            document.getElementById('loadingSpinner').style.display = 'none';
            console.error(err);
            alert("Error loading data");
        });
});

window.addEventListener('DOMContentLoaded', () => {
    const examSelect = document.getElementById('examSelect');
    if (examSelect.value) {
        examSelect.dispatchEvent(new Event('change'));
    }
});

document.getElementById('printBtn').addEventListener('click', function() {
    const container = document.querySelector('.container').cloneNode(true);

    const printWindow = window.open('', '', 'width=1200,height=800');
    printWindow.document.write(`
        <html>
            <head>
                <title>Student Ranking Report</title>
                <!-- Bootstrap CSS for table styling -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse !important; }
                    th, td { border: 1px solid #000 !important; }
                    .table-striped tbody tr:nth-of-type(odd) { background-color: #f2f2f2 !important; }
                    .badge { font-size: 14px !important; color: #fff !important; }
                    select {
                        pointer-events: none;
                        border: none;
                        background: transparent;
                        font-weight: bold;
                    }
                    h2 { color: #000 !important; }
                    @media print {
                        body { -webkit-print-color-adjust: exact; }
                        thead { display: table-header-group; }
                        tr { page-break-inside: avoid; page-break-after: auto; }
                        @page { size: landscape; margin: 15mm; }
                    }
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

</script>
@endpush
