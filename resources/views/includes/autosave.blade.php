@include('shared-utils::includes.min')
@push('scripts')
<script src="https://blixdev.com/ListingEngine/assets/plugins/ro749/autosave.js"></script>
@endpush
@push('js')
        $('.autosave').autoSaveInput();
@endpush