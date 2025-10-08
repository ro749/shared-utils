@props(['form' => null])
@php
$initial_data = $form->get_initial_data();
@endphp

<div x-data="{{$form->get_id()}}_submit()" id="{{ $form->get_id() }}"  {{ $attributes }}>
@if (!$slot->isEmpty())
{{ $slot }}
@else
    @foreach ($form->fields as $name => $field)
        @if($field->type === Ro749\SharedUtils\Forms\InputType::HIDDEN)
        @continue
        @endif
        
        <x-field :name="$name" :form="$form"/>
        
    @endforeach
    
    @if($form->submit_text==!"")
    <button class="btn btn-light" @click="submit">
        {{ $form->submit_text }}
    </button>
    @endif
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

@if(!empty($form->popup))
@include($form->popup, ['class' => $form->get_id()])
@endif

@push('scripts')
<script>
    function {{$form->get_id()}}_submit() {
        return {
            form: {
                @if($form->get_initial_data() != null)
                @foreach ($form->get_initial_data() as $key => $value)
                {{ $key }}: `{{ $value }}`,
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
                @stack($form->get_id())
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
                    url: '{{ $form->submit_url==""? '/form/'.$form->get_id() : $form->submit_url }}',
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
                        @elseif(!empty($form->popup))

                        let success = '{{ $form->success_msg ?? 'Datos guardados correctamente.' }}';
                        const matches = [...success.matchAll(/\{(.*?)\}/g)];
                        const args = matches.map(match => match[1].trim());
                        for (const arg of args) {
                            success = success.replace('{' + arg + '}', this.form[arg]);
                        }
                        $('#{{ $form->get_id() }}-success').text(success);

                        openPopup("{{ $form->get_id() }}-success-popup");
                        @elseif($form->callback)
                        {!! $form->callback !!}
                        @else
                        openPopup("form-success-popup",2500);
                        @endif
                        for (const key in this.form) {
                            this.form[key] = '';
                        }
                        @stack($form->get_id().'_reset')
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
                $(document).trigger('submit-{{$form->get_id()}}', this.form);
                @endif
            }
        }
    }
</script>
@endpush