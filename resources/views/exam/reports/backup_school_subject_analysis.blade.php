@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4" style="font-weight:bold; color:#000;">Subject-wise Analysis</h2>

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
                <th>Average</th>
                <th>Rank</th>
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
    const subjectSelect = document.getElementById('subjectSelect');
    const table = document.getElementById('resultTable');
    const tbody = table.querySelector('tbody');

    function loadData(examId = examSelect.value, subjectId = subjectSelect.value) {
        if (!examId || !subjectId) return;

        document.getElementById('loadingSpinner').style.display = 'block';
        table.style.display = 'none';

        fetch(`{{ route('reports.school.subject.analysis.data') }}?exam_id=${examId}&subject_id=${subjectId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('loadingSpinner').style.display = 'none';
            tbody.innerHTML = '';

            data.forEach((row, i) => {
                tbody.innerHTML += `
                    <tr style="font-size:16px; font-weight:bold;">
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
                        <td>${row.average}</td>
                        <td>${row.rank}</td>
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

    function loadSubjects(examId) {
        fetch(`{{ route('reports.exam.subjects') }}?exam_id=${examId}`)
        .then(res => res.json())
        .then(subjects => {
            subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';

            subjects.forEach((sub, i) => {
                subjectSelect.innerHTML += `
                    <option value="${sub.id}" ${i === 0 ? 'selected' : ''}>
                        ${sub.cadre}
                    </option>
                `;
            });

            // auto-load first subject if exists
            if (subjects.length > 0) {
                subjectSelect.value = subjects[0].id;
                loadData(examId, subjects[0].id);
            }
        });
    }

    // when exam changes → reload subjects (and table auto refreshes)
    examSelect.addEventListener('change', function() {
        if (this.value) {
            loadSubjects(this.value);
        }
    });

    // when subject changes → reload table
    subjectSelect.addEventListener('change', function() {
        loadData();
    });

    // 🔥 Initial load (latest exam + default subject already selected by backend)
    const initialExam = examSelect.value;
    const initialSubject = subjectSelect.value;
    if (initialExam && initialSubject) {
        loadData(initialExam, initialSubject);
    }
});
</script>
@endpush
