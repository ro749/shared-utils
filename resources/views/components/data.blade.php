@props(['data' => null])
<div x-data="{}" id="{{ $data->get_id() }}" {{ $attributes }}>
    {{ $slot }}
</div>

@if($data->dynamic)
@push('scripts')
<script>
function get_{{ $data->get_id() }}() {
    $.ajax({
        url: "/data/{{ $data->get_id() }}",
        method: "GET",
        success: function (data) {
            Alpine.$data($('#{{ $data->get_id() }}')[0]).data = data;
        }
    });
}
get_{{ $data->get_id() }}();
</script>
@endpush
@push('reset')
get_{{ $data->get_id() }}();
@endpush
@endif
    