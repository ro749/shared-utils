@props(['data' => null])
<div x-data="{}" {{ $attributes }}>
    {{ $slot }}
</div>

@if($data->dynamic)
@push('scripts')
    <script>
        
    </script>
@endpush
@endif
    