@props(['data' => []])
@php
  $data = $data->toArray();
  $names = array_column($data, 'name');
  $percents = array_column($data, 'modelo_percent');
  $colors = array_column($data, 'color');
@endphp
<div id="dunut-chart"></div>
@push('scripts')
<script>
var options = { 
      series: @json($percents),
      colors: @json($colors),
      labels: @json($names),
      legend: {
          show: false 
      },
      chart: {
        type: 'donut',    
        height: 300,
        sparkline: {
          enabled: true // Remove whitespace
        },
        margin: {
            top: -100,
            right: -100,
            bottom: -100,
            left: -100
        },
        padding: {
          top: -100,
          right: -100,
          bottom: -100,
          left: -100
        }
      },
      stroke: {
        width: 0,
      },
      dataLabels: {
        enabled: false
      },
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200
          },
          legend: {
            position: 'bottom'
          }
        }
      }],
      plotOptions: {
        pie: {
          startAngle: -90,
          endAngle: 90,
          offsetY: 10,
          customScale: 0.8,
          donut: {
            size: '70%',
            labels: {
              show: true,
              total: {
                showAlways: true,
                show: true,
                label: 'Customer Report',
                // formatter: function (w) {
                //     return w.globals.seriesTotals.reduce((a, b) => {
                //         return a + b;
                //     }, 0);
                // }
              }
            },
          }
        }
      },
    };

    var chart = new ApexCharts(document.querySelector("#dunut-chart"), options);
    chart.render();
</script>
@endpush