<div style="display:flex; flex-direction:row; justify-content:flex-end; gap:8px; margin-left:8px; align-items:center">
    <p style="margin:0">{{ $filter->display }}</p>
    @include('sharedutils::components.forms.selector', 
    [
        'selector' => $filter->selector, 
        'name' => 'cf-' . $filter->id, 
        'class' => 'category-filter', 
        'id' => 'cf-' . $filter->id]
        )
</div>