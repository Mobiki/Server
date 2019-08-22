$(function() {
    var map, svg, g;
    map = new L.Map('map-2d').setView(new L.LatLng(0,0), 10);
    map.attributionControl.setPrefix('Mobiki Indoor Navigation');
    svg = d3.select(map.getPanes().overlayPane).append('svg');
    g = svg.append('g');
    _circle = svg.append('g');
    map._layersMaxZoom  = 22;
    map._layersMinZoom  = 18;
    var jsonOutside;
    var _current_json;
    var _currentType;

    //<editor-fold desc="Step 1: Trigger">
    var trigger = setTimeout(function(){
        if($('#floorPlan a').length > 0){
            $('#floorPlan a.floorListClick2:first-child').trigger('click');
        }
        clearTimeout(trigger);
    }, 500);
    //</editor-fold>

    //<editor-fold desc="Step 2: Is Floor Changed">
    $(document).on('click', '.floorListClick2', function (event) {
        /*App.blockUI({
            message : "Yükleniyor...",
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                color: '#fff'
            }
        });*/
        event.preventDefault();

        $('#floorPlan .floorListClick2').removeClass('active');
        $(this).addClass('active');

        level  = Number($(this).data('value'));
        var floorLevel   = 'http://localhost/mobiki.in/floor/xml/%7BL%7D';
        var beacon = 'http://localhost/mobiki.in/beacon/getJsonData/%7BL%7D';

        var floorUrl    = decodeURIComponent(floorLevel + '?type=p');
        floorUrl        = floorUrl.replace('{L}', level);

        var beaconUrl = decodeURIComponent(beacon);
        beaconUrl = beaconUrl.replace('{L}', level);

        queue()
            .defer(d3.xml, floorUrl, 'application/xml')
            .defer(d3.json, beaconUrl)
            .await(json_file)
        //setTimeout(App.unblockUI, 1000);
    });
    //</editor-fold>

    //<editor-fold desc="Strp 3: Draw Map">
    function json_file(error, file, beacon_file){

        if(error){
            alert('File XML Error Problem');
            return false;
        }

        jsonOutside = collection = osm_geojson.osm2geojson(file);

        var _tmp_features = [];
        jsonOutside.features.forEach(function(x, y){
            if(
                typeof x.geometry.type !== 'undefined'
                && x.geometry.type == 'LineString'
                && typeof x.geometry.coordinates !== 'undefined'
                && x.geometry.coordinates.length >= 3
            )
            {
                _tmp_features.push(x);
            }
        });

        var _new_count = jsonOutside.features.length + 1;

        var _tmp_circle_features = [];

        if(beacon_file && beacon_file.length > 0){

            beacon_file.forEach(function(v, k){

                _new_count++;

                var _t_feature = {
                    geometry    : {
                        coordinates : v.location.split(',', 2).reverse(),
                        type        : 'Point'
                    },
                    id          : _new_count,
                    properties  : [_new_count],
                    type        : 'Feature'
                };

                _tmp_circle_features.push(_t_feature);
            });

        }

        if(_tmp_features && _tmp_features.length > 0)
            jsonOutside.features = _tmp_features;

        jsonWayOutside = {
            type        : 'FeatureCollection',
            features    : _tmp_circle_features
        };

        feature = null;
        circle = null;

        d3.selection.prototype.moveToBack = function() {
            return this.each(function() {
                var firstChild = this.parentNode.firstChild;
                if (firstChild) {
                    this.parentNode.insertBefore(this, firstChild);
                }
            });
        };

        d3.selection.prototype.moveToFront = function() {
            return this.each(function(){
                this.parentNode.appendChild(this);
            });
        };

        bounds = d3.geo.bounds(jsonOutside);

        if(file){

            g.selectAll('path').remove();
            _circle.selectAll('circle').remove();

            grads = _circle.append("defs").selectAll("radialGradient").data(_tmp_circle_features)
                .enter().append("radialGradient")
                .attr("id", function(d, i) { return "grad" + i; });

            grads.append("stop").attr("offset", "30%").style("stop-opacity", 0.8).style("stop-color", "#287AA9");
            grads.append("stop").attr("offset", "100%").style("stop-opacity", 0).style("stop-color", "white");

            circle = _circle.selectAll('circle').data(_tmp_circle_features)
                .enter().append("circle")
                .attr("fill", function(d, i) { return "url(#grad" + i + ")"; })
                .attr('r', 25);

            feature = g.selectAll('path').data(jsonOutside.features).enter().append('path');

            g.selectAll('path').style("fill", function (d) {

                if(d.properties !== null && typeof d.properties.type !== 'undefined' && d.properties.type == 'group')
                    d3.select(this).moveToBack();

                if( d.properties !== null && typeof d.properties.color !== 'undefined'){
                    var v = d.properties.color.toString().replace('#', '');
                    return '#' + v;
                }
                else
                    return '';

            });
        }

        fitMap(bounds);

        var _project = function(x){
            var point;
            point = map.latLngToLayerPoint(new L.LatLng(x[1], x[0]));
            return [point.x, point.y];
        };

        auto_checked_colorized();

        var _reset = function(){
            var bottomLeft, topRight;
            bottomLeft = _project(bounds[0]);
            topRight = _project(bounds[1]);

            svg.attr('width', topRight[0] - bottomLeft[0]).attr('height', bottomLeft[1] - topRight[1]).style('margin-left', bottomLeft[0] + 'px').style('margin-top', topRight[1] + 'px');
            g.attr('transform', 'translate(' + -bottomLeft[0] + ',' + -topRight[1] + ')');
            _circle.attr('transform', 'translate(' + -bottomLeft[0] + ',' + -topRight[1] + ')');
        };

        _reset();

        var featuresOverlay = L.d3SvgOverlay(function(sel,proj){

            var project = function(x){
                point = proj.latLngToLayerPoint(new L.LatLng(x[1], x[0]));
                return [point.x, point.y];
            };

            path = d3.geo.path().projection(project);

            circle.attr("cx", function (d) {
                return proj.latLngToLayerPoint(new L.LatLng(d.geometry.coordinates[1], d.geometry.coordinates[0])).x;
            })
                .attr("cy", function (d) {
                    return proj.latLngToLayerPoint(new L.LatLng(d.geometry.coordinates[1], d.geometry.coordinates[0])).y;
                });

            feature.attr('d', path);
        });

        featuresOverlay.addTo(map);
    }
    //</editor-fold>

    //<editor-fold desc="Step 4: Fit Map">
    function fitMap(bounds){
        var southWest = L.latLng(bounds[0][1], bounds[0][0]),
            northEast = L.latLng(bounds[1][1], bounds[1][0]),
            neigh_bounds = L.latLngBounds(southWest, northEast);
        map.fitBounds(neigh_bounds);
    }
    //</editor-fold>

    //<editor-fold desc="Step 5: Colorize Building">
    function auto_checked_colorized() {
        g.selectAll('path').style("fill", function (d) {

            var _color = '';
            if( d.properties !== null && typeof d.properties.color != 'undefined'){
                var v = d.properties.color.toString().replace('#', '');
                _color = "#" + v;
            }

            if ( typeof _current_json !== 'undefined' && typeof _current_json.id !== 'undefined' && _current_json.id == d.id && typeof _currentType !== 'undefined' && _currentType == 'p') {
                _color = '#A52A2A';
            }

            return _color;
        });
    }
    //</editor-fold>

});