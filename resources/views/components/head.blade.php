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