@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4" style="margin-bottom:30px; font-weight:bold; color:#000;">Past Year G.C.E O/L Final Exam Result</h2>

    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
        <!-- Loading spinner -->
        <div id="loadingSpinner" style="display:none; margin-left:10px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
        </div>
    </div>

    <!-- Chart row -->
    <div class="row" id="chartRow">
        <div class="col-md-12" id="zonalCol">
            <div style="height:600px;">
                <canvas id="line-chart-ol" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.box {
    box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, 
                rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
    padding: 10px;
    border-radius: 25px;
    margin: 10px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>

<script>
$(document).ready(function() {
    Chart.plugins.register(ChartDataLabels);

    let chartOL;

    function getColor(index) {
        const colors = ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850"];
        return colors[index % colors.length];
    }

    function drawOLChart(labels, data) {
        const ctx = document.getElementById('line-chart-ol').getContext('2d');
        if (chartOL) chartOL.destroy();

        chartOL = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    label: "Pass %",
                    borderColor: getColor(2),
                    fill: false,
                    tension: 0
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        align: 'top',
                        anchor: 'end',
                        color: '#000',
                        font: { weight: 'bold', size: 16 },
                        formatter: value => value + '%'
                    }
                },
                title: { display: true, text: 'G.C.E O/L Pass Percentage' },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            max: 100,
                            callback: val => val + '%',
                            beginAtZero: true,
                            fontSize: 16,
                            fontStyle: 'bold',
                            fontColor: '#000'
                        },
                        scaleLabel: { display: true, labelString: 'Pass Percentage' }
                    }],
                    xAxes: [{
                        ticks: {
                        fontSize: 16,
                        fontStyle: 'bold',
                        fontColor: '#000'
                        }
                    }]
                }
            }
        });
    }

    // ✅ Load O/L Chart on page load
    function loadOLChart() {
         // Show spinner
        document.getElementById('loadingSpinner').style.display = 'inline-block';

        $.get('{{ route("reports.ol_exam.final.result.data") }}', function(response) {
            if (response.length > 0) {
                $('#ol-section').show();
                document.getElementById('loadingSpinner').style.display = 'none';
                const years = response.map(r => r.year);
                const percentages = response.map(r => parseFloat(r.pass_percentage));
                drawOLChart(years, percentages);
            }
        });
    }

    loadOLChart();
});
</script>
@endpush
