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
</script>
<script>
function openPopup(id,time=0){
    $('#'+id).show();
    document.documentElement.style.overflowY ="hidden";
    if(time!=0){
        setTimeout(function(){closePopup(id)},time);
    }
}

function closePopup(id){
    $('#'+id).hide();
    document.documentElement.style.overflowY ="scroll";
}

</script>
@endpush