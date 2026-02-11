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
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const examSelect = document.getElementById('examSelect');
    const table = document.getElementById('resultTable');
    const tbody = table.querySelector('tbody');

    function loadData(examId = examSelect.value) {
        if (!examId) return;

        document.getElementById('loadingSpinner').style.display = 'block';
        table.style.display = 'none';

        fetch(`{{ route('reports.school.overall.analysis.data') }}?exam_id=${examId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('loadingSpinner').style.display = 'none';
            tbody.innerHTML = '';

            data.forEach((row, i) => {
                tbody.innerHTML += `
                    <tr style="font-size:16px; font-weight:bold;">
                        <td>${row.rank}</td>
                        <td>${row.school}</td>
                        <td>${row.total}</td>
                        <td>${row.pass}</td>
                        <td>${row.fail}</td>
                        <td>${row.percentage}%</td>
                    </tr>
                `;
            });

            table.style.display = 'table';
        })
        .catch(err => {
            console.error(err);
            document.getElementById('loadingSpinner').style.display = 'none';
            alert("Error loading data");
        });
    }

    // when exam changes → reload table
    examSelect.addEventListener('change', function() {
        loadData(this.value);
    });

    // 🔥 Initial load
    const initialExam = examSelect.value;
    if (initialExam) {
        loadData(initialExam);
    }
});
</script>
@endpush
