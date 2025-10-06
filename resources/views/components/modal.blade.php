@props(['id' => '', 'innerStyle' => ''])
<div class="modal" id="{{ $id }}">
    <div class="card popup" style="{{ $innerStyle }}">
        <div class="card-header popup-header">
        <iconify-icon  id ="close'.$id.'span" class="close-popup-button" icon="iconamoon:close-light" onclick="{{ isset($onclose)?$onclose:"" }} closePopup('{{ $id }}')"></iconify-icon>
        </div>
        <div class="card-body popup-body" >
            {{ $slot }}
        </div>
    </div>
</div>
@push('scripts')
<script>
    $('#{{ $id }}').on('click', function(event) {
        if (event.target === this) {
            closePopup('{{ $id }}');
        }
    });
</script>
@endpush