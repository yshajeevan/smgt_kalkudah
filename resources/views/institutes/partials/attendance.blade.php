<div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary float-left">Daily Attendance of Students</h6>
</div>
<div class="card-body">
    <div style="width: 100%; overflow-x: auto; overflow-y: hidden">
  <div style="width: 3000px; height: 300px">
    <canvas id="attendance" height="300" width="0"></canvas>
  </div>
</div>
</div>
<script>
    new Chart(document.getElementById('attendance'), {
  type: 'line',
  data: {
    labels: {!! json_encode(($attendance->pluck('date')),JSON_NUMERIC_CHECK) !!},
    datasets: [{ 
        data: {!! json_encode(($attendance->pluck('percentage')),JSON_NUMERIC_CHECK) !!},
        label: "Percentage",
        borderColor: "#3e95cd",
        fill: true,
        tension: 0.5
      }
    ]
  },
  options: {
    maintainAspectRatio: false,
    title: {
      display: true,
      text: 'Daily Attendance'
    },
  tooltips: {
                enabled: true
            },
            hover: {
                animationDuration: 1
            },
            animation: {
            duration: 1,
            onComplete: function () {
                var chartInstance = this.chart,
                    ctx = chartInstance.ctx;
                    ctx.textAlign = 'center';
                    ctx.fillStyle = "rgba(0, 0, 0, 1)";
                    ctx.textBaseline = 'bottom';

                    // Loop through each data in the datasets

                    this.data.datasets.forEach(function (dataset, i) {
                        var meta = chartInstance.controller.getDatasetMeta(i);
                        meta.data.forEach(function (bar, index) {
                            var data = dataset.data[index];
                            ctx.fillText(data, bar._model.x, bar._model.y - 5);

                        });
                    });
                }
            },
    scales: {
        xAxes: [{
            display: true,
            scaleLabel: {
                display: true,
                labelString: 'Year'
            }
        }],
        yAxes: [{
            display: true,
            ticks: {
                beginAtZero: true,
                steps: 10,
                stepValue: 5,
                max: 100,
                callback: function(value, index, ticks) {
                        return value + '%';
                    }
            },
            scaleLabel: {
                display: true,
                labelString: 'Pass Percentage'
            }
        }]
    }
  }      
}); 
</script>