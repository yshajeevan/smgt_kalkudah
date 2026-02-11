@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Analysis of Teachers and Development Officers (Teaching)</h6>
    </div>
    <div class="card-body">
        <div class="container">
            <div class="chart-container">
                <div class="chart-title">Gender Distribution</div>
                <canvas id="genderChart"></canvas>
            </div>

            <!-- Age Group Chart -->
            <div class="chart-container">
                <div class="chart-title">Age Groups</div>
                <canvas id="ageChart"></canvas>
            </div>

            <!-- Religion Chart -->
            <div class="chart-container">
                <div class="chart-title">Religion Distribution</div>
                <canvas id="religionChart"></canvas>
            </div>

            <!-- Civil Status Chart -->
            <div class="chart-container">
                <div class="chart-title">Civil Status Distribution</div>
                <canvas id="civilStatusChart"></canvas>
            </div>

            <!-- Distance Chart -->
            <div class="chart-container">
                <div class="chart-title">Distance from Office</div>
                <canvas id="distanceChart"></canvas>
            </div>

            <!-- DS Division Chart -->
            <div class="chart-container">
                <div class="chart-title">DS Division Distribution</div>
                <canvas id="dsDivisionChart"></canvas>
            </div>

            <!-- GN Division Chart -->
            <div class="chart-container">
                <div class="chart-title">GN Division Distribution</div>
                <canvas id="gnDivisionChart"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-title">Zone Distribution</div>
                <canvas id="zoneChart"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-title">Transport Mode Distribution</div>
                <canvas id="transmodeChart"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-title">Grade Distribution</div>
                <canvas id="gradeChart"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-title">Designation Distribution</div>
                <canvas id="designationChart"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-title">Trained Distribution</div>
                <canvas id="trainedChart"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-title">Appointment Category Distribution</div>
                <canvas id="appcategoryChart"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-title">Highest Qualification Distribution</div>
                <canvas id="highqualificationChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
        .card-body .container {
            display: grid;
            gap: 20px;
            padding: 20px;
        }

        .chart-container {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            text-align: center;
            overflow: hidden; /* Prevent content overflow */
        }

        .chart-title {
            font-size: 18px;
            margin-bottom: 10px;
        }

        canvas {
            max-width: 100%; /* Ensure canvas fits the container */
        }

        /* Wide Screen (Two Grids) */
        @media (min-width: 768px) {
            .container {
                grid-template-columns: repeat(2, 1fr); /* Two columns */
            }
        }

        /* Mobile Screen (Single Column) */
        @media (max-width: 767px) {
            .container {
                grid-template-columns: 1fr; /* Single column */
            }

            .chart-container {
                padding: 10px; /* Adjust padding for mobile */
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
      const createChart = (ctx, type, labels, data, label, bgColor) => {
        return new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: bgColor
                }]
            },
            options: {
                plugins: {
                    legend: { display: type === 'pie' }
                },
                scales: type === 'pie' ? {} : {
                    y: { beginAtZero: true }
                }
            }
        });
    };

    // Charts
    createChart(document.getElementById('genderChart'), 'pie', {!! json_encode($genderData->keys()) !!}, {!! json_encode($genderData->values()) !!}, 'Gender Distribution', ['#FF6384', '#36A2EB', '#FFCE56']);
    createChart(document.getElementById('ageChart'), 'bar', {!! json_encode($ageGroups->keys()) !!}, {!! json_encode($ageGroups->values()) !!}, 'Age Groups', '#36A2EB');
    createChart(document.getElementById('religionChart'), 'pie', {!! json_encode($religionData->keys()) !!}, {!! json_encode($religionData->values()) !!}, 'Religion Distribution', ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']);
    createChart(document.getElementById('civilStatusChart'), 'bar', {!! json_encode($civilStatusData->keys()) !!}, {!! json_encode($civilStatusData->values()) !!}, 'Civil Status', '#FFCE56');
    createChart(document.getElementById('distanceChart'), 'bar', {!! json_encode($distanceGroups->keys()) !!}, {!! json_encode($distanceGroups->values()) !!}, 'Distance from Office', '#4BC0C0');
    createChart(document.getElementById('dsDivisionChart'), 'bar', {!! json_encode($dsDivisionData->keys()) !!}, {!! json_encode($dsDivisionData->values()) !!}, 'DS Divisions', '#FF6384');
    createChart(document.getElementById('gnDivisionChart'), 'bar', {!! json_encode($gnDivisionData->keys()) !!}, {!! json_encode($gnDivisionData->values()) !!}, 'GN Divisions', '#36A2EB');
    createChart(document.getElementById('zoneChart'), 'bar', {!! json_encode($zoneData->keys()) !!}, {!! json_encode($zoneData->values()) !!}, 'Zone Distribution', '#FF6384');
    createChart(document.getElementById('transmodeChart'), 'bar', {!! json_encode($transmodeData->keys()) !!}, {!! json_encode($transmodeData->values()) !!}, 'Transport Modes', '#4BC0C0');
    createChart(document.getElementById('gradeChart'), 'bar', {!! json_encode($gradeData->keys()) !!}, {!! json_encode($gradeData->values()) !!}, 'Grade Distribution', '#FFCE56');
    createChart(document.getElementById('designationChart'), 'bar', {!! json_encode($designationData->keys()) !!}, {!! json_encode($designationData->values()) !!}, 'Designation Distribution', '#36A2EB');
    createChart(document.getElementById('trainedChart'), 'pie', {!! json_encode($trainedData->keys()) !!}, {!! json_encode($trainedData->values()) !!}, 'Trained', ['#FF6384', '#36A2EB']);
    createChart(document.getElementById('appcategoryChart'), 'bar', {!! json_encode($appcategoryData->keys()) !!}, {!! json_encode($appcategoryData->values()) !!}, 'Appointment Categories', '#FFCE56');
    createChart(document.getElementById('highqualificationChart'), 'bar', {!! json_encode($highqualificationData->keys()) !!}, {!! json_encode($highqualificationData->values()) !!}, 'Highest Qualifications', '#4BC0C0');
    </script>
@endpush
