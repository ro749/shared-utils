<div x-data="{{$form->id}}_submit()" style="{{ isset($style)?$style:"" }}">
    @foreach ($form->formFields as $name => $field)
        @if($field->type === Ro749\SharedUtils\FormRequests\InputType::HIDDEN)
        @continue
        @endif
        <div id="form-field-{{ $name }}" class="form-field" style="width: auto;">
            @if($field->icon)
            <div class="input-group">
                <span class="input-group-text">
                    <i class="{{ $field->icon }}"></i>
                </span>
            @endif
            @if($field->label!="")
            <label for="{{ $name }}" class="block font-semibold">{{ $field->label }}{{ $field->is_required() ? '*' : '' }}</label>
            @endif
            {!! $field->render($name) !!}
            @if($field->icon)
            </div>
            @endif
            <template x-if="errors['{{ $name }}']">
                <p class="form-error" x-text="errors['{{ $name }}']"></p>
            </template>
        </div>
    @endforeach
    <button class="btn btn-light" @click="submit">
        {{ $form->submit_text=="" ? 'Enviar': $form->submit_text }}
    </button>
</div>
@if($form->redirect=="" && $form->popup=="")
@once('form-popup')
    <x-sharedutils::modal  id="form-success-popup" title="Datos guardados correctamente.">
        <p>Datos guardados correctamente.</p>
    </x-modal>
@endonce
@endif

@push('scripts')
<script>
    function {{$form->id}}_submit() {
        return {
            form: {},
            errors: {},
            submit() {
                const urlParams = new URLSearchParams(window.location.search);
console.log('Form Data:', this.form);
for (const key in this.form) {
    console.log(`${key}: ${this.form[key]}`);
}

                for (const key of urlParams.keys()) {
                    this.form[key] = urlParams.get(key);
                }
                $.ajax({
                    url: '{{ $form->submit_url==""? '/form/'.$form->id : $form->submit_url }}',
                    method: 'POST',
                    data: this.form,
                    success: (response) => {
                        @if($form->redirect)
                        window.location.href = response.redirect;
                        @elseif($form->popup)
                        openPopup("{{ $form->popup }}",2500);
                        @elseif($form->callback)
                        {!! $form->callback !!}
                        @else
                        openPopup("form-success-popup",2500);
                        @endif
                        for (const key in this.form) {
                            this.form[key] = '';
                        }
                        this.errors = {};
                    },
                    error: (xhr) => {
                        console.error(xhr);
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            this.errors = xhr.responseJSON.errors;
                        } else {
                            this.errors = { general: 'An error occurred. Please try again.' };
                        }
                    }
                });
            }
        }
    }
</script>
@endpush