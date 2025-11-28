@props(['chart' => null, 'color' => '#000000'])
<div id="{{ $chart->get_id() }}"></div>

@push('scripts')
<script>
var chartColor = '{{ $color }}';
var currentYear = new Date().getFullYear();
var options = {
  series: [{
    name: 'sales',
    data: @json($chart->get(interval: Ro749\SharedUtils\Statistics\ChartTime::MONTH,number: 12))
  }],
  chart: {
          type: 'area',
          width: 80,
          height: 42,
          sparkline: {
            enabled: true // Remove whitespace
          },

          toolbar: {
              show: false
          },
          padding: {
              left: 0,
              right: 0,
              top: 0,
              bottom: 0
          }
      },
      dataLabels: {
          enabled: false
      },
      stroke: {
          curve: 'smooth',
          width: 2,
          colors: [chartColor],
          lineCap: 'round'
      },
      grid: {
          show: true,
          borderColor: 'transparent',
          strokeDashArray: 0,
          position: 'back',
          xaxis: {
              lines: {
                  show: false
              }
          },   
          yaxis: {
              lines: {
                  show: false
              }
          },  
          row: {
              colors: undefined,
              opacity: 0.5
          },  
          column: {
              colors: undefined,
              opacity: 0.5
          },  
          padding: {
              top: -3,
              right: 0,
              bottom: 0,
              left: 0
          },  
      },
      fill: {
          type: 'gradient',
          colors: [chartColor], // Set the starting color (top color) here
          gradient: {
              shade: 'light', // Gradient shading type
              type: 'vertical',  // Gradient direction (vertical)
              shadeIntensity: 0.5, // Intensity of the gradient shading
              gradientToColors: [`${chartColor}00`], // Bottom gradient color (with transparency)
              inverseColors: false, // Do not invert colors
              opacityFrom: .75, // Starting opacity
              opacityTo: 0.3,  // Ending opacity
              stops: [0, 100],
          },
      },
      // Customize the circle marker color on hover
      markers: {
        colors: [chartColor],
        strokeWidth: 2,
        size: 0,
        hover: {
          size: 8
        }
      },
      xaxis: {
          labels: {
              show: false
          },
          categories: [`Jan ${currentYear}`, `Feb ${currentYear}`, `Mar ${currentYear}`, `Apr ${currentYear}`, `May ${currentYear}`, `Jun ${currentYear}`, `Jul ${currentYear}`, `Aug ${currentYear}`, `Sep ${currentYear}`, `Oct ${currentYear}`, `Nov ${currentYear}`, `Dec ${currentYear}`],
          tooltip: {
              enabled: false,
          },
      },
      yaxis: {
          labels: {
              show: false
          }
      },
      tooltip: {
          x: {
              format: 'dd/MM/yy HH:mm'
          },
      },
}
var chart = new ApexCharts(document.querySelector("#{{ $chart->get_id() }}"), options);

chart.render();

</script>
@endpush