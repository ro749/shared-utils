@props(['id' => '', 'names' => [], 'percents' => [], 'colors' => []])

<div id="{{ $id }}"></div>
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

    var chart = new ApexCharts(document.querySelector("#{{ $id }}"), options);
    chart.render();
</script>
@endpush