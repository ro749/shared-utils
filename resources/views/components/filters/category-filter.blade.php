<div style="display:flex; flex-direction:row; justify-content:flex-end; gap:8px; margin-left:8px; align-items:center">
    <p style="margin:0">{{ $filter->display }}</p>
    <x-sharedutils::selector :selector="$filter->selector" class="category-filter" id="category-filter-{{ $filter->id }}"/>
</div>