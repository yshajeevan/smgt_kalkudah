@extends('layouts.master')

@section('main-content')
<div class="container-fluid">
    <h4>Enter Student Marks</h4>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Exam Dropdown --}}
    <select id="exam_id" class="form-control mb-3">
        <option value="">-- Select Exam --</option>
        @foreach($exams as $exam)
            <option value="{{ $exam->id }}" 
                {{ (isset($selectedExamId) && $selectedExamId == $exam->id) ? 'selected' : '' }}>
                {{ $exam->name }} ({{ $exam->year }})
            </option>
        @endforeach
    </select>

    <div class="row">
        {{-- Left: Marks Entry --}}
        <div class="col-md-6">
            <form id="marksForm" method="POST" action="{{ route('marks.store') }}">
                @csrf
                <input type="hidden" name="exam_id" id="examInput" value="{{ $selectedExamId ?? '' }}">

                <div class="form-group">
                    <label>Select Student</label>
                    <select name="student_id" id="student_id" class="form-control">
                        <option value="">-- Select Student --</option>
                        @foreach($students as $stu)
                            <option value="{{ $stu->id }}" {{ (old('student_id') == $stu->id) ? 'selected' : '' }}>
                                {{ $stu->name }} ({{ $stu->id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="subjectsContainer">
                    {{-- Subjects + attendance will load here via AJAX --}}
                    @if(old('marks') && !empty(old('marks')))
                        {{-- optional: re-render old inputs when validation fails --}}
                        @foreach(old('marks') as $subId => $val)
                            <div class="form-group d-flex align-items-center mb-2">
                                <label class="col-md-4 mb-0">Subject {{ $subId }}</label>
                                <input type="number" name="marks[{{ $subId }}]" class="form-control col-md-4 mark-input" value="{{ $val }}">
                                <div class="form-check ms-2">
                                    <input type="checkbox" name="is_absent[{{ $subId }}]" class="form-check-input" {{ isset(old('is_absent')[$subId]) ? 'checked' : '' }}>
                                    <label class="form-check-label">Absent</label>
                                </div>
                            </div>
                        @endforeach
                        <div class="form-group mt-3">
                            <label>Attendance (Days)</label>
                            <input type="number" name="attendance" id="attendance" class="form-control" value="{{ old('attendance') }}">
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary mt-3">Save Marks</button>
            </form>
        </div>

        {{-- Right: Student Panel --}}
        <div class="col-md-6">
            @php $examSelected = isset($selectedExamId) && $selectedExamId; @endphp

            <div id="studentsPanel" style="{{ $examSelected ? '' : 'display:none;' }}">
                <h5>Marks Table</h5>
                <input type="text" id="studentSearch" class="form-control mb-2" placeholder="Search by name or ID">
                <div style="max-height: 540px; overflow-y: auto;">
                    <table class="table table-bordered table-sm" id="studentsTable">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Student</th>
                                <th>Subjects Entered</th>
                                <th>Attendance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($examSelected)
                                @php $examId = $selectedExamId; @endphp
                                @foreach($students as $stu)
                                    @php
                                        $marksCount = \App\Models\Mark::where('student_id', $stu->id)->where('exam_id', $examId)->count();
                                        $attendance = \App\Models\StudentAttendance::where('student_id', $stu->id)->where('exam_id', $examId)->value('attendance');
                                        $completed = $marksCount > 0 || !is_null($attendance);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $stu->name }} ({{ $stu->id }})</td>
                                        <td>{!! $marksCount !!} {!! $completed ? '<span class="text-success ms-1">✔</span>' : '' !!}</td>
                                        <td>{{ $attendance ?? '-' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-btn" data-student="{{ $stu->id }}">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="5" class="text-center">Please select an exam to view students.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Inline script --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const examSelect = document.getElementById('exam_id');
    const studentSelect = document.getElementById('student_id');
    const subjectsContainer = document.getElementById('subjectsContainer');
    const studentsPanel = document.getElementById('studentsPanel');
    const studentsTbody = document.querySelector('#studentsTable tbody');
    const studentSearch = document.getElementById('studentSearch');

    // helper: show/hide students panel
    function toggleStudentsPanel(show) {
        if(!studentsPanel) return;
        studentsPanel.style.display = show ? '' : 'none';
    }

    // Bind edit buttons
    function bindEditButtons() {
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.removeEventListener('click', editHandler);
            btn.addEventListener('click', editHandler);
        });
    }

    function editHandler(e) {
        const studentId = this.dataset.student;
        const examId = examSelect.value;
        if(!examId){ alert("Please select an exam first."); return; }
        loadStudentData(studentId, examId);
    }

    // Load student subjects + marks via AJAX
    function loadStudentData(studentId, examId) {
        document.getElementById('examInput').value = examId;
        studentSelect.value = studentId;

        if(studentId && examId){
            fetch(`/students/${studentId}/subjects?exam_id=${examId}`)
            .then(res => {
                if(!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                let html = '';

                data.subjects.forEach((sub, index) => {
                    let markValue = data.marks[sub.id] ?? '';
                    let isAbsent = (data.absent && (data.absent[sub.id] == 1 || data.absent[sub.id] === '1')) ? 'checked' : '';

                    html += `<div class="form-group d-flex align-items-center mb-2">
                                <label class="col-md-4 mb-0">${sub.name}</label>
                                <input type="number" name="marks[${sub.id}]" 
                                    class="form-control col-md-4 mark-input" 
                                    placeholder="Enter marks" 
                                    value="${markValue}" 
                                    ${isAbsent ? '' : 'required'} 
                                    tabindex="${index + 1}">
                                <div class="form-check ms-2">
                                    <input type="checkbox" name="is_absent[${sub.id}]" 
                                        value="1" 
                                        class="form-check-input absent-checkbox" 
                                        ${isAbsent} 
                                        tabindex="-1">
                                    <label class="form-check-label">Absent</label>
                                </div>
                            </div>`;
                });

                html += `<div class="form-group mt-3">
                            <label>Attendance (Days)</label>
                            <input type="number" name="attendance" 
                                   id="attendance" 
                                   class="form-control" 
                                   placeholder="Enter attendance days" 
                                   value="${data.attendance ?? ''}" 
                                   tabindex="${data.subjects.length + 1}">
                        </div>`;

                subjectsContainer.innerHTML = html;

                // Wire absent checkboxes to toggle required
                document.querySelectorAll('.absent-checkbox').forEach(chk => {
                    chk.addEventListener('change', function(){
                        let markInput = this.closest('.form-group').querySelector('.mark-input');
                        if(markInput) markInput.required = !this.checked;
                    });
                });
            })
            .catch(err => {
                console.error(err);
                alert('Failed to load student data.');
            });
        }
    }

    // When student select changed
    if(studentSelect){
        studentSelect.addEventListener('change', function(){
            const studentId = this.value;
            const examId = examSelect.value;
            loadStudentData(studentId, examId);
        });
    }

    // When exam select changed: refresh students summary + if student selected, reload subjects
    function handleExamChange() {
        const examId = examSelect.value;
        document.getElementById('examInput').value = examId;

        if(!examId) {
            toggleStudentsPanel(false);
            // clear students table body to friendly message
            if(studentsTbody) studentsTbody.innerHTML = '<tr><td colspan="5" class="text-center">Please select an exam to view students.</td></tr>';
            return;
        }

        toggleStudentsPanel(true);

        // refresh students table via AJAX
        fetch(`/exam/${examId}/students-summary`)
        .then(res => {
            if(!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if(studentsTbody) {
                studentsTbody.innerHTML = data.html;
                bindEditButtons();
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to refresh students list.');
        });

        // reload subjects for currently selected student, if any
        const currentStudentId = studentSelect ? studentSelect.value : null;
        if(currentStudentId) {
            loadStudentData(currentStudentId, examId);
        }
    }

    if(examSelect){
        // initial: show/hide students panel depending on selected exam (server-side may have rendered it)
        toggleStudentsPanel(!!examSelect.value);

        examSelect.addEventListener('change', handleExamChange);
    }

    // Search filter (only active when students panel visible)
    if(studentSearch){
        studentSearch.addEventListener('keyup', function(){
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#studentsTable tbody tr');
            let visibleIndex = 0;
            rows.forEach(row => {
                let studentCell = (row.querySelector('td:nth-child(2)')?.textContent || '').toLowerCase();
                if(studentCell.includes(filter)){
                    row.style.display = '';
                    visibleIndex++;
                    let firstCell = row.querySelector('td:first-child');
                    if(firstCell) firstCell.textContent = visibleIndex;
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // initial bind for edit buttons (if any)
    bindEditButtons();
});
</script>
@endpush
@endsection
