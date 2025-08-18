<div id="{{ $table->id }}-container">
    <div style="display: flex; flex-direction: row; align-items: center;">
        <button class="btn btn-light back-btn" style="height:100%; visibility: hidden; align-items: center;">
            <iconify-icon icon="iconamoon:arrow-left-6-circle-thin" class="back-icon"></iconify-icon>
        </button>
        <h6 class="title" style="margin-bottom: 0;"></h6>
    </div>
    <table id="{{ $table->id }}" class="table bordered-table mb-0">
    </table>
</div>

@push('scripts')
<script>
    $('#{{ $table->id }}').layeredSmartTable(@json($table->get_info()));
</script>
@endpush