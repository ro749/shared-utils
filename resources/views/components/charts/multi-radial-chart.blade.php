@props(['id' => '', 'names' => [], 'percents' => [], 'colors' => [], 'radialWidth' => "100%", 'hollowSize' => "1%"])
<div id="{{ $id }}" style="width: 100%;"></div>
@push('scripts')
<script>
    var options = {
        series: @json($percents),
        chart: {
            width: "{{ $radialWidth }}",
            type: "radialBar",
        },
        colors: @json($colors),
        stroke: {
            lineCap: "round",
        },
        plotOptions: {
            radialBar: {
                hollow: {
                    size: "{{ $hollowSize }}", // Adjust this value to control the bar width
                },
                dataLabels: {
                    name: {
                        fontSize: "16px",
                    },
                    value: {
                        fontSize: "16px",
                    },
                    // total: {
                    //     show: true,
                    //     formatter: function (w) {
                    //         return "82%"
                    //     }
                    // }
                },
                track: {
                    margin: 1, // Space between the bars
                }
            }
        },
        labels: @json($names),
    };
    var chart = new ApexCharts(document.querySelector("#{{ $id }}"), options);
    chart.render();
</script>
@endpush