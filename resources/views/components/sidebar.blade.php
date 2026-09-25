<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    @if(!empty($logo))
    <div>
        <a  class="sidebar-logo">
            <img src="{{ $logo }}" alt="site logo" class="light-logo">
        </a>
    </div>
    @endif
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
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".sidebar-mobile-toggle").forEach(btn => {
    btn.addEventListener("click", () => {
      document.querySelector(".sidebar").classList.add("sidebar-open");
      document.body.classList.add("overlay-active");
    });
  });

  document.querySelectorAll(".sidebar-close-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      document.querySelector(".sidebar").classList.remove("sidebar-open");
      document.body.classList.remove("overlay-active");
    });
  });
});
</script>
@endpush