<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('sharedutils::components.title')
<meta name="theme-color" content="#ffffff"/>
@if(!empty(config('app.pwa')))
<link rel="apple-touch-icon" href="https://propstudios.mx/img/Verdant/Logo%20e%20Icono/Icono.png">
<link rel="manifest" href="{{ asset('/manifest.json') }}">
@push('scripts')
<script src="{{ asset('/sw.js') }}"></script>
<script>
   if ("serviceWorker" in navigator) {
      // Register a service worker hosted at the root of the
      // site using the default scope.
      navigator.serviceWorker.register("/sw.js").then(
      (registration) => {
         console.log("Service worker registration succeeded:", registration);
      },
      (error) => {
         console.error(`Service worker registration failed: ${error}`);
      },
    );
  } else {
     console.error("Service workers are not supported.");
  }
</script>
<script src="https://unpkg.com/phidget22@3.x/browser/phidget22.min.js"></script>
<script>
    
  (async () => {
			try {
				const conn = new phidget22.NetworkConnection(8989, 'localhost');
				await conn.connect();

				const rfid0 = new phidget22.RFID();

				rfid0.onTag = (tag, protocol) => {
					$(document).trigger('rfid', [tag]);
				};
				//rfid0.onTagLost = (tag, protocol) => {
				//	tagLostDataEl.textContent = '\tTag: ' + tag.toString() + '\n';
				//};
//
				await rfid0.open(5000);
                console.log('connected');
				//statusEl.textContent = 'Connected';

			} catch(err) {
				console.error('Phidget error:', err);
				//statusEl.textContent = '⚠️ ' + err.message;
			}
		})();
</script>
@endpush
@endif
@push('styles')
    @include('shared-utils::components.style')
@endpush
@push('script-includes')
    @include('shared-utils::components.scripts')
    <script enum="text/javascript" src="{{ asset('js/options.js') }}"></script>
@endpush
@push('scripts')
<script>
</script>
<script>
function openPopup(id,time=0){
    $('#'+id).show();
    document.documentElement.style.overflowY ="hidden";
    if(time!=0){
        setTimeout(function(){closePopup(id)},time);
    }
}

function closePopup(id){
    $('#'+id).hide();
    document.documentElement.style.overflowY ="scroll";
}

</script>
@endpush