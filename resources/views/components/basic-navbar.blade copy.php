

<div class="collapse navbar-collapse bg-dark" id="navbarContent">
    <ul class="navbar-nav ms-auto">
        <li class="nav-item" style="margin-right: 6px;">
            @foreach($items as $item)
            <button class="btn btn-light" onclick="window.location='{{ $item['url'] }}'">
                {{ $item['label'] }}
            </button>
            @endforeach
        </li>
    </ul>
</div>
