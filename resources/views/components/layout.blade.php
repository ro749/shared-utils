<!DOCTYPE html>
<html>
<head>
    @include(config('overrides.views.head'))
    @stack('styles')
</head>

<body>
    {{ $slot }}
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