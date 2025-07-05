<!DOCTYPE html>
<html>
<head>
    <title>Inputs Test</title>
    @include('shared-utils::includes.min')
    @include('shared-utils::includes.select2')
    @include('shared-utils::includes.modals')
    @include('shared-utils::includes.forms')
    @include('shared-utils::includes.icons')
    @stack('styles')
</head>


<body>
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
        <div class="card" style="padding:1.5rem;">
            <img src="/Images/Logos/logo.png" id="logo" style="margin-bottom:1.5rem;">
            @include('shared-utils::components.ajax-form', [
                'form' => $form,
                'style' => 'display: flex; flex-direction: column; align-items: center;'
            ])
        </div>
    </div>
    @stack('scripts')
</body>
</html>