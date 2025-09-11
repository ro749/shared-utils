<div x-data="{{$form->id}}_submit()" id="{{ $form->id }}">
    @foreach ($form->formFields as $name => $field)
        @if($field->type === Ro749\SharedUtils\FormRequests\InputType::HIDDEN)
        @continue
        @endif
        <div id="form-field-{{ $name }}" class="form-field" style="width: auto;">
            @if($field->label!="")
            <label for="{{ $name }}" class="block font-semibold">{{ $field->label }}{{ $field->is_required() ? '*' : '' }}</label>
            @endif
            @if($field->icon)
            <div class="icon-field">
                <span class="icon">
                    <iconify-icon icon="{{ $field->icon }}"></iconify-icon>
                </span>
            @endif
            
            {!! $field->render($name,$form->id) !!}
            @if($field->icon)
            </div>
            @endif
            <template x-if="errors['{{ $name }}']">
                <p class="form-error" x-text="errors['{{ $name }}']"></p>
            </template>
        </div>
    @endforeach
    @if($form->submit_text==!"")
    <button class="btn btn-light" @click="submit">
        {{ $form->submit_text }}
    </button>
    @endif
</div>
@if($form->redirect=="" && $form->popup=="")
@once('form-popup')
    <x-sharedutils::modal  id="form-success-popup" title="Datos guardados correctamente.">
        <p>Datos guardados correctamente.</p>
    </x-modal>
@endonce
@endif

@if(!empty($form->uploading_message))
@once('form-uploading-popup')
    <x-sharedutils::modal  id="form-uploading-popup">
        <p style="text-align:center">{{ $form->uploading_message }}</p>
    </x-modal>
@endonce
@endif

@push('scripts')
<script>
    function {{$form->id}}_submit() {
        return {
            form: {
                @if($form->initial_data != null)
                @foreach ($form->initial_data as $key => $value)
                {{ $key }}: '{{ $value }}',
                @endforeach
                @endif
            },
            errors: {},
            images: {},
            @if($form->has_images)
            // Second function
            storeImage(event) {
                this.images[event.target.id] = event.target.files[0];
            },
            
            @endif
            init(){
                @stack($form->id)
            },
            submit() {
                const urlParams = new URLSearchParams(window.location.search);
                for (const key of urlParams.keys()) {
                    this.form[key] = urlParams.get(key);
                }
                @if($form->has_images)
                var formData = new FormData();
                formData.append('test', 'test');
                Object.entries(this.form).forEach(([key, value]) => {
                    formData.append(key, value);
                });
                Object.entries(this.images).forEach(([key, file]) => {
                    formData.append(key, file);
                });
                @endif
                @if(!empty($form->uploading_message))
                openPopup("form-uploading-popup");
                @endif
                @if($form->submit_text==!"" || $form->is_autosave())
                $.ajax({
                    url: '{{ $form->submit_url==""? '/form/'.$form->id : $form->submit_url }}',
                    method: 'POST',
                    @if($form->has_images)
                    data: formData,
                    processData: false,
                    contentType: false,
                    @else
                    data: this.form,
                    @endif
                    success: (response) => {
                        @if($form->is_autosave())
                        @else

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
                        @endif

                        @if($form->reload)
                        location.reload();
                        @endif

                        this.errors = {};
                    },
                    error: (xhr) => {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            this.errors = xhr.responseJSON.errors;
                        } else {
                            this.errors = { general: 'An error occurred. Please try again.' };
                        }
                    },
                    @if(!empty($form->uploading_message))
                    complete: () => {
                        closePopup("form-uploading-popup");
                    }
                    @endif
                });
                @else
                $(document).trigger('submit-{{$form->id}}', this.form);
                @endif
            }
        }
    }
</script>
@endpush