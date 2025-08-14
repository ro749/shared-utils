<table id="{{ $table->id }}" class="table bordered-table mb-0">
</table>

@push('scripts')
<script>
    $('#{{ $table->id }}').layeredSmartTable(@json($table->get_info()));
</script>
@endpush