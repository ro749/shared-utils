<div id='image-map-pro'>

</div>

@push('script-includes')
<script src="vendor/shared-utils/js/image-map-pro.min.js"></script>
@endpush

@push('scripts')
<script>
    $(document).ready(function () {
        $.ajax({
            url: "{{ route('image-map-pro') }}",
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                ImageMapPro.init('#image-map-pro',response);
            }
        });
    });
</script>
@endpush