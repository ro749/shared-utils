<div>
    @if(!empty($chart->getter->filters))
        <x-base-filters :form="$chart->getter->filters" />
@push('scripts')
<script>
    $(document).on('submit-{{ $chart->getter->filters->get_id() }}', function(e, data) {

        $('#{{ $chart->get_id() }}').trigger('reset', data);
    });
</script>
@endpush

    @endif
    {{ $slot }}
</div>