@props(['data' => null])
<div x-data="{}" id="{{ $data->get_id() }}" {{ $attributes }}>
    {{ $slot }}
</div>

@if($data->dynamic)
@push('scripts')
<script>
function get_{{ $data->get_id() }}() {
    @if(count($data->request_data) != 0)
    var data = {};
    @endif
    @foreach ($data->request_data as $form) 
    data['{{ $form }}'] = $('#{{ $form }}').;
    @endforeach
    
    $.ajax({
        url: "/data/{{ $data->get_id() }}",
        method: "GET",
        success: function (data) {
            @stack('fill')
        }
    });
}
console.log("getting: {{ $data->get_id() }}");
get_{{ $data->get_id() }}();
</script>
@endpush
@push('reset')
get_{{ $data->get_id() }}();
@endpush
@endif
    