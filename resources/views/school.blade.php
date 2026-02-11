@extends('layouts.master')

@section('main-content')
<div class="container-flex">

    {{-- HEADER --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card-slider">
                <div id="test2"><p>School Management System</p></div>
                <div id="test1"><p>{{ $institute }}</p></div>
            </div>
        </div>
    </div>

    {{-- BUTTON GRID --}}
    <div class="row mt-3">
        <div class="col-xl-9">
            <h5><em><strong>General Analysis</strong></em></h5>
            <div class="grid-container">
                <button class="grid-btn" onclick="window.location.href='{{ route('employee.index') }}'">
                    <i class="fas fa-user-tie"></i>
                    Manage Employees
                </button>
                <!-- <button class="grid-btn" onclick="showUnderConstruction()">
                    <i class="fas fa-user-tie"></i>
                    Manage Employees
                </button> -->

                <!-- <button class="grid-btn" onclick="window.location.href='{{ route('reports.ol.exam.final.subject.result') }}'">
                    <i class="fas fa-user-graduate"></i>
                    Manage Students
                </button> -->

                <button class="grid-btn" onclick="window.location.href='{{ route('students.index') }}'">
                    <i class="fas fa-user-graduate"></i>
                    Manage Students
                </button>

               <div class="btn-wrapper">
                    <button class="grid-btn" onclick="window.location.href='{{ route('manage.result') }}'">
                        <!-- <span class="card-badge new-badge">New</span> -->
                        <i class="fas fa-file-alt"></i>
                        Manage Results
                    </button>
                </div>
            </div>
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

.card-badge {
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

@push('scripts')
<script>
function showUnderConstruction() {
    alert("🚧 This page is under construction.");
}
</script>
@endpush
