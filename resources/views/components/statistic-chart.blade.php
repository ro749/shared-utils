<div style="{{ $style }}">
<canvas id="{{ $statistics->id }}" ></canvas>
</div>
@push('scripts')
<script>
	var {{ $statistics->id }} = null;
	$( document ).ready(function() {
    {{ $statistics->id }} = new Chart(document.getElementById('{{ $statistics->id }}'), {
		type: '{{ $type }}',
		data: {
			labels: @json($statistics->get_labels()),
			datasets: [{
				label: '{{ $statistics->label }}',
				backgroundColor: @json($statistics->get_colors()),
				data: @json($statistics->get_data())
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			legend: {
				display: false
			},
			scales: {
				xAxes: [{
					ticks: {
						autoSkip: false,
						fontColor: 'rgba(128, 128, 128, 1)',
					},
					gridLines: {
						display: true,
						color: 'rgba(128, 128, 128, 0.5)'
					},
				}],
				yAxes: [{
					ticks: {
						beginAtZero: true,
						fontColor: 'rgba(128, 128, 128, 1)',
					},
					gridLines: {
						display: true,
						color: 'rgba(128, 128, 128, 0.5)'
					},
				}],
			}
		}
	});

	});
    </script>
@endpush 