@props(['chart' => null, 'color' => '#000000', 'data' => null])
<style>
#{{ $chart->get_id() }} text {
    transform: translateX(36px);
}
</style>
<x-base-chart :chart="$chart">
    <div id="{{ $chart->get_id() }}"></div>
</x-base-chart>
@push('scripts')
<script>
var options = {
      series: [{
        name: '{{ $chart->get_series_name() }}',
        data: @json($chart->get($data))
    }],
    chart: {
        height: 528,
        type: 'line',
        colors: '#000',
        zoom: {
            enabled: false
        },
        toolbar: {
            show: false
        },
    },
    colors: ['{{ $color }}'],  // Set the color of the series
    dataLabels: {
      enabled: true,
      formatter: function (val, opts) {
          return "$" + val.toLocaleString(); 
        }
    },
    stroke: {
      curve: 'straight',
      width: 4,
      color: "#000"
    },
    markers: {
        size: 0,
        strokeWidth: 3,
        hover: {
            size: 8
        }
    },
    grid: {
        show: true,
        borderColor: '#D1D5DB',
        strokeDashArray: 3,
        row: {
          colors: ['#f3f3f3', 'transparent'],
          opacity: 0,
        },
        padding: {
            //left: 216  // add space on the right for the labels
        },
    },
    // Customize the circle marker color on hover
    markers: {
        colors: '#487FFF',
        strokeWidth: 3,
        size: 0,
        hover: {
            size: 10
        }
    },
    xaxis: {
        categories: @json($chart->get_categories()),
        lines: {
            show: false
        }
    },
    yaxis: {
        opposite: false,
        labels: {
            //offsetX: 216,
            //offsetY: 36,
            floating: false,
            formatter: function (value) {
                return "$" + value.toLocaleString();
            },
            style: {
                fontSize: "14px"
            },
            minWidth: 0,
            maxWidth: 1000,
        },
    },
};
var chart = new ApexCharts(document.querySelector("#{{ $chart->get_id() }}"), options);
chart.render();


$("#{{ $chart->get_id() }}").on('reset', function(event, data) {
    $.ajax({
        url: '/chart/{{ $chart->get_id() }}',
        method: 'GET',
        data: data,
        success: function(response) {
            chart.updateOptions({
                series: [{
                    data: response
                }],
            });
            //chart.render();
        },
    });
    
});
</script>
@endpush