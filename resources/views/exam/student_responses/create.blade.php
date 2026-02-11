@extends('layouts.master')

@section('main-content')
<div id="flashMessage" class="mt-2"></div>
<div class="container-fluid">
    <div class="row">
        {{-- LEFT: FORM --}}
        <div class="col-md-8">
            <h2 class="mb-4 fw-bold">Enter / Edit Student Responses</h2>

            {{-- Exam --}}
            <div class="mb-3">
                <label for="examSelect" class="form-label">Select Exam</label>
                <select id="examSelect" class="form-select">
                    <option value="">-- Choose Exam --</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Subject --}}
            <div class="mb-3">
                <label for="subjectSelect" class="form-label">Select Subject</label>
                <select id="subjectSelect" class="form-select" disabled>
                    <option value="">-- Choose Subject --</option>
                </select>
            </div>

            {{-- Student --}}
            <div class="mb-3">
                <label for="studentSelect" class="form-label">Select Student</label>
                <select id="studentSelect" class="form-select" disabled>
                    <option value="">-- Choose Student --</option>
                </select>
            </div>

            {{-- Questions Table --}}
            <div id="questionsTable"></div>
        </div>

        {{-- RIGHT: STATUS TABLE --}}
        <div class="col-md-4">
            <h4 class="fw-bold">Student Status</h4>
            <div id="statusTable"></div>
        </div>
    </div>
</div>

<script>
// ------------------- Exam Change -------------------
document.getElementById('examSelect').addEventListener('change', function () {
    let examId = this.value;

    const subjSel = document.getElementById('subjectSelect');
    subjSel.innerHTML = '<option value="">-- Choose Subject --</option>';
    subjSel.disabled = true;

    const stuSel = document.getElementById('studentSelect');
    stuSel.innerHTML = '<option value="">-- Choose Student --</option>';
    stuSel.disabled = true;

    document.getElementById('questionsTable').innerHTML = '';
    document.getElementById('statusTable').innerHTML = '';

    if (examId) {
        fetch("{{ route('student_responses.loadSubjects') }}?exam_id=" + examId)
        .then(res => res.json())
        .then(data => {
            data.forEach(sub => {
                if(sub && sub.id){   
                    subjSel.innerHTML += `<option value="${sub.id}">${sub.cadre ?? sub.name}</option>`;
                }
            });
            subjSel.disabled = false;
        });
    }
});

// ------------------- Subject Change -------------------
document.getElementById('subjectSelect').addEventListener('change', function () {
    const examId = document.getElementById('examSelect').value;
    const subjectId = this.value;

    const stuSel = document.getElementById('studentSelect');
    stuSel.innerHTML = '<option value="">-- Choose Student --</option>';
    stuSel.disabled = true;

    document.getElementById('questionsTable').innerHTML = '';
    loadStatusTable(examId, subjectId);

    if(examId && subjectId){
        fetch(`{{ route('student_responses.loadStudents') }}?exam_id=${examId}&subject_id=${subjectId}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(st => {
                if(st && st.id){
                    stuSel.innerHTML += `<option value="${st.id}">${st.name}</option>`;
                }
            });
            stuSel.disabled = false;
        });
    }
});

// ------------------- Student Change -------------------
document.getElementById('studentSelect').addEventListener('change', function () {
    const examId = document.getElementById('examSelect').value;
    const subjectId = document.getElementById('subjectSelect').value;
    const studentId = this.value;

    if(examId && subjectId && studentId){
        fetch(`{{ route('student_responses.loadQuestions') }}?student_id=${studentId}&exam_id=${examId}&subject_id=${subjectId}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('questionsTable').innerHTML = html;

            // attach submit handler for AJAX (for new HTML loaded)
            attachFormAjax();
        });
    }
});

// ------------------- Status Table -------------------
function loadStatusTable(examId, subjectId){
    if(examId && subjectId){
        fetch(`{{ route('student_responses.status') }}?exam_id=${examId}&subject_id=${subjectId}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('statusTable').innerHTML = html;
        });
    } else {
        document.getElementById('statusTable').innerHTML = '';
    }
}

// ------------------- Edit Student -------------------
function editStudent(studentId, examId, subjectId){
    fetch(`{{ route('student_responses.loadQuestions') }}?student_id=${studentId}&exam_id=${examId}&subject_id=${subjectId}`)
    .then(res => res.text())
    .then(html => {
        document.getElementById('questionsTable').innerHTML = html;
        document.getElementById('studentSelect').value = studentId;

        attachFormAjax();
    });
}

// ------------------- Delete Student -------------------
function deleteStudent(studentId, examId, subjectId){
    if(confirm("Are you sure you want to delete this student's responses?")){
        fetch(`{{ route('student_responses.delete') }}?student_id=${studentId}&exam_id=${examId}&subject_id=${subjectId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadStatusTable(examId, subjectId);
            document.getElementById('questionsTable').innerHTML = '';
            document.getElementById('studentSelect').value = '';
        });
    }
}

// ------------------- AJAX Form Submission -------------------
function attachFormAjax(){
    const form = document.getElementById('responseForm');
    if(!form) return;

    // ------------------- Delegate submit handler -------------------
    document.addEventListener('submit', function(e){
        const form = e.target;
        if(form.id === 'responseForm'){
            e.preventDefault();

            const formData = new FormData(form);
            fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                const flash = document.getElementById('flashMessage');
                flash.innerHTML = '';

                if(data.success){
                    // show flash message
                    flash.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Responses saved successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;

                    // auto-hide after 3 seconds
                    setTimeout(() => {
                        const alert = flash.querySelector('.alert');
                        if(alert) alert.classList.remove('show'); // fade out
                        setTimeout(() => { flash.innerHTML = ''; }, 500); // remove from DOM after fade
                    }, 3000);

                    // reset student & questions
                    const studentSel = document.getElementById('studentSelect');
                    studentSel.value = '';
                    document.getElementById('questionsTable').innerHTML = '';

                    // reload status table
                    const examId = document.getElementById('examSelect').value;
                    const subjectId = document.getElementById('subjectSelect').value;
                    loadStatusTable(examId, subjectId);
                }

            })
            .catch(err => console.error(err));
        }
    });
}
</script>
@endsection
