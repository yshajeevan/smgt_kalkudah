@extends('layouts.master')

@section('main-content')
<div class="container-flex">

    {{-- HEADER --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card-slider">
                <div id="test1"><p>Result Analysis of Grade 10 and 11 Students</p></div>
                <div id="test2"><p>{{ $institute }}</p></div>
            </div>
        </div>
    </div>

    {{-- BUTTON GRID --}}
    <div class="row mt-3">
        <div class="col-xl-9">
            <h5><em><strong>General Analysis</strong></em></h5>
            <div class="grid-container">
                <button class="grid-btn" onclick="window.location.href='{{ route('reports.ol.exam.final.result') }}'">
                    <i class="fas fa-chart-line"></i>
                    G.C.E O/L Final Exam Pass %
                </button>

                <button class="grid-btn" onclick="window.location.href='{{ route('reports.ol.exam.final.subject.result') }}'">
                    <i class="fas fa-book-open"></i>
                    G.C.E O/L Final Exam Subject wise Pass %
                </button>

               <div class="btn-wrapper">
                    <button class="grid-btn" onclick="window.location.href='{{ route('students.optionals.index') }}'">
                        <span class="badge new-badge">New</span>
                        <i class="fas fa-puzzle-piece"></i>
                        Update Optional Subjects
                    </button>
                </div>

                <button class="grid-btn" onclick="window.location.href='{{ route('marks.create') }}'">
                    <i class="fas fa-plus"></i>
                    Add Marks
                </button>

                <div class="btn-wrapper">
                    <button class="grid-btn" onclick="window.location.href='{{ route('reports.pass') }}'">
                        <span class="badge">Updated</span>
                        <i class="fas fa-percent"></i>
                        Assessment Exam Subject wise Pass %
                    </button>
                </div>

                <div class="btn-wrapper">
                    <button class="grid-btn" onclick="window.location.href='{{ route('reports.average') }}'">
                        <span class="badge">Updated</span>
                        <i class="fas fa-chart-bar"></i>
                        Assessment Exam Subject wise Average (PI)
                    </button>
                </div>

                <button class="grid-btn" onclick="window.location.href='{{ route('reports.students.subject.marks') }}'">
                    <i class="fas fa-user-graduate"></i>
                    Assessment Exam Subject wise Student's Performance
                </button>

                <button class="grid-btn" onclick="window.location.href='{{ route('reports.student.average.allsubject.marks') }}'">
                    <i class="fas fa-users"></i>
                    Assessment Exam Student's Subject wise Performance
                </button>

                <button class="grid-btn" onclick="window.location.href='{{ route('reports.exam.attendance') }}'">
                    <i class="fas fa-user-check"></i>
                    Assessment Exam Attendance Proportion
                </button>

                <button class="grid-btn" onclick="window.location.href='{{ route('reports.student.marks.table') }}'">
                    <i class="fas fa-table"></i>
                    Assessment Exam Marks Table
                </button>

                <button class="grid-btn" onclick="window.location.href='{{ route('reports.student.ranks.table') }}'">
                    <i class="fas fa-trophy"></i>
                    Assessment Exam Rank Table
                </button>

                <button class="grid-btn" onclick="window.location.href='{{ route('reports.student.attendance') }}'">
                    <i class="fas fa-calendar-check"></i>
                    Assessment Exam Student's Term Attendance %
                </button>

                <div class="btn-wrapper">
                    <button class="grid-btn" onclick="window.location.href='{{ route('reports.student.marks.print') }}'">
                        <span class="badge updated-badge">Updated</span>
                        <i class="fas fa-print"></i>
                        Assessment Exam Student's Marks Sheet (Printable)
                    </button>
                </div>


                <button class="grid-btn" onclick="window.location.href='{{ route('reports.school.subject.analysis') }}'">
                    <i class="fas fa-school"></i>
                    Assessment Exam School wise Subjects Ranking
                </button>

                <button class="grid-btn" onclick="window.location.href='{{ route('reports.school.overall.analysis') }}'">
                    <i class="fas fa-layer-group"></i>
                    Assessment Exam School wise Pass % Ranking
                </button>
            </div>

            <br>
            <h5><em><strong>Item Analysis</strong></em></h5>
            <div class="grid-container">
                <button class="grid-btn" onclick="window.location.href='{{ route('competencies.index') }}'">
                    <i class="fas fa-lightbulb"></i>
                    Add and Manage Competencies
                </button>

                <button class="grid-btn" onclick="window.location.href='{{ route('syllabus_units.index') }}'">
                    <i class="fas fa-book"></i>
                    Add and Manage Syllabus Unit
                </button>

                <button class="grid-btn" onclick="window.location.href='{{ route('questions.manage') }}'">
                    <i class="fas fa-question-circle"></i>
                    Add and Manage Questions
                </button>

                <button class="grid-btn" onclick="window.location.href='{{ route('student_responses.create') }}'">
                    <i class="fas fa-check-circle"></i>
                    Add Student Responses
                </button>

                <button class="grid-btn" onclick="window.location.href='{{ route('reports.item.analysis') }}'">
                    <i class="fas fa-chart-bar"></i>
                    Item Analysis
                </button>

                <button class="grid-btn" onclick="window.location.href='{{ route('reports.student.unit.analysis') }}'">
                    <i class="fas fa-search-plus"></i>
                    Unit wise Item Analysis
                </button>
            </div> 

            <br>
            <h5><em><strong>Awardings</strong></em></h5>
            <div class="grid-container">
                <button class="grid-btn" onclick="window.location.href='{{ route('reports.subject.medal.winners') }}'">
                    <i class="fas fa-award"></i>
                    Subject wise student awardings
                </button>

                <div class="btn-wrapper">
                    <button class="grid-btn" onclick="window.location.href='{{ route('reports.subject.improvement.awards') }}'">
                        <span class="badge new-badge">New</span>
                        <i class="fas fa-star"></i>
                        Subject wise student Best Aachievement award
                    </button>
                </div>
            </div> 
        </div>

        {{-- SUMMARY TABLE --}}
        <div class="col-xl-3">
            <div class="box">
                <h5 class="box-title">Summary of Data Entry</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Grade</th>
                                <th>Stu.Exist</th>
                                <th>Stu.Entered</th>
                                <th>Exam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summary as $row)
                                <tr>
                                    <td>{{ $row['grade'] }}</td>
                                    <td>{{ $row['stuExist'] }}</td>
                                    <td>{{ $row['stuEntere'] }}</td>
                                    <td>{{ $row['lastTerm'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="row mt-4">
        <div class="col-xl-12 text-center">
            <p><b>Zonal Education Office, Batticaloa West</b></p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* General */
.container-flex { padding: 20px; }
.card-slider {
    background: maroon;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
}
#test1 p { font-size: 28px; color: white; margin: 5px; }
#test2 p { font-size: 36px; color: white; margin: 5px; font-weight: bold; }

/* Button grid */
.grid-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
}
.btn-wrapper {
    position: relative;
    width: 100%;
}

.grid-btn {
    position: relative;
    background: #cfe3f3;
    border: 1px solid #99bbdd;
    padding: 25px 15px;
    text-align: center;
    border-radius: 10px;
    font-weight: 600;
    transition: 0.3s;
    width: 100%;
    font-size: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 140px;
}

.badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: #ff5252;
    color: white;
    font-size: 12px;
    font-weight: bold;
    padding: 2px 8px;
    border-radius: 8px;
}

.new-badge {
    background: #ffcc00; /* yellow */
    color: #000; /* black text for contrast */
}

.grid-btn i { font-size: 36px; margin-bottom: 10px; }
.grid-btn:hover { background: #a9c9ec; transform: scale(1.05); cursor: pointer; }

/* Summary table */
.box {
    border: 1px solid #ccc;
    border-radius: 6px;
    background: white;
    padding: 10px;
    box-shadow: 0px 2px 4px rgba(0,0,0,0.1);
}
.box-title { font-weight: bold; margin-bottom: 10px; font-size: 16px; color: black; }
.table { font-size: 16px; text-align: center; font-weight: bold; color: black; }
.table th { background: #f1f1f1; font-weight: bold; color: black; font-size: 16px; }
.table td { font-weight: bold; color: black; font-size: 16px; }

/* Mobile responsiveness */
@media (max-width: 992px) {
    .grid-container { grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .grid-btn { min-height: 120px; font-size: 14px; padding: 20px 10px; }
    .grid-btn i { font-size: 28px; }
    #test1 p { font-size: 22px; }
    #test2 p { font-size: 28px; }
}

@media (max-width: 768px) {
    .grid-container { grid-template-columns: repeat(2, 1fr); }
    .grid-btn { min-height: 100px; font-size: 13px; padding: 18px 10px; }
    .grid-btn i { font-size: 26px; }
    #test1 p { font-size: 20px; }
    #test2 p { font-size: 24px; }
}

@media (max-width: 480px) {
    .grid-container { grid-template-columns: 1fr; }
    .grid-btn { min-height: 90px; font-size: 12px; padding: 15px 10px; }
    .grid-btn i { font-size: 24px; }
    #test1 p { font-size: 18px; }
    #test2 p { font-size: 20px; }
}
</style>
@endpush
