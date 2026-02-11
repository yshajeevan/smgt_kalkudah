@extends('layouts.master')

@section('main-content')
<div class="container-fluid">
    <h4>Edit Student - {{ $student->name }}</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
        </div>
    @endif

    <form method="POST" id="studentEditForm" action="{{ route('students.update', $student) }}">
        @csrf
        @method('PUT')

        {{-- Hidden force flags set by JS when user confirms cross-school actions --}}
        <input type="hidden" name="force_admission" id="force_admission" value="{{ old('force_admission', 0) }}">
        <input type="hidden" name="force_institute" id="force_institute" value="{{ old('force_institute', 0) }}">

        <div class="row">
            <div class="col-md-6">

                <div class="mb-2">
                    <label>Admission Number</label>
                    <input type="text" id="admission_input" name="admission_number" class="form-control" value="{{ old('admission_number', $student->admission_number) }}" required>
                    <div id="admissionHelp" class="small text-muted mt-1"></div>
                </div>

                <div class="mb-2">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $student->name) }}" required>
                </div>

                <div class="mb-2">
                    <label>Grade</label>
                    <select name="grade_id" id="grade_id" class="form-control" required>
                        <option value="">-- Select grade --</option>
                        @foreach($gradesForRows as $g)
                            <option value="{{ $g->id }}" {{ (old('grade', $student->grade_id) == $g->id) ? 'selected' : '' }}>
                                {{ $g->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="Active" {{ $student->status == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Droped out" {{ $student->status == 'Droped out' ? 'selected' : '' }}>Droped out</option>
                    </select>
                </div>

                {{-- EWS dropdown --}}
                <div class="mb-2">
                    <label>EWS</label>
                    <select name="ews_color" id="ews_color" class="form-control">
                        <option value="">-- Select EWS --</option>
                        <option value="1" {{ (old('ews_color', $student->ews_color) == 1) ? 'selected' : '' }}>Green (Regular)</option>
                        <option value="2" {{ (old('ews_color', $student->ews_color) == 2) ? 'selected' : '' }}>Orange (Not to school 14 days)</option>
                        <option value="3" {{ (old('ews_color', $student->ews_color) == 3) ? 'selected' : '' }}>Red (Not to school 21 days)</option>
                    </select>
                </div>

                {{-- Cadre selects (initialCadres1/2/3/4 provided by controller) --}}
                <div class="mb-2">
                    <label>Cadre Subject 1</label>
                    <select name="cadresubject1_id" id="cadre1" class="form-control">
                        <option value="">-- None --</option>
                        @foreach($initialCadres1 as $c)
                            <option value="{{ $c->id }}" {{ (old('cadresubject1_id', $student->cadresubject1_id) == $c->id) ? 'selected' : '' }}>
                                {{ $c->cadre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label>Cadre Subject 2</label>
                    <select name="cadresubject2_id" id="cadre2" class="form-control">
                        <option value="">-- None --</option>
                        @foreach($initialCadres2 as $c)
                            <option value="{{ $c->id }}" {{ (old('cadresubject2_id', $student->cadresubject2_id) == $c->id) ? 'selected' : '' }}>
                                {{ $c->cadre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label>Cadre Subject 3</label>
                    <select name="cadresubject3_id" id="cadre3" class="form-control">
                        <option value="">-- None --</option>
                        @foreach($initialCadres3 as $c)
                            <option value="{{ $c->id }}" {{ (old('cadresubject3_id', $student->cadresubject3_id) == $c->id) ? 'selected' : '' }}>
                                {{ $c->cadre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label>Cadre Subject 4 (Religion)</label>
                    <select name="cadresubject4_id" id="cadre4" class="form-control">
                        <option value="">-- None --</option>
                        @foreach($initialCadres4 as $c)
                            <option value="{{ $c->id }}" {{ (old('cadresubject4_id', $student->cadresubject4_id) == $c->id) ? 'selected' : '' }}>
                                {{ $c->cadre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label>Father Name</label>
                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $student->father_name) }}">
                </div>

                <div class="mb-2">
                    <label>Father NIC</label>
                    <input type="text" name="father_nic" class="form-control" value="{{ old('father_nic', $student->father_nic) }}">
                </div>

                <div class="mb-2">
                    <label>Mother Name</label>
                    <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $student->mother_name) }}">
                </div>

            </div>

            <div class="col-md-6">

                {{-- DS and GN dependent --}}
                <div class="mb-2">
                    <label>DS Division</label>
                    <select id="ds_select" name="dsdivision_id" class="form-control">
                        <option value="">-- Select DS --</option>
                        @foreach($dsdivisions as $d)
                            <option value="{{ $d->id }}" {{ (old('dsdivision_id', $student->dsdivision_id) == $d->id) ? 'selected' : '' }}>
                                {{ $d->ds }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label>GN Division</label>
                    <select id="gn_select" name="gndivision_id" class="form-control">
                        <option value="">-- Select GN --</option>
                        @foreach($gndivisions as $g)
                            <option value="{{ $g->id }}" {{ (old('gndivision_id', $student->gndivision_id) == $g->id) ? 'selected' : '' }}>
                                {{ $g->gn }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label>Mobile</label>
                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $student->mobile) }}">
                </div>

                <div class="mb-2">
                    <label>Address</label>
                    <textarea name="address" class="form-control">{{ old('address', $student->address) }}</textarea>
                </div>

                <div class="mb-2">
                    <label>Institute (transfer)</label>
                    <select id="institute_select" name="institute_id" class="form-control">
                        <option value="">-- Keep current --</option>
                        @foreach($institutesForSelect as $inst)
                            <option value="{{ $inst->id }}" {{ $student->institute_id == $inst->id ? 'selected' : '' }}>{{ $inst->institute }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1">Changing institute requires confirmation — this will not be applied until you click <strong>Save</strong>.</small>
                </div>

            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>

@push('style')
<style>
    /* highlight pending changes on the edit page as well */
    .row-pending-change { background-color: #fff7e6 !important; }
    .pending-note { font-size: 0.9rem; color: #794c00; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // map color to EWS id
    function mapHexToEws(hex) {
        hex = hex.replace('#','').toLowerCase();
        const r = parseInt(hex.substring(0,2),16);
        const g = parseInt(hex.substring(2,4),16);
        const b = parseInt(hex.substring(4,6),16);

        const candidates = [
            { id: 1, r: 0,   g:255, b:0   }, // green
            { id: 2, r: 255, g:165, b:0   }, // orange
            { id: 3, r: 255, g:0,   b:0   }  // red
        ];

        let best = candidates[0];
        let bestDist = Number.POSITIVE_INFINITY;
        candidates.forEach(c => {
            const dist = Math.pow(r-c.r,2) + Math.pow(g-c.g,2) + Math.pow(b-c.b,2);
            if (dist < bestDist) { bestDist = dist; best = c; }
        });

        return best.id;
    }

    const colorPicker = document.getElementById('ewsColorPicker');
    const hiddenEws = document.getElementById('ews_color');
    const swatch = document.getElementById('ewsSwatch');

    if(colorPicker) {
        colorPicker.addEventListener('change', function(){
            const hex = this.value;
            const mapped = mapHexToEws(hex);
            hiddenEws.value = mapped;
            const displayColor = (mapped == 1 ? '#00ff00' : (mapped == 2 ? '#ffa500' : '#ff0000'));
            if(swatch) swatch.style.background = displayColor;
        });
    }

    // --- admission blur validation (AJAX) ---
    const admissionInput = document.getElementById('admission_input');
    const admissionHelp = document.getElementById('admissionHelp');
    const forceAdmissionInput = document.getElementById('force_admission');

    if (admissionInput) {
        // store baseline original
        if (!admissionInput.dataset.originalAdm) admissionInput.dataset.originalAdm = admissionInput.value;

        admissionInput.addEventListener('blur', async function(e) {
            const newVal = (this.value || '').trim();
            const original = this.dataset.originalAdm || '';

            if (newVal === original) return;
            if (!newVal) {
                alert('Admission number cannot be empty.');
                this.value = original;
                return;
            }

            // call server validate (no force)
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
                    if (!ok) {
                        this.value = original;
                        return;
                    }

                    // user confirmed -> set force_admission hidden and mark pending (no save yet)
                    forceAdmissionInput.value = 1;
                    markPendingChange('admission', newVal);
                    this.dataset.originalAdm = newVal;
                    admissionHelp.textContent = 'Confirmed — pending save.';
                    return;
                }

                if (res.ok && data.success) {
                    // OK — mark pending; user must click Save to apply
                    markPendingChange('admission', newVal);
                    this.dataset.originalAdm = newVal;
                    admissionHelp.textContent = 'Valid — pending save.';
                    return;
                }

                // error
                alert(data.message || 'Admission validation failed.');
                this.value = original;
            } catch (err) {
                console.error(err);
                alert('Request failed during admission validation.');
                this.value = original;
            }
        });
    }

    // --- institute change confirm (mark pending) ---
    const instituteSelect = document.getElementById('institute_select');
    const forceInstituteInput = document.getElementById('force_institute');
    if (instituteSelect) {
        // store baseline
        if (!instituteSelect.dataset.originalInstitute) instituteSelect.dataset.originalInstitute = instituteSelect.value;

        instituteSelect.addEventListener('change', function(){
            const prev = instituteSelect.dataset.originalInstitute || '';
            const newVal = this.value;

            if (String(prev) === String(newVal)) {
                // nothing
                instituteSelect.dataset.originalInstitute = newVal;
                return;
            }

            const schoolName = this.options[this.selectedIndex] ? this.options[this.selectedIndex].text : 'selected school';
            const ok = confirm('Are you sure want to transfer the student to selected school: ' + schoolName + ' ?');

            if (!ok) {
                this.value = prev; // revert
                return;
            }

            // user confirmed: mark force flag and pending (actual change applied on Save)
            forceInstituteInput.value = 1;
            markPendingChange('institute', newVal);
            instituteSelect.dataset.originalInstitute = newVal;
        });
    }

    // mark pending visual (we add class on the form wrapper)
    function markPendingChange(type, value) {
        // add a visible note under form if not present
        let note = document.getElementById('pendingNote');
        if (!note) {
            note = document.createElement('div');
            note.id = 'pendingNote';
            note.className = 'pending-note mt-2';
            note.textContent = 'You have pending changes. Click Save to apply them.';
            const form = document.getElementById('studentEditForm');
            form.parentNode.insertBefore(note, form.nextSibling);
        }
        // add highlight style to form to show pending change
        const form = document.getElementById('studentEditForm');
        form.classList.add('row-pending-change');
    }

    const dsSelect = document.getElementById('ds_select');
    const gnSelect = document.getElementById('gn_select');

    async function loadGndsForDs(dsId, preselect = null) {
        gnSelect.innerHTML = '<option>Loading…</option>';
        try {
            const res = await fetch(`/manage-students/ds/${dsId}/gnds`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            gnSelect.innerHTML = '<option value=\"\">-- Select GN --</option>';
            if (data && data.gnds && data.gnds.length) {
                data.gnds.forEach(g => {
                    const opt = document.createElement('option');
                    opt.value = g.id;
                    opt.textContent = g.gn;
                    if (preselect && String(preselect) === String(g.id)) opt.selected = true;
                    gnSelect.appendChild(opt);
                });
            } else {
                // no GN for this DS
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = '-- No GN divisions --';
                gnSelect.appendChild(opt);
            }
        } catch(err) {
            console.error(err);
            gnSelect.innerHTML = '<option value=\"\">-- Error loading GN --</option>';
        }
    }

    if (dsSelect) {
        dsSelect.addEventListener('change', function(){
            const dsId = this.value;
            // clear previous GN
            if (!dsId) {
                gnSelect.innerHTML = '<option value=\"\">-- Select GN --</option>';
                return;
            }
            loadGndsForDs(dsId);
        });

        // on page load: if a DS is already selected, ensure GN list loaded (use current value)
        const initDs = dsSelect.value;
        const preGn = '{{ old('gndivision_id', $student->gndivision_id ?? '') }}';
        if (initDs) {
            loadGndsForDs(initDs, preGn);
        }
    }

    // optional: on submit, ensure force flags are either set or server will block
    // no extra JS needed — server validates again


    //Cadre subjects validation
    const gradeSelect = document.getElementById('grade_id');
    const s1 = document.getElementById('cadre1');
    const s2 = document.getElementById('cadre2');
    const s3 = document.getElementById('cadre3');
    const s4 = document.getElementById('cadre4'); // DO NOT change via grade-change

    function clearSelect(sel) {
        if (!sel) return;
        sel.innerHTML = '<option value="">-- None --</option>';
    }

    function populateSelect(sel, items, preselect = null) {
        if (!sel) return;
        clearSelect(sel);
        (items || []).forEach(it => {
            const opt = document.createElement('option');
            opt.value = it.id;
            opt.textContent = it.cadre;
            sel.appendChild(opt);
        });
        // if preselect passed and exists in options, set it
        if (preselect) {
            const found = Array.from(sel.options).some(o => String(o.value) === String(preselect));
            if (found) sel.value = preselect;
        }
    }

    function showOrHideCadres(show) {
        [s1, s2, s3].forEach(sel => {
            if (!sel) return;
            const wrap = sel.closest('.mb-2');
            if (wrap) wrap.style.display = show ? '' : 'none';
        });
    }

    async function onGradeChange(gid) {
        if (!gid) { showOrHideCadres(false); return; }

        // If grade < 6 hide cadres 1/2/3
        if (Number(gid) < 6) {
            showOrHideCadres(false);
            return;
        }

        try {
            const res = await fetch(`/manage-students/grade/${gid}/cadres`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            // Grades 6-9 => use basket2 for 1/2/3 (new requirement)
            if ([6,7,8,9].includes(Number(gid))) {
                showOrHideCadres(true);
                populateSelect(s1, data.basket2 || [], null);
                populateSelect(s2, data.basket2 || [], null);
                populateSelect(s3, data.basket2 || [], null);
            }
            // Grades 10-13 => basket1/basket2/basket3 mapping
            else if ([10,11,12,13].includes(Number(gid))) {
                showOrHideCadres(true);
                populateSelect(s1, data.basket1 || [], null);
                populateSelect(s2, data.basket2 || [], null);
                populateSelect(s3, data.basket3 || [], null);
            }
            // Grades 14-37 => A/L for all three
            else if (Number(gid) >= 14 && Number(gid) <= 37) {
                showOrHideCadres(true);
                populateSelect(s1, data.al || [], null);
                populateSelect(s2, data.al || [], null);
                populateSelect(s3, data.al || [], null);
            }
            // Grades 38/39 => 13_years_education for all three
            else if ([38,39].includes(Number(gid))) {
                showOrHideCadres(true);
                populateSelect(s1, data.yrs13 || [], null);
                populateSelect(s2, data.yrs13 || [], null);
                populateSelect(s3, data.yrs13 || [], null);
            } else {
                // any other grade: hide the three selects (or keep empty)
                showOrHideCadres(false);
            }

            // IMPORTANT: DO NOT touch s4 (cadre4) here; religion select remains as server-rendered.
        } catch (err) {
            console.error('Failed to load cadres for grade', gid, err);
        }
    }

    // attach change listener (only run when user changes grade)
    if (gradeSelect) {
        gradeSelect.addEventListener('change', function () {
            onGradeChange(this.value);
        });
    }

    // client-side duplicate prevention for cadre1/2/3
    function dupCheckHandler() {
        const v1 = s1 ? s1.value : '';
        const v2 = s2 ? s2.value : '';
        const v3 = s3 ? s3.value : '';
        const arr = [v1, v2, v3].filter(x => x);
        const dup = arr.some((val, idx) => arr.indexOf(val) !== idx);
        if (dup) {
            alert('Please select different subjects for Cadre Subject 1, 2 and 3. Duplicates are not allowed.');
            this.value = '';
        }
    }
    [s1, s2, s3].forEach(s => {
        if (!s) return;
        s.removeEventListener('change', dupCheckHandler);
        s.addEventListener('change', dupCheckHandler);
    });

});
</script>
@endpush

@endsection
