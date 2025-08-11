<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a  class="sidebar-logo">
            <img src="" alt="site logo" class="light-logo">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            @foreach ($items as $item)
                <li>
                    <a href="{{ $item['url'] }}">
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>   
    </div>
</aside>

@push('scripts')
<script>
$(".sidebar-mobile-toggle").on("click", function(){
  $(".sidebar").addClass("sidebar-open");
  $("body").addClass("overlay-active");
});
$(".sidebar-close-btn").on("click", function(){
  $(".sidebar").removeClass("sidebar-open");
  $("body").removeClass("overlay-active");
});
</script>
@endpush