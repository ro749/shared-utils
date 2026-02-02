@props(['chart' => null, 'color' => '#000000', 'data' => null])
<div id="{{ $chart->get_id() }}"></div>

@push('scripts')
<script>
var options = {
      series: [{
          name: '{{ $chart->get_series_name() }}',
        @php
        $data = $chart->get($data);
        $labels = $chart->get_categories();
        @endphp
          data: [
            @for($i = 0; $i < count($labels); $i++)
            {
              x: '{{ $labels[$i] }}',
              y: {{ $data[$i] }}
            },
            @endfor
            ]
      }],
      chart: {
          type: 'bar',
          height: 310,
          toolbar: {
              show: false
          }
      },
      plotOptions: {
          bar: {
              borderRadius: 4,
              horizontal: false,
              columnWidth: '23%',
              endingShape: 'rounded',
          }
      },
      dataLabels: {
          enabled: false
      },
      fill: {
          type: 'gradient',
          colors: ['{{ $color }}'], // Set the starting color (top color) here
          gradient: {
              shade: 'light', // Gradient shading type
              type: 'vertical',  // Gradient direction (vertical)
              shadeIntensity: 0.5, // Intensity of the gradient shading
              gradientToColors: ['{{ $color }}'], // Bottom gradient color (with transparency)
              inverseColors: false, // Do not invert colors
              opacityFrom: 1, // Starting opacity
              opacityTo: 1,  // Ending opacity
              stops: [0, 100],
          },
      },
      grid: {
          show: true,
          borderColor: '#D1D5DB',
          strokeDashArray: 4, // Use a number for dashed style
          position: 'back',
      },
      xaxis: {
          type: 'category',
          categories: @json($labels)
      },
    };

    var chart = new ApexCharts(document.querySelector("#{{ $chart->get_id() }}"), options);
    chart.render();
</script>
@endpush