<div class="modal" id="{{ $id }}" style=" background-color: rgba(0, 0, 0, 0.4); ">
    <div class="card popup" style="max-height: 90%; margin: 0; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);" >
        <div class="card-header popup-header" style="border: 0px; padding-bottom:0; display: flex; justify-content: end;">
        <iconify-icon  id ="close'.$id.'span" icon="iconamoon:close-light" style="color: #000000ff; cursor: pointer;" onclick="{{ isset($onclose)?$onclose:"" }} closePopup('{{ $id }}')"></iconify-icon>
        </div>
        <div class="card-body popupbody" style="position: relative; overflow-y: hidden;overflow-x: hidden; padding-top:0; color:black; !important">
            {{ $slot }}
        </div>
    </div>
</div>