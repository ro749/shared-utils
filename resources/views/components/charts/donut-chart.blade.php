@props(['id' => '', 'names' => [], 'percents' => [], 'colors' => []])
<div id="{{ $id }}"></div>
@push('scripts')
<script>
var options = {
        series: @json($percents),
        labels: @json($names),
        chart: {
            height: 264,
            type: 'donut',
        },
        colors: @json($colors),
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
                show: false
                }
            }
        }],
        legend: {
            position: 'right',
            offsetY: 0,
            height: 230,
            show: false
        }
};
var chart = new ApexCharts(document.querySelector("#{{ $id }}"), options);
chart.render();
</script>
@endpush