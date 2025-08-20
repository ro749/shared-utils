<div id='image-map-pro'>

</div>

@push('script-includes')
<script src="vendor/shared-utils/js/image-map-pro.min.js"></script>
@endpush

@push('scripts')
<script>
    $(document).ready(function () {

        function get_unit(unit){
            $.ajax({
                url: "imagemappro/{{$imp->id}}/unit",
                method: 'GET',
                dataType: 'json',
                data: {unit: unit},
                success: function (response) {
                    $(document).trigger('selected-unit', [{ unit: response }]);
                }
            });
        } 

        ImageMapPro.subscribe((action) =>{
            if(action.type == "mapInit"){
                if(ImageMapPro.isMobile()){
                    document.getElementById("image-map-pro").addEventListener("click", function(event) {
                        get_unit(event.target.getAttribute("data-title"));
                    });
                }
            }
            if(action.type == "objectClick"){
                get_unit(action.payload.object);
            }
            if(ImageMapPro.isMobile() && action.type == "tooltipShow"){
                ImageMapPro.hideTooltip(action.payload.map, action.payload.object);
            }
            if(action.type == "artboardChange"){
                current_artboard = action.payload.artboard;
            }
        });

        $.ajax({
            url: "imagemappro/{{$imp->id}}/map",
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                ImageMapPro.init('#image-map-pro',response);
            }
        });
        
    });
</script>
@endpush