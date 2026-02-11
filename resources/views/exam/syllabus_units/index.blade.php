@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4 fw-bold text-dark">Manage Syllabus</h2>

    <div class="row">
        {{-- Left Form --}}
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div id="alertBox" class="alert alert-success d-none"></div>
                    <form id="unitForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="unit_id">

                        <div class="row g-3">
                            {{-- Subject --}}
                            <div class="col-12">
                                <label class="form-label">Subject</label>
                                <select id="subject_id" class="form-select" required>
                                    <option value="">-- Select Subject --</option>
                                    @foreach($subjects as $s)
                                        <option value="{{ $s->id }}">{{ $s->cadre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Competency --}}
                            <div class="col-12">
                                <label class="form-label">Grade</label>
                                <select name="competency_id" id="competency_id" class="form-select" required>
                                    <option value="">-- Select Grade --</option>
                                </select>
                            </div>

                            {{-- Name --}}
                            <div class="col-12">
                                <label class="form-label">Unit Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>

                            {{-- Code --}}
                            <div class="col-6">
                                <label class="form-label">Code</label>
                                <input type="text" name="code" id="code" class="form-control">
                            </div>

                            {{-- Worksheet (PDF) --}}
                            @can('results-manage')
                            <div class="col-12">
                                <label class="form-label">Upload Worksheet (PDF)</label>
                                <input type="file" name="worksheet" id="worksheet" class="form-control" accept="application/pdf">
                            </div>
                            @endcan
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

        {{-- Right Table --}}
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Units List</div>
                <div class="card-body p-0">
                    <div style="max-height: 500px; overflow-y:auto;">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Grade</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Worksheet</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody id="unitTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Permission info --}}
<script>
    const canManageResults = @json(auth()->user()->hasRole('results-manage'));
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("unitForm");
    const saveBtn = document.getElementById("saveBtn");
    const resetBtn = document.getElementById("resetBtn");
    const tableBody = document.getElementById("unitTableBody");
    const subjectSelect = document.getElementById("subject_id");
    const competencySelect = document.getElementById("competency_id");
    const alertBox = document.getElementById("alertBox");

    function showAlert(message) {
        alertBox.textContent = message;
        alertBox.classList.remove("d-none");
        setTimeout(() => alertBox.classList.add("d-none"), 3000);
    }

    function loadUnits(competencyId = null) {
        if(!competencyId) {
            tableBody.innerHTML = '';
            return;
        }
        fetch(`/syllabus-units/by-competency/${competencyId}`)
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = '';
                data.forEach(u => {
                    const worksheetHtml = u.worksheet 
                        ? `<a href="/${u.worksheet}" target="_blank">
                            <img src="/images/pdf-icon.png" alt="PDF" width="30" title="View PDF">
                        </a>
                        <a href="/${u.worksheet}" download class="ms-2 text-primary fw-bold">Download</a>`
                        : `<span class="text-muted">No file</span>`;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${u.id}</td>
                        <td>${u.competency ? u.competency.name : '-'}</td>
                        <td>${u.name}</td>
                        <td>${u.code ?? '-'}</td>
                        <td>${worksheetHtml}</td>
                        <td>
                            ${canManageResults ? `<button class="btn btn-sm btn-warning editBtn" data-unit='${JSON.stringify(u)}'>Edit</button>` : '-'}
                        </td>
                        <td>
                            ${canManageResults ? `<button class="btn btn-sm btn-danger deleteBtn" data-id="${u.id}">Delete</button>` : '-'}
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                attachEditDeleteHandlers();
            });
    }

    // Subject → load competencies
    subjectSelect.addEventListener('change', function() {
        competencySelect.innerHTML = '<option value="">-- Select Grade --</option>';
        tableBody.innerHTML = '';

        if(!this.value) return;

        fetch(`/competencies/by-subject/${this.value}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(c => {
                    let opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.name;
                    competencySelect.appendChild(opt);
                });
            });
    });

    // Competency → load units
    competencySelect.addEventListener('change', function() {
        loadUnits(this.value);
    });

    // Add / Update Unit
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        saveBtn.disabled = true;

        const id = document.getElementById("unit_id").value;
        const url = id ? `/syllabus-units/${id}` : "{{ route('syllabus_units.store') }}";
        const formData = new FormData(form);
        if(id) formData.append('_method', 'PUT');

        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(res => res.json())
        .then(resp => {
            if(resp.success) {
                showAlert(id ? "Unit updated!" : "Unit added!");
                loadUnits(competencySelect.value);
                form.reset();
                document.getElementById("unit_id").value = '';
                saveBtn.textContent = "Save";
            }
        })
        .catch(err => console.error(err))
        .finally(() => saveBtn.disabled = false);
    });

    // Edit / Delete Handlers
    function attachEditDeleteHandlers() {
        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                const data = JSON.parse(this.dataset.unit);
                document.getElementById("unit_id").value = data.id;
                document.getElementById("subject_id").value = data.competency.subject_id;
                document.getElementById("competency_id").value = data.competency_id;
                document.getElementById("name").value = data.name;
                document.getElementById("code").value = data.code ?? '';
                saveBtn.textContent = "Update";
            });
        });

        document.querySelectorAll('.deleteBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                if(confirm('Are you sure to delete this unit?')) {
                    fetch(`/syllabus-units/${this.dataset.id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                    .then(res => res.json())
                    .then(resp => {
                        if(resp.success) loadUnits(competencySelect.value);
                    });
                }
            });
        });
    }

    // Reset
    resetBtn.addEventListener("click", function() {
        document.getElementById("unit_id").value = '';
        saveBtn.textContent = "Save";
    });
});
</script>
@endsection
