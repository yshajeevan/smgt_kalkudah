@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4 fw-bold text-dark">Manage Grade</h2>

    <div class="row">
        {{-- Left side form --}}
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="alertBox" class="alert alert-success d-none"></div>

                    <form id="competencyForm">
                        @csrf
                        <input type="hidden" name="id" id="competency_id">

                        <div class="row g-3">
                            {{-- Subject --}}
                            <div class="col-12">
                                <label class="form-label">Subject</label>
                                <select name="subject_id" id="subject_id" class="form-select" required>
                                    <option value="">-- Select Subject --</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->cadre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Grade --}}
                            <div class="col-12">
                                <label class="form-label">Grade</label>
                                <input type="text" name="name" id="name" class="form-control" required>
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
                <div class="card-header fw-bold">Grade List</div>
                <div class="card-body p-0">
                    <div style="max-height: 500px; overflow-y:auto;">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Subject</th>
                                    <th>Grade</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody id="competencyTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pass permission info to JS --}}
<script>
    const canManageResults = @json(auth()->user()->hasRole('results-manage'));
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("competencyForm");
    const saveBtn = document.getElementById("saveBtn");
    const resetBtn = document.getElementById("resetBtn");
    const tableBody = document.getElementById("competencyTableBody");
    const subjectSelect = document.getElementById("subject_id");
    const alertBox = document.getElementById("alertBox");

    function showAlert(message) {
        alertBox.textContent = message;
        alertBox.classList.remove("d-none");
        setTimeout(() => alertBox.classList.add("d-none"), 3000);
    }

    // Load competencies for selected subject
    function loadCompetencies(subjectId = null) {
        let url = subjectId ? `/competencies/by-subject/${subjectId}` : "{{ route('competencies.index') }}";
        fetch(url)
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = '';
                data.forEach(c => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${c.id}</td>
                        <td>${c.subject ? c.subject.cadre : '-'}</td>
                        <td>${c.name}</td>
                        <td>
                            ${canManageResults ? `<button class="btn btn-sm btn-warning editBtn" data-competency='${JSON.stringify(c)}'>Edit</button>` : '-'}
                        </td>
                        <td>
                            ${canManageResults ? `<button class="btn btn-sm btn-danger deleteBtn" data-id="${c.id}">Delete</button>` : '-'}
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                attachEditDeleteHandlers();
            })
            .catch(err => console.error(err));
    }

    // Initial load
    loadCompetencies();

    // Reload table when subject changes
    subjectSelect.addEventListener('change', function() {
        loadCompetencies(this.value);
    });

    // AJAX submit (Add / Update)
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!canManageResults) return showAlert("You do not have permission to perform this action.");

        saveBtn.disabled = true;

        const id = document.getElementById("competency_id").value;
        const url = id ? `/competencies/${id}` : "{{ route('competencies.store') }}";
        const formData = new FormData(form);

        if (id) formData.append('_method', 'PUT'); // Laravel override

        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            if(resp.success) {
                showAlert(id ? "Grade updated!" : "Grade added!");
                loadCompetencies(subjectSelect.value); // reload table

                // Reset form fields, keep subject selected
                document.getElementById("competency_id").value = '';
                document.getElementById("name").value = '';
                saveBtn.textContent = "Save";
            } else if(resp.error) {
                showAlert(resp.error);
            }
        })
        .catch(err => console.error(err))
        .finally(() => saveBtn.disabled = false);
    });

    // Edit & Delete handlers
    function attachEditDeleteHandlers() {
        if (!canManageResults) return;

        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                const data = JSON.parse(this.dataset.competency);
                document.getElementById("competency_id").value = data.id;
                document.getElementById("subject_id").value = data.subject_id;
                document.getElementById("name").value = data.name;
                saveBtn.textContent = "Update";
            });
        });

        document.querySelectorAll('.deleteBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                if(confirm('Are you sure to delete this grade?')) {
                    fetch(`/competencies/${this.dataset.id}`, {
                        method: 'DELETE',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                    })
                    .then(res => res.json())
                    .then(resp => {
                        if(resp.success) {
                            showAlert("Grade deleted");
                            loadCompetencies(subjectSelect.value);
                        }
                    });
                }
            });
        });
    }

    // Reset button
    resetBtn.addEventListener("click", function() {
        document.getElementById("competency_id").value = "";
        saveBtn.textContent = "Save";
    });
});
</script>
@endsection
