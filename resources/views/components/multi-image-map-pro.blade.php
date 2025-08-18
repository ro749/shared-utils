

@push('script-includes')
<script src="vendor/shared-utils/js/image-map-pro.min.js"></script>
@endpush

@push('scripts')
<script>
    $(document).ready(function () {
        var maps = [];
        var current_floor;
        function get_floor(floor){
            current_floor = floor;
            $.ajax({
                url: "imagemappro/{{$imp->id}}/floor",
                method: 'GET',
                dataType: 'json',
                data: {floor: floor},
                success: function (response) {
                    ImageMapPro.init('#image-map-pro-floor',response);
                }
            });
        } 

        function get_unit(floor,type){
            $.ajax({
                url: "imagemappro/{{$imp->id}}/unit",
                method: 'GET',
                dataType: 'json',
                data: {
                    floor: floor,
                    type: type
                },
                success: function (response) {
                    $(document).trigger('selected-unit', [{ unit: event.target.getAttribute("data-title") }]);
                }
            });
        } 
        
        ImageMapPro.subscribe((action) =>{
            if(action.type == "mapInit"){
                maps.push(action.payload.map);
                if(ImageMapPro.isMobile()){
                    document.getElementById("image-map-pro").addEventListener("click", function(event) {
                        get_map(event.target.getAttribute("data-title"));
                    });
                }
            }
            if(action.type == "objectClick"){
                if(maps[0] == action.payload.map){
                    get_floor(action.payload.object);
                }
                else{
                    get_unit(current_floor,action.payload.object);
                }
                //get_map(action.payload.object);
            }
            if(ImageMapPro.isMobile() && action.type == "tooltipShow"){
                ImageMapPro.hideTooltip(action.payload.map, action.payload.object);
            }
            if(action.type == "artboardChange"){
                current_artboard = action.payload.artboard;
            }
        });

        $.ajax({
            url: "imagemappro/{{$imp->id}}/tower",
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                ImageMapPro.init('#image-map-pro-tower',response);
            }
        });
        
    });
</script>
@endpush