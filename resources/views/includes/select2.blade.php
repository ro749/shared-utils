@include('shared-utils::includes.min')

@push('styles')
<link href="https://blixdev.com/ListingEngine/assets/plugins/select2/css/select2.min.css" rel="stylesheet" />
<link href="https://blixdev.com/ListingEngine/assets/plugins/select2/css/select2-bootstrap4.css" rel="stylesheet" />
@endpush

@push('scripts')

<script src="https://blixdev.com/ListingEngine/assets/plugins/select2/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({theme: 'bootstrap4'});
    });
</script>
@endpush