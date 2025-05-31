<div class="modal" id="{{ $id }}" style="background-color: rgb(0,0,0); background-color: rgba(0, 0, 0, 0.4); ">
    <div class="card popup" style="background-color: rgba(0, 0, 0, 90%); max-height: 90%; margin: 0; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);" >
        <div class="card-header" style="border: 0px; padding-bottom:0;">
            <span class="close" id ="close'.$id.'span" style="color: #aaaaaa; margin-left: 95%; margin-top: 0%; cursor: pointer;" onclick="{{ isset($onclose)?$onclose:"" }} closePopup('{{ $id }}')">&times;</span>
        </div>
        <div class="card-body popupbody" style="position: relative; overflow-y: hidden;overflow-x: hidden; padding-top:0;">
            {{ $slot }}
        </div>
    </div>
</div>