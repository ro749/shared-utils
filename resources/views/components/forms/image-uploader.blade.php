<input id="{{ $name }}" x-model="form.{{ $name }}" class="sr-only" type="file" accept="image/*" 
@if($field->autosave)
@change="storeImage($event); submit();"
@else
@change="storeImage($event);"
@endif
/>

@if($field->view)
@include($field->view, ["field"=>$field,"name"=>$name])
@endif