@extends('layouts.master')

@section('main-content')
<div class="p-4">
    <h2 class="mb-4" style="margin-bottom:30px; font-weight:bold; color:#000;">Student Marks Report</h2>

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

    <table class="table table-bordered table-striped" id="studentMarksTable">
        <thead>
            <tr id="tableHeader">
                <th>StuID</th>
                <th>StuName</th>
                <!-- Subject headers will be injected here -->
                <th>Total</th>
                <th>Average</th>
                <th>Attendance %</th>
                <th>Rank</th>
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

#studentMarksTable th,
#studentMarksTable td {
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

.average-cell {
    position: relative;
    font-weight: bold;
    color: #000;
    text-align: left;
    padding-left: 6px;
}
.average-cell span {
    position: relative;
    z-index: 2;
}
.average-bar {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1;
    border-radius: 4px;
    opacity: 0.7;
}

</style>
@endpush

@push('scripts')
<script>
let dataTable;

function applyAverageGradient() {
    $('#studentMarksTable tbody tr').each(function () {
        const td = $(this).find('td').eq(-3); // Average column (now before Attendance)
        const avg = parseFloat(td.text());
        if (!isNaN(avg)) {
            td.empty();

            // Width as percentage of average (0-100)
            const percentWidth = avg; // since average is already 0-100

            // Determine bar color: green for high, red for low (vertical gradient)
            const barColor = avg >= 50 
                ? 'linear-gradient(to top, darkgreen, lightgreen)'
                : 'linear-gradient(to top, darkred, lightcoral)';

            const bar = $('<div class="average-bar"></div>').css({
                width: percentWidth + '%',
                height: '100%',
                background: barColor,
                borderRadius: '4px'
            });

            td.append(bar).append(`<span>${avg}</span>`).addClass('average-cell');
        }
    });
}


document.getElementById('examSelect').addEventListener('change', function () {
    const examId = this.value;
    if (!examId) return;

    document.getElementById('loadingSpinner').style.display = 'inline-block';

    fetch(`{{ route('reports.student.marks.table.data') }}?exam_id=${examId}`)
        .then(res => res.json())
        .then(res => {
            document.getElementById('loadingSpinner').style.display = 'none';
            const tbody = document.querySelector('#studentMarksTable tbody');
            const thead = document.querySelector('#tableHeader');

            // Remove old subject headers
            thead.querySelectorAll('.subjectHeader').forEach(th => th.remove());

            // Insert new subject headers before "Total"
            const totalIndex = Array.from(thead.children).findIndex(th => th.textContent === "Total");

            let totalCol = thead.querySelector('th:nth-child(3)'); // "Total" column (after StuName + subjects)
            res.subjects.forEach(sub => {
                const th = document.createElement('th');
                th.textContent = sub.name;
                th.classList.add('subjectHeader');
                thead.insertBefore(th, totalCol);
            });

            // Fill rows
            tbody.innerHTML = '';
            res.students.forEach(stu => {
                const tr = document.createElement('tr');
                let subjCells = '';

                res.subjects.forEach(sub => {
                    let val = stu.subjects[String(sub.id)] ?? ''; // safe lookup
                    subjCells += `<td>${val}</td>`;
                });

                tr.innerHTML = `
                    <td>${stu.id}</td>
                    <td>${stu.name}</td>
                    ${subjCells}
                    <td>${stu.total}</td>
                    <td>${stu.average}</td>
                    <td>${stu.attendance}%</td> <!-- 👈 New -->
                    <td>${stu.rank}</td>
                `;
                tbody.appendChild(tr);
            });

            // Reset DataTable
            if (dataTable) dataTable.destroy();

            const filterRow = $('<tr id="filterRow"></tr>');
            $('#studentMarksTable thead tr#tableHeader th').each(function () {
                filterRow.append('<th></th>');
            });
            $('#studentMarksTable thead').append(filterRow);

            dataTable = $('#studentMarksTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                autoWidth: false,
                initComplete: function () {
                    let api = this.api();
                    const startCol = 2;
                    const endCol = 2 + res.subjects.length;

                    api.columns().every(function (colIdx) {
                        if (colIdx >= startCol && colIdx < endCol) {
                            let column = this;
                            let select = $('<select multiple="multiple" style="width:100%"></select>')
                                .appendTo($('#filterRow th').eq(colIdx).empty())
                                .on('change', function () {
                                    let vals = $(this).val();
                                    if (vals && vals.length > 0) {
                                        column.search(vals.join('|'), true, false).draw();
                                    } else {
                                        column.search('', true, false).draw();
                                    }
                                });

                            column.data().unique().sort().each(function (d) {
                                select.append('<option value="' + d + '">' + d + '</option>');
                            });

                            select.select2({
                                placeholder: "Filter",
                                allowClear: true,
                                width: 'resolve'
                            });
                        }
                    });

                    // 🔥 Apply gradient after DataTable draws
                    applyAverageGradient();
                }
            });

            // 🔥 Reapply gradient on page change / filter
            $('#studentMarksTable').on('draw.dt', function () {
                applyAverageGradient();
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
