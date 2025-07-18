
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endpush
@if($table->needsButtons())
    @include('shared-utils::includes.icons')
@endif

@if($table->delete)
    @include('shared-utils::includes.modals')
@endif

@push('script-includes')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@endpush

@include('shared-utils::scripts.jsUtils')