<div style="height:{{ $statistics->get_height() }}px">
<canvas id="{{ $statistics->id }}" style="width:100px;height:100px"></canvas>
</div>
@push('scripts')
<script>
	var {{ $statistics->id }} = null;
	$( document ).ready(function() {
    {{ $statistics->id }} = new Chart(document.getElementById('{{ $statistics->id }}'), {
		type: 'horizontalBar',
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
						beginAtZero: true,
						fontColor: '#fff',
					},
					gridLines: {
						display: true,
						color: 'rgba(255, 255, 255, 0.24)'
					},
				}],
				yAxes: [{
					ticks: {
						beginAtZero: true,
						fontColor: 'rgba(255, 255, 255, 0.64)',
					},
					gridLines: {
						display: true,
						color: 'rgba(255, 255, 255, 0.24)'
					},
				}],
			}
		}
	});

	});
    </script>
@endpush