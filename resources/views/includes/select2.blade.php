@include('shared-utils::includes.min')

@push('styles')
<link href="https://blixdev.com/ListingEngine/assets/plugins/select2/css/select2.min.css" rel="stylesheet" />
<link href="https://blixdev.com/ListingEngine/assets/plugins/select2/css/select2-bootstrap4.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://blixdev.com/ListingEngine/assets/js/jquery.min.js"></script>
<script src="https://blixdev.com/ListingEngine/assets/plugins/select2/js/select2.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({theme: 'bootstrap4'});
    });
</script>
@endpush