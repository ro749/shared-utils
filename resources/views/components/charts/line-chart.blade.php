@props(['chart' => null, 'color' => '#000000', 'data' => null])
<x-base-chart :chart="$chart">
    <div id="{{ $chart->get_id() }}"></div>
</x-base-chart>
@push('scripts')
<script>
    console.log('line-chart');
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
        }
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
            labels: {
                align: 'right',
                formatter: function (value) {
                    return "$" + value.toLocaleString();
                },
                style: {
                    fontSize: "14px"
                },
                minWidth: 0,
                maxWidth: 1000,
                offsetX: 0,
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