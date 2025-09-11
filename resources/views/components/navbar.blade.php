<div class="navbar-header" style="display:flex; align-items: center; justify-content: space-between;">
    <button type="button" class="sidebar-mobile-toggle">
            <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
    </button>
    @if(!empty($user))
    <div style="display:flex; align-items: center; gap: 8px;">
        <p>{{ $user->name }}</p>
        <button style="width: 100%;" class="d-flex justify-content-center align-items-center rounded-circle" type="button" id="pfp">
            <img src="
            @if(!empty($user->pfp))
            {{ asset('storage/uploads/' . $user->pfp) }}
            @else
            https://propstudios.mx/img/default_user.jpeg
            @endif
            " alt="image" class="w-40-px h-40-px object-fit-cover rounded-circle">
        </button>
    </div>
    @endif
    
</div>