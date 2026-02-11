@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4" style="font-weight:bold; color:#000;">Subject-wise Medal Winners</h2>

    <!-- Exam Dropdown -->
    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
        <select id="examSelect" class="form-select" style="width:260px;">
            <option value="">-- Select Exam --</option>
            @foreach($exams as $exam)
                <option value="{{ $exam->id }}">{{ $exam->name }} - {{ $exam->year }}</option>
            @endforeach
        </select>
        <div id="loadingSpinner" style="display:none; margin-left:10px;">
            <span class="spinner-border spinner-border-sm"></span> Loading...
        </div>
    </div>

    <!-- Print Button -->
    <button id="printBtn" class="btn btn-primary">
        <i class="bi bi-printer"></i> Print
    </button>

    <div id="loadingSpinner" style="display:none; margin-left:10px;">
        <span class="spinner-border spinner-border-sm"></span> Loading...
    </div>


    <!-- Results -->
    <div id="winnersContainer"></div>
</div>

@push('styles')
<style>
@media print {
    @page { size: A4 portrait; margin: 15mm; }
    body { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; }
    #examSelect, #printBtn { display: none !important; }

    /* Remove fixed heights/scrolling for print */
    #winnersContainer {
        height: auto !important;
        overflow: visible !important;
    }

    /* Avoid breaking table rows */
    table { page-break-inside: auto; }
    tr    { page-break-inside: avoid; page-break-after: auto; }
}

.table-modern {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.table-modern th {
    background: #007bff;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
}
.table-modern td {
    font-size: 16px;
    font-weight: bold;
    color: #000;
    text-align: center;
}
.table-modern td.student-name {
    text-align: left; /* left align student name */
    padding-left: 12px;
}
</style>
@endpush

@push('scripts')
<script>
$('#examSelect').on('change', function() {
    const examId = $(this).val();
    if (!examId) return;

    $('#loadingSpinner').show();
    $('#winnersContainer').empty();

    fetch(`{{ route('reports.subject.medal.winners.data') }}?exam_id=${examId}`)
    .then(r => r.json())
    .then(data => {
        $('#loadingSpinner').hide();
        if (data.error) {
            $('#winnersContainer').html(`<div class="alert alert-danger">${data.error}</div>`);
            return;
        }

        let html = '';
        data.winners.forEach(sub => {
            // Sort winners: Gold → Silver → Bronze
            const order = { 'Gold': 1, 'Silver': 2, 'Bronze': 3 };
            sub.winners.sort((a, b) => (order[a.medal] || 4) - (order[b.medal] || 4));

            html += `
                <h4 style="margin-top:20px; font-weight:bold; color:#000;">${sub.subject}</h4>
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Marks</th>
                            <th>Medal</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            sub.winners.forEach(w => {
                let icon = w.medal === 'Gold' ? '🥇 (Gold)' :
                        w.medal === 'Silver' ? '🥈 (Silver)' :
                        '🥉 (Bronze)';
                html += `
                    <tr>
                        <td class="student-name">${w.student}</td>
                        <td>${w.mark}</td>
                        <td style="font-size:24px;">${icon}</td>
                    </tr>
                `;
            });

            html += `</tbody></table>`;
        });

        $('#winnersContainer').html(html);
    })
    .catch(err => {
        $('#loadingSpinner').hide();
        console.error(err);
        $('#winnersContainer').html(`<div class="alert alert-danger">Error loading data</div>`);
    });
});

// Print button
$('#printBtn').on('click', function() {
    // Clone the content
    const content = document.getElementById('winnersContainer').cloneNode(true);

    // Open a new window for printing
    const printWindow = window.open('', '', 'width=900,height=650');
    printWindow.document.write(`
        <html>
        <head>
            <title>Print Medal Winners</title>
            <style>
                body { font-family: Arial, sans-serif; color: #000; font-size: 16px; font-weight: bold; }
                h4 { font-weight: bold; color: #000; margin-top: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body></body>
        </html>
    `);

    printWindow.document.body.appendChild(content);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
});
</script>
@endpush
@endsection
