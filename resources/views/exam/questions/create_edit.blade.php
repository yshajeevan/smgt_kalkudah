@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4 fw-bold text-dark">Manage Questions</h2>

    <div class="row">
        {{-- Left side form --}}
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    {{-- Success alert --}}
                    <div id="alertBox" class="alert alert-success d-none"></div>

                    <form id="questionForm">
                        @csrf
                        <input type="hidden" name="id" id="question_id">

                        <div class="row g-3">
                            {{-- Exam --}}
                            <div class="col-12">
                                <label class="form-label">Exam</label>
                                <select name="exam_id" id="exam_id" class="form-select" required>
                                    <option value="">-- Select Exam --</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Subject --}}
                            <div class="col-12">
                                <label class="form-label">Subject</label>
                                <select name="subject_id" id="subject_id" class="form-select" required>
                                    <option value="">-- Select Subject --</option>
                                </select>
                            </div>

                            {{-- Syllabus Unit --}}
                            <div class="col-12">
                                <label class="form-label">Syllabus Unit</label>
                                <select name="syllabus_unit_id" id="syllabus_unit_id" class="form-select" required>
                                    <option value="">-- Select Unit --</option>
                                </select>
                            </div>

                            {{-- Question No --}}
                            <div class="col-6">
                                <label class="form-label">Question No</label>
                                <input type="number" name="question_no" id="question_no" class="form-control" required>
                            </div>

                            {{-- Type --}}
                            <div class="col-6">
                                <label class="form-label">Type</label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    <option value="MCQ">MCQ</option>
                                    <option value="SAQ">SAQ</option>
                                </select>
                            </div>

                            {{-- Max Marks --}}
                            <div class="col-6">
                                <label class="form-label">Max Marks</label>
                                <input type="number" name="max_marks" id="max_marks" class="form-control" required>
                            </div>
                        </div>
                        @can('results-manage')
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4" id="saveBtn">Save</button>
                            <button type="reset" class="btn btn-secondary px-4" id="resetBtn">Reset</button>
                        </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>

        {{-- Right side table --}}
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Questions List</div>
                <div class="card-body p-0">
                    <div style="max-height: 500px; overflow-y:auto;">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Q. No</th>
                                    <th>Unit</th>
                                    <th>Type</th>
                                    <th>Marks</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody id="questionsTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JS --}}
{{-- Pass permission info to JS --}}
<script>
    const canManageResults = @json(auth()->user()->hasRole('results-manage'));
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const examSelect = document.getElementById('exam_id');
    const subjectSelect = document.getElementById('subject_id');
    const unitSelect = document.getElementById('syllabus_unit_id');
    const tableBody = document.getElementById('questionsTableBody');
    const form = document.getElementById("questionForm");
    const saveBtn = document.getElementById("saveBtn");
    const resetBtn = document.getElementById("resetBtn");
    const alertBox = document.getElementById("alertBox");

    // Show alert
    function showAlert(message) {
        alertBox.textContent = message;
        alertBox.classList.remove("d-none");
        setTimeout(() => alertBox.classList.add("d-none"), 3000);
    }

    // Load subjects for selected exam
    examSelect.addEventListener('change', function() {
        const examId = this.value;
        subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';
        unitSelect.innerHTML = '<option value="">-- Select Unit --</option>';
        tableBody.innerHTML = '';

        if (!examId) return;

        fetch("{{ route('questions.getSubjects') }}?exam_id=" + examId)
            .then(res => res.json())
            .then(data => {
                data.forEach(s => {
                    subjectSelect.innerHTML += `<option value="${s.id}">${s.cadre}</option>`;
                });
            });
    });

    // Load units and questions when subject changes
    subjectSelect.addEventListener('change', function() {
        const subjectId = this.value;
        unitSelect.innerHTML = '<option value="">-- Select Unit --</option>';
        tableBody.innerHTML = '';
        if (!subjectId) return;

        // Units
        fetch(`/questions/get-units/${subjectId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(u => unitSelect.innerHTML += `<option value="${u.id}">${u.name}</option>`);
            });

        loadQuestions(subjectId);
    });

    // Function to reload questions
    function loadQuestions(subjectId) {
        fetch(`/questions/by-subject/${subjectId}`)
            .then(res => res.json())
            .then(questions => {
                tableBody.innerHTML = "";
                questions.forEach(q => {
                    const unitTitle = q.syllabus_unit ? q.syllabus_unit.name : '-';
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${q.question_no}</td>
                        <td>${unitTitle}</td>
                        <td>${q.type}</td>
                        <td>${q.max_marks}</td>
                        <td>
                            ${canManageResults ? `<button class="btn btn-sm btn-warning editBtn" data-question='${JSON.stringify(c)}'>Edit</button>` : '-'}
                        </td>
                        <td>
                            ${canManageResults ? `<button class="btn btn-sm btn-danger deleteBtn" data-id="${c.id}">Delete</button>` : '-'}
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                attachEditDeleteHandlers();
            });
    }

    // Submit form via AJAX
    form.addEventListener("submit", function(e) {
        e.preventDefault();
        saveBtn.disabled = true; // disable to prevent double click
        const formData = new FormData(form);

        fetch("{{ route('questions.store') }}", {
            method: "POST",
            headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                const subjectId = subjectSelect.value;
                if (subjectId) loadQuestions(subjectId);

                // Reset only question fields
                document.getElementById("question_id").value = "";
                document.getElementById("question_no").value = "";
                saveBtn.textContent = "Save";

                showAlert(resp.message);
            }
        })
        .catch(err => alert(err.message))
        .finally(() => {
            saveBtn.disabled = false; // re-enable button
        });
    });


    // Edit & Delete handlers
    function attachEditDeleteHandlers() {
        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                const data = JSON.parse(this.dataset.question);
                document.getElementById("question_id").value = data.id;
                document.getElementById("exam_id").value = data.exam_id;
                document.getElementById("subject_id").value = data.subject_id;
                document.getElementById("syllabus_unit_id").value = data.syllabus_unit_id;
                document.getElementById("question_no").value = data.question_no;
                document.getElementById("type").value = data.type;
                document.getElementById("max_marks").value = data.max_marks;
                saveBtn.textContent = "Update";
            });
        });

        document.querySelectorAll('.deleteBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                if(confirm('Are you sure to delete this question?')) {
                    fetch(`/questions/${this.dataset.id}`, {
                        method: 'DELETE',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                    }).then(res => res.json())
                    .then(resp => {
                        if(resp.success) {
                            this.closest('tr').remove();
                            showAlert("Question deleted");
                        }
                    });
                }
            });
        });
    }

    // Reset button
    resetBtn.addEventListener("click", function() {
        document.getElementById("question_id").value = "";
        saveBtn.textContent = "Save";
    });
});
</script>
@endsection
