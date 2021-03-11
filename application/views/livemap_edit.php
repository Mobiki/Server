<?php $this->load->view('layout/up') ?>

<?php $user_data = $this->session->userdata('userdata'); ?>

<link rel="stylesheet" href="<?php echo base_url("assets/css/app.css"); ?>">
<script src="https://npmcdn.com/leaflet@1.3.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://leaflet.github.io/Leaflet.draw/docs/examples/libs/leaflet.css" />

<script src="https://leaflet.github.io/Leaflet.draw/src/Leaflet.draw.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/Leaflet.Draw.Event.js"></script>
<link rel="stylesheet" href="https://leaflet.github.io/Leaflet.draw/src/leaflet.draw.css" />

<script src="https://leaflet.github.io/Leaflet.draw/src/Toolbar.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/Tooltip.js"></script>

<script src="https://leaflet.github.io/Leaflet.draw/src/ext/GeometryUtil.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/ext/LatLngUtil.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/ext/LineUtil.Intersect.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/ext/Polygon.Intersect.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/ext/Polyline.Intersect.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/ext/TouchEvents.js"></script>

<script src="https://leaflet.github.io/Leaflet.draw/src/draw/DrawToolbar.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/draw/handler/Draw.Feature.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/draw/handler/Draw.SimpleShape.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/draw/handler/Draw.Polyline.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/draw/handler/Draw.Marker.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/draw/handler/Draw.Circle.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/draw/handler/Draw.CircleMarker.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/draw/handler/Draw.Polygon.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/draw/handler/Draw.Rectangle.js"></script>


<script src="https://leaflet.github.io/Leaflet.draw/src/edit/EditToolbar.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/edit/handler/EditToolbar.Edit.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/edit/handler/EditToolbar.Delete.js"></script>

<script src="https://leaflet.github.io/Leaflet.draw/src/Control.Draw.js"></script>

<script src="https://leaflet.github.io/Leaflet.draw/src/edit/handler/Edit.Poly.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/edit/handler/Edit.SimpleShape.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/edit/handler/Edit.Rectangle.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/edit/handler/Edit.Marker.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/edit/handler/Edit.CircleMarker.js"></script>
<script src="https://leaflet.github.io/Leaflet.draw/src/edit/handler/Edit.Circle.js"></script>

<script type="text/javascript" language="javascript" src="<?php echo base_url("assets/js/osm_geojson.js"); ?>"></script>

<?php
$floorid = 0;
if (isset($_GET["floorid"])) {
    $floorid = $_GET["floorid"];
}

?>

<article class="content responsive-tables-page">
    <section class="section">
        <?php if ($user_data["role_id"] == 1) { ?>
            <div class="row">
                <?php if ($user_data["role_id"] == 1) { ?>
                <?php } ?>
                <div class="col-md-2">
                    <a href="<?php echo base_url("livemap"); ?>" type="button" class="btn btn-primary btn-sm">Live map</a>
                </div>

                <div class="col-md-2">
                    <button id="save" onclick="save()">save</button>
                </div>
                <div class="col-md-2">
                    <select id="floor_id">
                        <option value="-1" <?php echo $floorid == "-1" ? "selected" : ""; ?>>Outside</option>
                        <option value="0" <?php echo $floorid == "0" ? "selected" : ""; ?>>Ground floor</option>
                    </select>
                </div>
            </div>
            <br>
        <?php } ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-block">
                        <section class="example">
                            <div class="table-responsive">
                                <div id="map" style="height: 700px;"></div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </section>
</article>

<?php $this->load->view('layout/down') ?>

<script type="text/javascript" language="javascript" src="<?php echo base_url("assets/js/jquery-3.3.1.js"); ?>"></script>

