@props(['element' => null, 'name' => ''])

<input id="{{ $name }}" x-model="form.{{ $name }}" class="sr-only" type="file" accept="image/*" 
@if($element->autosave)
@change="storeFile($event); submit();"
@else
@change="storeFile($event);"
@endif
/>

@if($element->view)
@include($element->view, ["field"=>$element,"name"=>$name,"data"=>$element->data])
@endif
