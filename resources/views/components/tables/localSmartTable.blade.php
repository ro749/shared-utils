@include('sharedutils::components.tables.table', ['table' => $table])

@once
    @push('script-includes')
        <script src="{{ mixed('vendor/shared-utils/js/localSmartTable.js') }}"></script>
    @endpush
@endonce

@push('scripts')
<script>
    @php 
    $info = $table->get_info();
    $info['name'] = $name;
    @endphp
    $('#{{ $table->get_id() }}').localSmartTable(@json($info));
</script>
@endpush

