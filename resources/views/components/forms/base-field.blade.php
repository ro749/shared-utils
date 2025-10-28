@php
$field = $form->fields[$name];
@endphp
@if($field->type === Ro749\SharedUtils\Forms\InputType::FILE)
{!! $field->render($name,$form->get_id(),$form->initial_data->{$name}??($form->initial_data[$name]??'')) !!}
@else
<div id="form-field-{{ $name }}" class="form-field" style="width: 100%;">
    @if($field->label!="")
        <label for="{{ $name }}" class="block font-semibold">{{ $field->label }}{{ $field->is_required() ? '*' : '' }}</label>
    @endif
    @if($field->icon)
    <div class="icon-field">
        <span class="icon">
            <iconify-icon icon="{{ $field->icon }}"></iconify-icon>
        </span>
    @endif
    {!! $field->render($name,$form->get_id(),$form->initial_data->{$name}??($form->initial_data[$name]??'')) !!}
    @if($field->icon)
    </div>
    @endif
    <template x-if="errors['{{ $name }}']">
        <p class="form-error" x-text="errors['{{ $name }}']"></p>
    </template>
</div>
@endif