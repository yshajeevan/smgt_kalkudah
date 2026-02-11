@extends('layouts.master')

@section('main-content')
<div class="container-fluid">
    <h4>Students</h4>

    {{-- reference images --}}
    <div class="mb-2 text-muted small">Reference images: /mnt/data/a5296086-e92a-494b-8580-7407b50e326c.png, /mnt/data/6360555f-c0b1-49a4-a03b-b6c49c3a8a08.png</div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filters & controls --}}
    <div class="row mb-3">
        <div class="col-md-8">
            <form method="GET" action="{{ route('students.index') }}" class="row g-2 align-items-center">
                <div class="col-auto">
                    <input type="search" name="q" class="form-control form-control-sm" placeholder="Search name / admission no" value="{{ $q ?? '' }}">
                </div>

                <div class="col-auto">
                    <select name="grade" class="form-select form-select-sm">
                        <option value="">All Grades</option>
                        @foreach($availableGrades as $g)
                            <option value="{{ $g->id }}" {{ (isset($filterGrade) && $filterGrade == $g->id) ? 'selected' : '' }}>
                                {{ $g->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <select name="ews_color" class="form-select form-select-sm">
                        <option value="">All EWS</option>
                        @foreach($availableEws as $ew)
                            <option value="{{ $ew }}" {{ (isset($filterEws) && $filterEws == $ew) ? 'selected' : '' }}>
                                {{ $ew==1 ? 'Green' : ($ew==2 ? 'Orange' : 'Red') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach($availableStatus as $st)
                            <option value="{{ $st }}" {{ (isset($filterStatus) && $filterStatus == $st) ? 'selected' : '' }}>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <button class="btn btn-sm btn-primary">Filter</button>
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="col-md-4 text-end">
            <div class="small text-muted">
                Total records: <strong id="totalCount">{{ $totalCount ?? 0 }}</strong> —
                Filtered: <strong id="filteredCount">{{ $filteredCount ?? 0 }}</strong>
            </div>
        </div>
    </div>

    <small class="text-muted d-block">EWS (Early warning system): Green (Regular) · Orrange (Not to school for 14 days) · Red (Not to school for 21 days)</small>

    {{-- inline CSS to guarantee pending highlight works even if layout stack differs --}}
    <style>
    .row-pending-change { background-color: #fff7e6 !important; }
    </style>

    {{-- table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-sm" id="studentsTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Admission No</th>
                    <th>Grade</th>
                    <th>EWS</th>
                    <th>Status</th>
                    <th>Institute</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="studentsRows">
                @foreach($students as $stu)
                <tr data-id="{{ $stu->id }}" data-current-institute="{{ $stu->institute_id }}">
                    <td>{{ $loop->iteration + (($students->currentPage()-1) * $students->perPage()) }}</td>

                    <td><input type="text" class="form-control form-control-sm quick-input name" value="{{ $stu->name }}"></td>

                    <td><input type="text" class="form-control form-control-sm quick-input admission_number" value="{{ $stu->admission_number }}"></td>

                    {{-- Inline grade select uses gradesForRows --}}
                    <td>
                        <select class="form-select form-select-sm grade-select">
                            <option value="">--</option>
                            @foreach($gradesForRows as $g)
                                <option value="{{ $g->id }}" {{ $stu->grade_id == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <div class="d-flex align-items-center">
                            <div class="ews-swatch me-2" style="width:28px;height:18px;border:1px solid #ccc;background:{{ $stu->ews_color==1? '#00ff00' : ($stu->ews_color==2? '#ffa500' : ($stu->ews_color==3? '#ff0000':'#fff')) }};" data-ews="{{ $stu->ews_color }}"></div>
                            <select class="form-select form-select-sm ews-select" style="width:auto;">
                                <option value="">--</option>
                                <option value="1" {{ $stu->ews_color==1 ? 'selected':'' }}>Green</option>
                                <option value="2" {{ $stu->ews_color==2 ? 'selected':'' }}>Orange</option>
                                <option value="3" {{ $stu->ews_color==3 ? 'selected':'' }}>Red</option>
                            </select>
                        </div>
                    </td>

                    <td>
                        <select class="form-select form-select-sm status">
                            <option value="Active" {{ $stu->status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Droped out" {{ $stu->status == 'Droped out' ? 'selected' : '' }}>Droped out</option>
                        </select>
                    </td>

                    {{-- Institute select (only in table rows). id < 69 list provided by controller --}}
                    <td>
                        <select class="form-select form-select-sm institute-select">
                            @foreach($institutesForSelect as $inst)
                                <option value="{{ $inst->id }}" {{ $stu->institute_id == $inst->id ? 'selected' : '' }}>
                                    {{ $inst->institute }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <button class="btn btn-sm btn-success btn-quick-save">Update</button>
                        <a href="{{ route('students.edit', $stu) }}" class="btn btn-sm btn-primary">Edit</a>

                        @if(Auth::user()->role === 'super_admin')
                            <form method="POST" action="{{ route('students.destroy', $stu) }}" style="display:inline-block" onsubmit="return confirm('Delete?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- quick add (no institute select here per your request) --}}
    <div class="mt-3">
        <div class="card p-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input id="new_name" type="text" class="form-control form-control-sm" placeholder="Name">
                </div>
                <div class="col-md-2">
                    <input id="new_adm" type="text" class="form-control form-control-sm" placeholder="Admission No">
                </div>
                <div class="col-md-2">
                    <select id="new_grade" class="form-select form-select-sm">
                        <option value="">Grade</option>
                        @foreach($gradesForRows as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                <select id="new_ews" class="form-select form-select-sm">
                    <option value="1" selected>Green</option>
                    <option value="2">Orange</option>
                    <option value="3">Red</option>
                </select>

                <select id="new_status" class="form-select form-select-sm">
                    <option value="Active" selected>Active</option>
                    <option value="Droped out">Droped out</option>
                </select>
                <div class="col-md-1 text-end">
                    <button id="btnAddStudent" class="btn btn-sm btn-success">Add</button>
                </div>
            </div>
            <div id="quickAddMsg" class="mt-2" style="display:none;"></div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-between align-items-center">
        <div>Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $filteredCount }} entries</div>
        <div>{{ $students->links() }}</div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const debounce = (fn, delay=300) => {
        let t;
        return (...args) => { clearTimeout(t); t = setTimeout(()=> fn(...args), delay); };
    };

    const qInput = document.querySelector('input[name="q"]');
    const gradeFilter = document.querySelector('select[name="grade"]');
    const ewsFilter = document.querySelector('select[name="ews_color"]');
    const statusFilter = document.querySelector('select[name="status"]');
    const rowsContainer = document.getElementById('studentsRows');
    const totalCountEl = document.getElementById('totalCount');
    const filteredCountEl = document.getElementById('filteredCount');

    const listUrl = "{{ route('students.list') }}";

    function fetchList() {
        const params = new URLSearchParams();
        if(qInput && qInput.value) params.append('q', qInput.value);
        if(gradeFilter && gradeFilter.value) params.append('grade', gradeFilter.value);
        if(ewsFilter && ewsFilter.value) params.append('ews_color', ewsFilter.value);
        if(statusFilter && statusFilter.value) params.append('status', statusFilter.value);

        fetch(`${listUrl}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(r => r.json()).then(data => {
            rowsContainer.innerHTML = data.html;
            if(filteredCountEl) filteredCountEl.textContent = data.filteredCount;
            if(totalCountEl) totalCountEl.textContent = data.totalCount;
            bindRowEvents(); // rebind for new rows
        }).catch(err => console.error(err));
    }

    const debouncedFetch = debounce(fetchList, 350);

    // live search
    if(qInput) qInput.addEventListener('input', debouncedFetch);

    // filters change
    [gradeFilter, ewsFilter, statusFilter].forEach(el => {
        if(el) el.addEventListener('change', fetchList);
    });

    // bind events for EWS select, Quick save, Grade select, Institute select
    function bindRowEvents() {
        // EWS change
        document.querySelectorAll('.ews-select').forEach(sel=>{
            sel.removeEventListener('change', ewsChangeHandler);
            sel.addEventListener('change', ewsChangeHandler);
        });

        // grade change (inline update)
        document.querySelectorAll('.grade-select').forEach(sel=>{
            sel.removeEventListener('change', gradeChangeHandler);
            sel.addEventListener('change', gradeChangeHandler);
        });

        // institute select (transfer)
        document.querySelectorAll('.institute-select').forEach(sel=>{
            sel.removeEventListener('change', instituteChangeHandler);
            sel.addEventListener('change', instituteChangeHandler);
        });

        // admission validation binding
        bindAdmissionValidation();

        // quick save (name/admission/status)
        document.querySelectorAll('.btn-quick-save').forEach(b=>{
            b.removeEventListener('click', quickSaveHandler);
            b.addEventListener('click', quickSaveHandler);
        });
    }

    function ewsChangeHandler() {
        const tr = this.closest('tr');
        const id = tr.dataset.id;
        const val = this.value;
        const sw = tr.querySelector('.ews-swatch');
        if(sw) sw.style.background = val==1? '#00ff00' : (val==2? '#ffa500' : (val==3? '#ff0000':'#fff'));

        if(!val) return;
        fetch(`/manage-students/${id}/update-ews`, {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: JSON.stringify({ ews_color: parseInt(val) })
        }).then(r => r.json()).then(resp=>{
            if(!resp.success) alert('EWS update failed');
            else if(sw) sw.dataset.ews = resp.ews_color;
        }).catch(err => console.error(err));
    }

    function gradeChangeHandler() {
        const tr = this.closest('tr');
        const id = tr.dataset.id;
        const grade_id = this.value || null;

        fetch(`/manage-students/${id}/quick-update`, {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: JSON.stringify({ grade_id })
        }).then(r => r.json()).then(resp=>{
            if(!resp.success) alert('Grade update failed');
        }).catch(err => console.error(err));
    }

    function instituteChangeHandler() {
        const sel = this;
        const tr = sel.closest('tr');
        const id = tr.dataset.id;
        const prev = tr.dataset.currentInstitute || null;
        const newInst = sel.value;

        // no change
        if (String(prev) === String(newInst)) return;

        // get selected school name for message
        const schoolName = sel.options[sel.selectedIndex].text || 'selected school';

        const ok = confirm('Are you sure want to transfer the student to selected school: ' + schoolName + ' ?');

        if (!ok) {
            // revert selection to previous value
            sel.value = prev;
            return;
        }

        // If confirmed: DON'T call server now.
        // Just mark the row as having a pending institute change; visual cue and data attribute.
        tr.dataset.pendingInstitute = newInst;            // used later by quickSaveHandler
        tr.classList.add('row-pending-change');          // visual cue (CSS below)

        // Optionally update the displayed current-institute value only after successful save.
        // We leave data-current-institute unchanged until user clicks Update.
    }



    // admission validation: binds blur handler to admission inputs
    function bindAdmissionValidation() {
        document.querySelectorAll('.admission_number').forEach(inp => {
            if (!inp.dataset.originalAdm) inp.dataset.originalAdm = inp.value;
            inp.removeEventListener('blur', admissionBlurHandler);
            inp.addEventListener('blur', admissionBlurHandler);
        });
    }

    async function admissionBlurHandler(e) {
        const inp = this;
        const newVal = (inp.value || '').trim();
        const tr = inp.closest('tr');
        const original = inp.dataset.originalAdm || '';

        if (newVal === original) return;
        if (!newVal) {
            alert('Admission number cannot be empty.');
            inp.value = original;
            return;
        }

        try {
            const res = await fetch("{{ route('students.check-admission') }}", {
                method: 'POST',
                headers: {
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ admission_number: newVal, force: false })
            });

            const data = await res.json().catch(()=> ({}));

            if (data.confirm_needed) {
                const otherName = data.other_institute_name || 'selected school';
                const ok = confirm((data.message || 'Are you sure?') + '\n\nSelected school: ' + otherName);
                if (!ok) { inp.value = original; return; }

                const res2 = await fetch("{{ route('students.check-admission') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ admission_number: newVal, force: true })
                });
                const data2 = await res2.json().catch(()=> ({}));
                if (data2.success) {
                    tr.dataset.pendingAdmission = newVal;
                    tr.classList.add('row-pending-change');
                    inp.dataset.originalAdm = newVal;
                } else {
                    alert(data2.message || 'Validation failed'); inp.value = original;
                }
                return;
            }

            if (res.ok && data.success) {
                tr.dataset.pendingAdmission = newVal;
                tr.classList.add('row-pending-change');
                inp.dataset.originalAdm = newVal;
                return;
            }

            alert(data.message || 'Admission validation failed.'); inp.value = original;
        } catch (err) {
            console.error(err); alert('Request failed during admission validation.'); inp.value = original;
        }
    }

    function quickSaveHandler(e) {
        e.preventDefault();
        const tr = this.closest('tr');
        const id = tr.dataset.id;

        const name = tr.querySelector('.name').value;
        const pendingAdm = tr.dataset.pendingAdmission;
        const admission = pendingAdm ? pendingAdm : (tr.querySelector('.admission_number') ? tr.querySelector('.admission_number').value.trim() : '');
        const status = tr.querySelector('.status').value;
        const sw = tr.querySelector('.ews-swatch');
        const ews_color = sw && sw.dataset.ews ? parseInt(sw.dataset.ews) : null;
        const grade = tr.querySelector('.grade-select').value || null;

        const pendingInst = tr.dataset.pendingInstitute;
        const institute_id = pendingInst ? pendingInst : (tr.querySelector('.institute-select') ? tr.querySelector('.institute-select').value : null);

        fetch(`/manage-students/${id}/quick-update`, {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: JSON.stringify({ name, admission_number: admission, status, ews_color, grade_id: grade, institute_id })
        }).then(r => r.json()).then(resp=>{
            if(resp.success){
                const btn = tr.querySelector('.btn-quick-save');
                btn.textContent = 'Saved';
                setTimeout(()=> btn.textContent = 'Update', 900);

                if(pendingInst){ tr.dataset.currentInstitute = institute_id; delete tr.dataset.pendingInstitute; }
                if(pendingAdm){ delete tr.dataset.pendingAdmission; }
                tr.classList.remove('row-pending-change');
            } else {
                alert(resp.message || 'Update failed');
            }
        }).catch(err => console.error(err));
    }

    // Quick add (AJAX) — uses existing quick-store endpoint and census checks server-side
    document.getElementById('btnAddStudent').addEventListener('click', function(e){
        e.preventDefault();
        const name = document.getElementById('new_name').value.trim();
        const admission = document.getElementById('new_adm').value.trim();
        const grade_id = document.getElementById('new_grade').value || null;
        const ews_color = document.getElementById('new_ews').value || null;
        const status = document.getElementById('new_status').value || 'Active';
        const msgDiv = document.getElementById('quickAddMsg');

        if(!name || !admission){
            msgDiv.style.display = 'block';
            msgDiv.className = 'alert alert-warning mt-2';
            msgDiv.textContent = 'Name and Admission number are required.';
            return;
        }

        function sendAdd(force = false) {
            fetch(`{{ route('students.quick-store') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, admission_number: admission, grade_id, ews_color, status, force })
            }).then(async r => {
                const data = await r.json().catch(()=>({}));
                if (data.confirm_needed) {
                    const ok = confirm(data.message || 'Are you sure that the student comes from another school?');
                    if (ok) sendAdd(true); else {
                        msgDiv.style.display = 'block';
                        msgDiv.className = 'alert alert-info mt-2';
                        msgDiv.textContent = 'Save cancelled.';
                    }
                    return;
                }

                if (r.ok && data.success) {
                    msgDiv.style.display = 'block';
                    msgDiv.className = 'alert alert-success mt-2';
                    msgDiv.textContent = 'Student added.';
                    document.getElementById('new_name').value = '';
                    document.getElementById('new_adm').value = '';
                    document.getElementById('new_grade').value = '';
                    document.getElementById('new_ews').value = '';
                    document.getElementById('new_status').value = 'Active';
                    if(typeof fetchList === 'function'){ fetchList(); } else { window.location.reload(); }
                } else {
                    msgDiv.style.display = 'block';
                    msgDiv.className = 'alert alert-danger mt-2';
                    msgDiv.textContent = data.message || 'Failed to add student';
                }
            }).catch(err => {
                console.error(err);
                msgDiv.style.display = 'block';
                msgDiv.className = 'alert alert-danger mt-2';
                msgDiv.textContent = 'Request failed.';
            });
        }

        sendAdd(false);
    });

    // initial bind on page load
    bindRowEvents();
});
</script>
@endpush


@endsection