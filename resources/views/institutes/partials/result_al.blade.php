<div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary float-left">Result Analysis-G.C.E A/L</h6>
</div>
<div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                 <canvas id="line-chart-al" width="800" height="350"></canvas>
              </div>
        </div>   
    </div>
</div>
<script>
    new Chart(document.getElementById('line-chart-al'), {
  type: 'line',
  data: {
    labels: {!! json_encode(($al_result->pluck('year')),JSON_NUMERIC_CHECK) !!},
    datasets: [
     { 
        data: {!! json_encode(($al_result->pluck('percentage')),JSON_NUMERIC_CHECK) !!},
        label: "Arts",
        borderColor: "#3e95cd",
        fill: false,
        tension: 0
      }
    ]
  },
  options: {
    maintainAspectRatio: false,
    title: {
      display: true,
      text: 'G.C.E A/L Pass Percentage'
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