<!DOCTYPE html>
<html>
<head>
    @include(config('overrides.views.head')??'shared-utils::components.head')
    @stack('styles')
</head>

<body {{ $attributes }}>
    {{ $slot }}
    @stack('script-includes-utils')
    @stack('script-includes')
    @push('scripts')
    <script>
        function reset(){
            @stack('reset')
        }
    </script>
    @endpush
    @stack('scripts')
</body>
</html>