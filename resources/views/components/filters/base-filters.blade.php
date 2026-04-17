@props(['form' => null, 'reset'=>''])
<x-form :form="$form"/>

@push('scripts')
<script>
    $(document).on('submit-{{$form->get_id()}}', function(e, data) {
        $(document).trigger('{{ $reset }}', data);
    });
</script>
@endpush