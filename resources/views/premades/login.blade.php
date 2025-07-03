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
    <div style="display: flex; justify-content: center; align-items: center; ">
    @include('shared-utils::components.ajax-form', [
        'form' => $form,
        'style' => 'display: flex; flex-direction: column; align-items: center;'])
    </div>
    @stack('scripts')
</body>
</html>