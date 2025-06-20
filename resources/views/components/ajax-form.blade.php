<div x-data="dynamicForm()">
    <form @submit.prevent="submit">
        @csrf

        @foreach ($form->formFields as $name => $field)
            <div class="mb-4">
                <label for="{{ $name }}" class="block font-semibold">{{ $field->label }}{{ $field->is_required() ? '*' : '' }}</label>
                <input
                    type="{{ $field->get_type() }}"
                    name="{{ $name }}"
                    x-model="form.{{ $name }}"
                    
                >
                <template x-if="errors['{{ $name }}']">
                    <p class="form-error" x-text="errors['{{ $name }}']"></p>
                </template>
            </div>
        @endforeach

        <button type="submit">
            {{ $form->submit_text=="" ? 'Enviar': $form->submit_text }}
        </button>
    </form>
</div>
@if($form->url=="" && $form->popup=="")
@once('form-popup')
    <x-sharedutils::modal  id="form-success-popup" title="Datos guardados correctamente.">
        <p>Datos guardados correctamente.</p>
    </x-modal>
@endonce
@endif


<script>
    function dynamicForm() {
        return {
            form: {},
            errors: {},
            submit() {
                $.ajax({
                    url: '/form/{{ $form->id }}',
                    method: 'POST',
                    data: this.form,
                    success: (response) => {
                        @if($form->url)
                        window.location.href = response.url;
                        @elseif($form->popup)
                        openPopup("{{ $form->popup }}",2500);
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