<input id="{{ $name }}" x-model="form.{{ $name }}" class="sr-only" type="file" accept="image/*" 
@if($field->autosave)
@change="storeFile($event); submit();"
@else
@change="storeFile($event);"
@endif
/>

@if($field->view)
@include($field->view, ["field"=>$field,"name"=>$name,"data"=>$data])
@endif
