(function ($) {
    $.fn.smartTable = function (options = {}) {
        function style_unit(child,dispo){
            var color = colors[dispo[child["title"]]];
            if(color==undefined){
                color = "#ffffff";
            }
            if(child["title"].charAt(child["title"].length - 1)=="R"){
                color = colors[dispo[child["title"].slice(0, -1)]];
            }
            child["default_style"]["background_color"] = color;
            child["mouseover_style"] = {
                "opacity": 1,
                "background_type": "color",
                "background_color": color,
                "background_opacity": 0.8,
                "background_image_url": "",
                "background_image_opacity": 1,
                "background_image_scale": 1,
                "background_image_offset_x": 0,
                "background_image_offset_y": 0,
                "border_radius": 4,
                "border_width": 0,
                "border_style": "solid",
                "border_color": "#ffffff",
                "border_opacity": 1,
                "stroke_color": "#ffffff",
                "stroke_opacity": 0.75,
                "stroke_width": 0,
                "stroke_dasharray": "0",
                "stroke_linecap": "round",
                "icon_fill": "#000000",
                "parent_filters": [],
                "filters": []
            };
            if(child["default_style"]["background_opacity"] == 0){
                child["default_style"]["border_color"] = color;
            }
        }
        return this.each(function () {
            $.when(
                $.ajax({
                    url: "{{ route('image-map-pro') }}",
                    method: 'GET',
                    dataType: 'json'
                }),
                $.ajax({
                    url: "{{ route('image-map-pro-data') }}",
                    method: 'GET',
                    dataType: 'json'
                })
            ).done(function (map, data) {
                var dispo = [];
                for(var i in data[0]){
                    dispo[data[0][i]["unit"]] = data[0][i]["status"];
                }
                var artboards = map[0]["artboards"];
                for(var art in artboards){
                    var artboard = artboards[art];
                    for(var children in artboard["children"]){
                        var child = artboard["children"][children];
                        if(child["type"] == "group"){
                            for(var grandchildren in child["children"]){
                                style_unit(child["children"][grandchildren],dispo);
                            }
                        }
                        else{
                            style_unit(child,dispo);
                        }
                    }
                }
                ImageMapPro.init('#image-map-pro',map[0]);
            });
        });
    };
})(jQuery);