<!DOCTYPE html>
<html>
<head>
    @include('shared-utils::components.head')
    @stack('styles')
</head>


<body>
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
        <div class="card login-card" style="padding:1.5rem;">
            <img src="/Images/Logos/logo.png" id="logo" style="margin-bottom:1.5rem;">
            <x-smartForm :form="$form" style="display: flex; flex-direction: column; align-items: center; gap: 6px;" />
        </div>
    </div>
    @stack('script-includes')
    @stack('scripts')
</body>
</html>