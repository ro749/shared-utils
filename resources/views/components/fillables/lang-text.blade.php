
<span class="lang-{{ $view }}-{{ $id }}">
    @if(!empty($data))
        {{ $data[$view]->{$id} }}
    @endif
</span>

