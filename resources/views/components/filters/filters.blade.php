<div id="{{ $filter->id }}" style="display:flex; flex-direction:row; justify-content:flex-start; gap:8px; margin-left:8px; align-items:center">
    <p style="margin:0">{{ $filter->display }}</p>
    @foreach($filter->filters as $key=>$column)
        <button id="btn_filter_{{ $key }}" class="btn btn-dark filter-button">{{ $column->display }}</button>
    @endforeach
</div>