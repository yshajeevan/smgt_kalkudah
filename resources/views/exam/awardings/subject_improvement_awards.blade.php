{{-- resources/views/exam/awardings/subject_improvement_awards.blade.php --}}
@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4" style="font-weight:bold; color:#000;">Subject-wise Improvement Awards</h2>
    <p class="text-muted">Awards are based on improvement compared to previous exam (Gold ≥20, Silver ≥10, Bronze ≥5)</p>

    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
        <select id="examSelect" class="form-select" style="width:260px;">
            <option value="">-- Select Exam --</option>
            @foreach($exams as $exam)
                <option value="{{ $exam->id }}">{{ $exam->name }} - {{ $exam->year }}</option>
            @endforeach
        </select>

        <button id="printBtn" class="btn btn-primary"> <i class="bi bi-printer"></i> Print</button>

        <div id="loadingSpinner" style="display:none; margin-left:10px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
        </div>
    </div>

    <div id="resultsContainer"></div>
</div>
@endsection

@push('styles')
<style>
.table-modern {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    width: 100%;
    margin-bottom: 18px;
}
.table-modern thead th {
    background: #007bff;
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    padding: 8px;
    text-align: center;
}
.table-modern tbody td {
    padding: 8px;
    border: 1px solid #ddd;
    font-weight: 600;
    color: #000;
    text-align: center;
}
.table-modern td.student-name { text-align: left; padding-left:12px; }

.badge-award { font-size: 18px; padding:6px 10px; border-radius:6px; color:#fff; font-weight:700; display:inline-block; }
.badge-gold{ background:#d4af37; }   /* gold */
.badge-silver{ background:#c0c0c0; } /* silver */
.badge-bronze{ background:#cd7f32; } /* bronze */
.badge-try{ background:#6c757d; }    /* keep trying */

@media print {
    @page { size: A4 portrait; margin: 15mm; }
    body { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; }
    #examSelect, #printBtn { display: none !important; }
    .table-modern { box-shadow: none; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const examSelect = document.getElementById('examSelect');
    const spinner = document.getElementById('loadingSpinner');
    const resultsContainer = document.getElementById('resultsContainer');

    function showSpinner(v){ spinner.style.display = v ? 'inline-block' : 'none'; }

    examSelect.addEventListener('change', function(){
        const examId = this.value;
        resultsContainer.innerHTML = '';
        if(!examId) return;
        showSpinner(true);

        fetch(`{{ route('reports.subject.improvement.data') }}?exam_id=${examId}`)
            .then(res => res.json())
            .then(data => {
                showSpinner(false);
                if(data.error){
                    resultsContainer.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                    return;
                }
                renderResults(data.winners || []);
            })
            .catch(err => {
                showSpinner(false);
                console.error(err);
                resultsContainer.innerHTML = `<div class="alert alert-danger">Error loading data</div>`;
            });
    });

    function awardBadge(award){
        switch((award||'').toLowerCase()){
            case 'gold': return `<span class="badge-award badge-gold">🥇 Gold</span>`;
            case 'silver': return `<span class="badge-award badge-silver">🥈 Silver</span>`;
            case 'bronze': return `<span class="badge-award badge-bronze">🥉 Bronze</span>`;
            default: return `<span class="badge-award badge-try">💪 Keep Trying</span>`;
        }
    }

    function renderResults(subjects){
        if(!subjects.length){
            resultsContainer.innerHTML = `<div class="alert alert-info">No improvement winners found for selected exam.</div>`;
            return;
        }

        let html = '';
        subjects.forEach(sub => {
            // Ensure stable sorting: Gold -> Silver -> Bronze -> Keep Trying, within same award sort by improvement desc
            const order = { 'gold': 1, 'silver': 2, 'bronze': 3, 'keep trying': 4 };
            sub.winners = (sub.winners || []).sort((a,b)=>{
                const oa = (order[(a.award||'').toLowerCase()]||99);
                const ob = (order[(b.award||'').toLowerCase()]||99);
                if(oa !== ob) return oa - ob;
                return (b.improvement || 0) - (a.improvement || 0);
            });

            html += `<h4 style="margin-top:18px; font-weight:700; color:#000">${sub.subject}</h4>`;
            html += `<table class="table-modern">
                        <thead>
                            <tr>
                                <th style="text-align:left;">Student</th>
                                <th>Previous</th>
                                <th>Current</th>
                                <th>Improvement</th>
                                <th>Award</th>
                            </tr>
                        </thead>
                        <tbody>`;

            if(sub.winners.length === 0){
                html += `<tr><td class="student-name" colspan="5" style="text-align:center;">No qualifying improvements</td></tr>`;
            } else {
                sub.winners.forEach(w => {
                    const prev = (w.previous_mark === null || w.previous_mark === undefined) ? '-' : w.previous_mark;
                    const curr = (w.current_mark === null || w.current_mark === undefined) ? '-' : w.current_mark;
                    const delta = (w.improvement === null || w.improvement === undefined) ? '-' : (w.improvement > 0 ? '+'+Number(w.improvement).toFixed(2) : Number(w.improvement).toFixed(2));
                    html += `<tr>
                                <td class="student-name">${escapeHtml(w.student)}</td>
                                <td>${prev}</td>
                                <td>${curr}</td>
                                <td>${delta}</td>
                                <td>${awardBadge(w.award)}</td>
                            </tr>`;
                });
            }

            html += `</tbody></table>`;
        });

        resultsContainer.innerHTML = html;
    }

    // basic escape
    function escapeHtml(s){
        if(!s && s !== 0) return '';
        return String(s)
            .replaceAll('&','&amp;')
            .replaceAll('<','&lt;')
            .replaceAll('>','&gt;')
            .replaceAll('"','&quot;')
            .replaceAll("'",'&#039;');
    }

    // Print
    document.getElementById('printBtn').addEventListener('click', function(){
        if(!resultsContainer.innerHTML.trim()){
            return alert('No data to print.');
        }

        // Clone content and open print window
        const content = resultsContainer.cloneNode(true);
        const w = window.open('', '', 'width=900,height=700');
        w.document.write(`<html><head><title>Subject Improvement Awards</title>
            <style>
                body{font-family:Arial, sans-serif; color:#000;}
                h4{font-weight:700;}
                table{width:100%; border-collapse:collapse; margin-bottom:18px;}
                th,td{border:1px solid #000; padding:8px; text-align:center; font-weight:600;}
                th:first-child, td:first-child{ text-align:left; padding-left:12px; }
                .badge-award{padding:6px 10px; border-radius:6px; color:#fff; font-weight:700;}
                .badge-gold{background:#d4af37;}
                .badge-silver{background:#c0c0c0;}
                .badge-bronze{background:#cd7f32;}
                .badge-try{background:#6c757d;}
            </style>
        </head><body></body></html>`);
        w.document.body.appendChild(content);
        w.document.close();
        w.focus();
        w.print();
        w.close();
    });
});
</script>
@endpush
