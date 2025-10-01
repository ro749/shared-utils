<x-sharedutils::modal id="{{ $class }}-warning-popup" onclose="closePopup('{{ $class }}-warning-popup');">
    <p id='{{ $class }}-warning' style='text-align:center'></p>
    <div style='display:flex; justify-content:space-evenly'>
        <button class='btn btn-danger-600' onclick='closePopup("{{ $class }}-warning-popup")'>Cancelar</button>
        <button class='btn btn-success-600' id='on-{{ $class }}'>Aceptar</button>
    </div>
</x-sharedutils::modal>