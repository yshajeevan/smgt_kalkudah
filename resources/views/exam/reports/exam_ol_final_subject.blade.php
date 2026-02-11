@extends('layouts.master')

@section('main-content')
<div class="container">
    <h2 class="mb-4" style="margin-bottom:30px; font-weight:bold; color:#000;">Exam Attendance Distribution</h2>

    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
        <label>Subject</label>
        <select class="form-control select2" id="subject">
            <option value="">-- Select Subject --</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject }}">{{ $subject }}</option>
            @endforeach
        </select>

        <!-- Loading spinner -->
        <div id="loadingSpinner" style="display:none; margin-left:10px;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
        </div>
    </div>

    <div class="card shadow p-4">
        <div style="height:600px;">
            <canvas id="ol-subject-chart" height="300"></canvas>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function () {
    $('.select2').select2();
    Chart.plugins.register(ChartDataLabels);
    let chart;

    function loadChart(subject) {
        if (!subject) return;

        document.getElementById('loadingSpinner').style.display = 'inline-block';

        $.ajax({
            url: '{{ route("reports.ol.exam.final.subject.result.data") }}',
            data: { subject },
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                document.getElementById('loadingSpinner').style.display = 'none';

                if (!Array.isArray(data) || data.length === 0) {
                    $('#ol-subject-section').hide();
                    return;
                }

                $('#ol-subject-section').show();

                let labels = data.map(r => r.year);
                let pass   = data.map(r => parseFloat(r.pass_percent));
                let pi     = data.map(r => parseFloat(r.pi));

                if (chart) chart.destroy();

                let ctx = document.getElementById('ol-subject-chart').getContext('2d');
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: "Pass %",
                                data: pass,
                                borderColor: "#3cba9f",
                                fill: false,
                                tension: 0
                            },
                            {
                                label: "PI",
                                data: pi,
                                borderColor: "#c45850",
                                fill: false,
                                tension: 0
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            datalabels: {
                                align: 'top',
                                anchor: 'end',
                                color: '#000',
                                font: { weight: 'bold', size: 16 },
                                formatter: v => v.toFixed(2)
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    max: 100,
                                    beginAtZero: true,
                                    fontSize: 16,
                                    fontStyle: 'bold',
                                    fontColor: '#000'
                                },
                                scaleLabel: { display: true, labelString: 'Value' }
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
        });
    }

    // Load chart when subject changes
    $('#subject').on('change', function () {
        loadChart($(this).val());
    });

    // 👇 Load default chart on page load (first subject in dropdown)
    let defaultSubject = $('#subject option:eq(1)').val(); 
    if (defaultSubject) {
        $('#subject').val(defaultSubject).trigger('change');
    }
});
</script>
@endpush