<script type="text/javascript" language="javascript">
    var xml = `<?php print_r($xml); ?>`;
    //floor_id floor_xml

    //http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png
    //https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw

    var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var osmAttrib = 'Mobiki';

    var draw_floor = L.tileLayer(osmUrl, {
        maxZoom: 19,
        attribution: osmAttrib
    });

    var building_latLong = new L.LatLng(40.9194102, 29.315826); // building_latLong
    var map = new L.Map('map', {
        center: building_latLong,
        zoom: 30,
        layers: [
            draw_floor
        ]
    });
    map._layersMaxZoom = 40;
    map._layersMinZoom = 10;
    var layer_floor = L.featureGroup().addTo(map);

    drawMap();

    map.addControl(new L.Control.Draw({
        edit: {
            featureGroup: layer_floor,
            /*poly: {
                allowIntersection: true
            }*/
        },
        draw: {
            featureGroup: layer_floor,
            /*polygon: true,
            rectangle: true,
            circle: true,
            polyline: true,*/
        }
    }));


    map.on(L.Draw.Event.CREATED, function(event) {
        var layer = event.layer,
            feature = layer.feature = layer.feature || {};
        feature.type = feature.type || "Feature";
        var props = feature.properties = feature.properties || {};

        layer.bindPopup("")
            .on('dblclick', function(e) {
                var cam_name = prompt("Camera name:", layer._popup._content);
                this._popup.setContent(cam_name);
            });
        layer_floor.addLayer(layer);
        //addProp(layer);
        console.log(layer);
    });
    /* function addProp(layer) {
    	layer.feature.properties.desc = "test";
};*/

    //---------------------------------------------
    function drawMap() {
        var geojson_data = osm_geojson.osm2geojson(xml);

        //console.log(JSON.stringify(geojson_data));

        //var geojson_layer = L.geoJson(geojson_data);
        //geojson_layer.addTo(layer_floor);

        var latLong = [];

        geojson_data.features.forEach(function(currentFeature) {
            var type = currentFeature.geometry.type;
            var coordinates = currentFeature.geometry.coordinates;
            var properties = currentFeature.properties;
            if (type == "LineString") {
                coordinates.forEach(function(locations) {
                    //locations.forEach(function(location) {
                    latLong.push([locations[1], locations[0]]);
                    //});
                });
                var polyline = L.polyline(latLong).bindPopup(JSON.stringify(properties));
                feature = polyline.feature = polyline.feature || {};
                feature.type = feature.type || "Feature";
                var props = feature.properties = feature.properties || {};
                feature.properties = properties;
                polyline.addTo(layer_floor);
                latLong = [];
            }

            /*if (type == "Polygon") {
                coordinates.forEach(function(locations) {
                    locations.forEach(function(location) {
                        latLong.push([location[1], location[0]]);
                    });
                });
                var polygon = L.polygon(latLong).bindPopup(JSON.stringify(properties));
                feature = polygon.feature = polygon.feature || {};
                feature.type = feature.type || "Feature";
                var props = feature.properties = feature.properties || {};
                feature.properties = properties;
                polygon.addTo(layer_floor);
                latLong = [];
            }*/

            if (type == "Point") {
                var marker = L.marker([coordinates[1], coordinates[0]]).bindPopup(JSON.stringify(properties))
                    .on('dblclick', function(e) {
                        var prop = prompt("Name:", "");
                        this._popup.setContent(prop);
                    });
                feature = marker.feature = marker.feature || {};
                feature.type = feature.type || "Feature";
                var props = feature.properties = feature.properties || {};
                feature.properties = properties;
                marker.addTo(layer_floor);
                //console.log(marker);
            }
        });

    };

    function getxml(xmlfile) {
        return $.ajax({
            type: "GET",
            url: xmlfile,
            async: false
        }).responseText;
    };

    function save() {
        //var shape = layer.toGeoJSON();
        //console.log(JSON.stringify(layer_floor.toGeoJSON()));
        geojson_data = layer_floor.toGeoJSON();
        var osm_data = osm_geojson.geojson2osm(geojson_data);
        console.log(osm_data);

        /*map.eachLayer(function(layer) {
            //var shape = layer.toGeoJSON();
            console.log(layer);
        });*/

        /*$.each(map._layers, function (ml) {
                console.log(JSON.stringify(ml.toGeoJSON()))  
        })*/

        /*var floor_markers = [];

        map.eachLayer(function(layer) {
            if (layer instanceof L.Marker) {
                var marker = {};
                marker.name = layer._popup._content;
                marker.lat = layer._latlng.lat;
                marker.lon = layer._latlng.lng;
                marker.mac = layer._popup._content;
                marker.floorid = <?php echo $floorid; ?>;
                floor_markers.push(marker);
            }
        });

        $.post("<?php echo base_url("map/save") ?>", {
                'building_id': 101,
                'floor_id': <?php echo $floorid; ?>,
                'data': floor_markers,
            }, function(data) {
                //console.log( data );
            }).done(function(data) {
                //alert( "second success" );
                console.log(data);
            })
            .fail(function(data) {
                //alert( "error" );
                console.log(data);
            }).always(function() {
                location.reload();
            });*/
    }
</script>