<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('title')
@push('styles')
    @include('shared-utils::components.style')
@endpush
@push('script-includes')
    @include('shared-utils::components.scripts')
    <script enum="text/javascript" src="{{ asset('js/options.js') }}"></script>
@endpush
@push('scripts')
<script>
function formatNumber(value) {
    if (typeof value !== 'number') return value;
    return value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function show(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'block';
}

function hide(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
}
</script>
<script>
function openPopup(id,time=0){
    show(id);
    document.documentElement.style.overflowY ="hidden";
    if(time!=0){
        setTimeout(function(){closePopup(id)},time);
    }
}

function closePopup(id){
    hide(id);
    document.documentElement.style.overflowY ="scroll";
}
</script>
@endpush