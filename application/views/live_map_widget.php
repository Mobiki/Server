<style>
    body {
        margin: 0px;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url("assets/css/app.css"); ?>">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css" integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ==" crossorigin="*" />

<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw==" crossorigin="*"></script>

<!--link rel="stylesheet" href="/css/leaflet.css"
          integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
          crossorigin="*"/>
    <script src="/js/leaflet.js"
            integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
            crossorigin="*"></script-->

<article class="content responsive-tables-page">
    <section class="section">
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


<script type="text/javascript" src="<?php echo base_url("assets/js/socket.io.js"); ?>"> </script>
<script type="text/javascript" language="javascript" src="<?php echo base_url("assets/js/jquery-3.3.1.js"); ?>"></script>

<script type="text/javascript" language="javascript">

    var datatableObj = [];
    var Marker = [];

    <?php //foreach ($gwList as $row){ 
    ?>
    /*datatableObj.push(
        {
            "isActive":"{{$row['isActive']}}"
            ,"name":"{{$row['name']}}"
            ,"mac":"{{$row['mac']}}"
            ,"lat":"{{$row['lat']}}"
            ,"lon":"{{$row['lon']}}"
        }
    );*/
    datatableObj.push({
        "isActive": "active",
        "name": "ofice",
        "mac": "ac233fc00629",
        "lat": "40.919807",
        "lon": "29.315383"
    });
    datatableObj.push({
        "isActive": "active",
        "name": "4A Giriş",
        "mac": "ac233fc00676",
        "lat": "40.919676",
        "lon": "29.315186"
    });
    <?php //} 
    ?>



    class Map {
        constructor() {
            this.map = L.map('map', {
                renderer: L.svg({
                    padding: 100 //değer haritanın düzgün bir şekilde renderlanabilmesi için çok önemli
                })
            }).setView([40.9191102, 29.315526], 40)
            //}).setView([40.9882717,28.8259847], 18)
            L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
                attribution: 'Mobiki',
                id: 'mapbox.streets',
            }).addTo(this.map)

            this.markers = L.layerGroup()
            this.setZooms()
            this.drawMap()
        }


        setZooms() {
            // zoomları sınırlayan fonksiyon
            this.map._layersMaxZoom = 40
            this.map._layersMinZoom = 10
        }

        setBounds(bounds) {
            // haritanın çok fazla scrollanmasını engellemek için sınırlarının belirlendiği kısım
            let limits = bounds
            limits._northEast.lat += .0001
            limits._northEast.lng += .01
            limits._southWest.lat -= .0001
            limits._southWest.lng -= .01
            this.map.fitBounds(bounds)
            this.map.setMaxBounds(limits)
        }


        addMarker(data) {
            /*
                daha kiisel bir marker için leafletin sitesinde custom markerlar var.
                ben şimdilik sadece pozisyon ve deneme bir popupu ve üzerinde veri gösterilen bir marker ekliyorum.
                gerisi senin keyfine kalmış.
            */
            //debugger;
            const {
                location,
                lat,
                lng,
                personName,
                gw_name
            } = data

            // lokasyonu da parçaladım. keyfine bağlı nasıl kullanacağın.
            //const parsedLocation = location.split('#')
            //const lat = parsedLocation[0]
            //const lng = parsedLocation[1]
            //const lat = lat
            //const lng = lng

            // şuan bulunan markerler içerisinde ada göre arama yapıyorum. ad şimdilik eşsiz değer olarak kullanılıyor. başka daha mantıklı bir eşsiz değer kullanılabilir.
            let isExist = this.markers.getLayers().find((layer) => layer.options.name == personName)

            // bu eşsiz değere sahip layer bulunuyorsa ve bu eşsiz değere sahip layerden lat lnglerden biri değişmiş ise if true döner
            if (isExist && (isExist._latlng.lat != lat || isExist._latlng.lng != lng)) {
                // bu durumda bu layerin konumunu güncelleriz
                isExist.setLatLng(new L.LatLng(lat, lng))
            } else if (!isExist) {
                // eğer layer hiç mapa eklenmemişse yeni bir layer(marker) eklenir

                let marker = new L.Marker([lat, lng], {
                    name: personName //identifer olarak kullanıyorum. daha mantıklı eşsiz bir değişken kullanılabilir
                }).bindPopup(personName + ' - ' + gw_name).addTo(this.markers)
                this.markers.addTo(this.map)
                marker.openPopup()
            }
        }

        drawMap() {
            // burada da bayağı bir şey dönüyor. loglayarak ne nedir bulnuabilir yada konuşabiirz üerinde.
            let data = RealTimePage.osm2geojson(RealTimePage.xml)

            let geoJsonLayer = L.geoJson(data, {
                style: (feature) => {
                    let color = feature.properties.color
                    return {
                        color: '#' + color,
                        fill: '#' + color,
                        fillOpacity: 1,
                        opacity: 1
                    }
                },
                filter: (feature, layer) => (
                    (feature.geometry.type === 'LineString' && typeof feature.geometry.coordinates !== 'undefined' && feature.geometry.coordinates.length >= 3 && typeof feature.geometry.type !== 'undefined')
                ),
                onEachFeature: (feature, layer) => {
                    layer.on({
                        //click: () => alert(feature.properties.id), //tıklayınca id
                    })
                }
            })
            geoJsonLayer.addTo(this.map)
            //this.setBounds(geoJsonLayer.getBounds())
        }
    }

    var RealTimePage = {

        //xml : `{!!$xml!!}`,

        xml: `<?php print_r($xml); ?>`,
        mymap: undefined,
        socket: undefined,

        /*maker1: undefined,
        maker2: undefined,
        maker3: undefined,*/

        load: function() {
            //RealTimePage.cometConnect('ac');
        },
        /*getComet: function() {
            RealTimePage.socket.on('news', function(msg) {

                var gelen = JSON.parse(msg);

                if (gelen.GW_MAC != '') {
                    $.each(datatableObj, function(indx, datasi) {

                        if (datasi.mac.toUpperCase() == gelen.GW_MAC) {
                            debugger;

                            if (RealTimePage.maker1 == undefined)
                                RealTimePage.maker1 = L.marker([datasi.lat, datasi.lon]).addTo(this.map);
                            else if (RealTimePage.maker2 == undefined)
                                RealTimePage.maker2 = L.marker([datasi.lat, datasi.lon]).addTo(this.map);

                        }

                    });
                }
            });
        },*/

        /*cometConnect: function(islem) {
            if (islem != 'ac') {
                if (RealTimePage.socket == undefined)
                    return;

                RealTimePage.socket.disconnect();
            } else {
                if (true)
                    RealTimePage.socket = io('https://realtime.mobiki.link:3000');
                else
                    RealTimePage.socket = io('http://78.46.112.41:3000');
                //RealTimePage.socket = io('http://realtime.mobiki.link:8081/');

                RealTimePage.socket.on('connect', function() {});
                RealTimePage.socket.on('event', function(data) {
                    debugger;
                });
                RealTimePage.socket.on('disconnect', function() {});

                RealTimePage.getComet();
            }

            RealTimePage.socket.on('connect_error', function() {
                //$('#statusSpan').html('Bağlantı tekrar deneniyor.');
            });
        },*/


        osm2geojson: function(osm, metaProperties) {
            var count = 0;
            var xml = parse(osm),
                usedCoords = {},
                nodeCache = cacheNodes(),
                wayCache = cacheWays();

            return Bounds({
                type: 'FeatureCollection',
                features: []
                    .concat(Ways(wayCache))
                    .concat(Ways(Relations))
                    .concat(Points(nodeCache))
            }, xml);

            function parse(xml) {
                if (typeof xml !== 'string') return xml;
                return (new DOMParser()).parseFromString(xml, 'text/xml');
            }

            function Bounds(geo, xml) {
                var bounds = getBy(xml, 'bounds');
                if (!bounds.length) return geo;
                geo.bbox = [
                    attrf(bounds[0], 'minlon'),
                    attrf(bounds[0], 'minlat'),
                    attrf(bounds[0], 'maxlon'),
                    attrf(bounds[0], 'maxlat')
                ];
                return geo;
            }

            function setProperties(element) {
                if (!element) return {};

                var props = {},
                    tags = element.getElementsByTagName('tag'),
                    tags_length = tags.length;

                for (var t = 0; t < tags_length; t++) {
                    props[attr(tags[t], 'k')] = attr(tags[t], 'v');
                }

                if (metaProperties) {
                    setIf(element, 'id', props, 'osm_id');
                    setIf(element, 'user', props, 'osm_lastEditor');
                    setIf(element, 'version', props, 'osm_version', true);
                    setIf(element, 'changeset', props, 'osm_lastChangeset', true);
                    setIf(element, 'timestamp', props, 'osm_lastEdited');
                }

                return sortObject(props);
            }

            function getFeature(element, type, coordinates) {
                return {
                    geometry: {
                        type: type,
                        coordinates: coordinates || []
                    },
                    type: 'Feature',
                    id: ++count,
                    properties: setProperties(element)
                };
            }

            function cacheNodes() {
                var nodes = getBy(xml, 'node'),
                    coords = {};

                for (var n = 0; n < nodes.length; n++) {
                    coords[attr(nodes[n], 'id')] = nodes[n];
                }

                return coords;
            }

            function Points(nodeCache) {
                let features = [];

                for (var node in nodeCache) {
                    var tags = getBy(nodeCache[node], 'tag');
                    if (!usedCoords[node] || tags.length)
                        features.push(getFeature(nodeCache[node], 'Point', lonLat(nodeCache[node])));
                }

                return features;
            }

            function cacheWays() {
                var ways = getBy(xml, 'way'),
                    out = {};

                for (var w = 0; w < ways.length; w++) {
                    var feature = {},
                        nds = getBy(ways[w], 'nd');

                    /*if (attr(nds[0], 'ref') === attr(nds[nds.length - 1], 'ref')) {
                        feature = getFeature(ways[w], 'Polygon', [[]]);
                    } else {
                        feature = getFeature(ways[w], 'LineString');
                    }*/

                    feature = getFeature(ways[w], 'LineString');

                    for (var n = 0; n < nds.length; n++) {
                        var node = nodeCache[attr(nds[n], 'ref')];
                        if (node) {
                            var cords = lonLat(node);
                            usedCoords[attr(nds[n], 'ref')] = true;
                            if (feature.geometry.type === 'Polygon') {
                                feature.geometry.coordinates[0].push(cords);
                            } else {
                                feature.geometry.coordinates.push(cords);
                            }
                        }
                    }
                    out[attr(ways[w], 'id')] = feature;
                }
                return out;
            }

            function Relations() {
                var relations = getBy(xml, 'relation'),
                    relations_length = relations.length,
                    features = [];

                for (var r = 0; r < relations_length; r++) {
                    var feature = getFeature(relations[r], 'MultiPolygon');

                    if (feature.properties.type === 'multipolygon') {
                        var members = getBy(relations[r], 'member');

                        // osm doesn't keep roles in order, so we do this twice
                        for (var m = 0; m < members.length; m++) {
                            if (attr(members[m], 'role') === 'outer') assignWay(members[m], feature);
                        }

                        for (var n = 0; n < members.length; n++) {
                            if (attr(members[n], 'role') === 'inner') assignWay(members[n], feature);
                        }

                        delete feature.properties.type;
                    } else {
                        // http://taginfo.openstreetmap.us/relations
                    }

                    if (feature.geometry.coordinates.length) features.push(feature);
                }

                return features;

                function assignWay(member, feature) {
                    var ref = attr(member, 'ref'),
                        way = wayCache[ref];

                    if (way && way.geometry.type === 'Polygon') {
                        if (way && attr(member, 'role') === 'outer') {
                            feature.geometry.coordinates.push(way.geometry.coordinates);
                            if (way.properties) {
                                // exterior polygon properties can move to the multipolygon
                                // but multipolygon (relation) tags take precedence
                                for (var prop in way.properties) {
                                    if (!feature.properties[prop]) {
                                        feature.properties[prop] = prop;
                                    }
                                }
                            }
                        } else if (way && attr(member, 'role') === 'inner') {
                            if (feature.geometry.coordinates.length > 1) {
                                // do a point in polygon against each outer
                                // this determines which outer the inner goes with
                                for (var a = 0; a < feature.geometry.coordinates.length; a++) {
                                    if (pointInPolygon(
                                            way.geometry.coordinates[0][0],
                                            feature.geometry.coordinates[a][0])) {
                                        feature.geometry.coordinates[a].push(way.geometry.coordinates[0]);
                                        break;
                                    }
                                }
                            } else {
                                if (feature.geometry.coordinates.length) {
                                    feature.geometry.coordinates[0].push(way.geometry.coordinates[0]);
                                }
                            }
                        }
                    }
                    wayCache[ref] = false;
                }
            }

            function Ways(wayCache) {
                var features = [];
                for (var w in wayCache)
                    if (wayCache[w]) features.push(wayCache[w]);
                return features;
            }

            // https://github.com/substack/point-in-polygon/blob/master/index.js
            function pointInPolygon(point, vs) {
                var x = point[0],
                    y = point[1];
                var inside = false;
                for (var i = 0, j = vs.length - 1; i < vs.length; j = i++) {
                    var xi = vs[i][0],
                        yi = vs[i][1],
                        xj = vs[j][0],
                        yj = vs[j][1],
                        intersect = ((yi > y) !== (yj > y)) &&
                        (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
                    if (intersect) inside = !inside;
                }
                return inside;
            }

            // http://stackoverflow.com/a/1359808
            function sortObject(o) {
                var sorted = {},
                    key, a = [];
                for (key in o)
                    if (o.hasOwnProperty(key)) a.push(key);
                a.sort();
                for (key = 0; key < a.length; key++) sorted[a[key]] = o[a[key]];
                return sorted;
            }

            // http://stackoverflow.com/a/1830844
            function isNumber(n) {
                return !isNaN(parseFloat(n)) && isFinite(n);
            }

            function attr(x, y) {
                return x.getAttribute(y);
            }

            function attrf(x, y) {
                return parseFloat(x.getAttribute(y));
            }

            function getBy(x, y) {
                return x.getElementsByTagName(y);
            }

            function lonLat(elem) {
                return [attrf(elem, 'lon'), attrf(elem, 'lat')];
            }

            function setIf(x, y, o, name, f) {
                if (attr(x, y)) o[name] = f ? parseFloat(attr(x, y)) : attr(x, y);
            }
        }
    };


    $.noConflict();
    jQuery(document).ready(function($) {
        //RealTimePage.load(); RealTimePage.initMap();
        RealTimePage.load();
        const map = new Map();

        setInterval(function() {

            //map.addMarker({'location':'40.9194102#29.315826','personName':'test','gw_name':'GW NAME'});

            <?php foreach ($devices as $key => $dvalue) { ?>

                $.get("<?php echo base_url('livemap/getLastDeviceInfo?mac=' . $dvalue["mac"]); ?>", function(data, status) {
                    // marker ekleyecek fonksiyon
                    //map.addMarker(data)
                    var timedif = <?php echo time(); ?> - data.epoch;
                    if (timedif < 30) {
                        map.addMarker(data)
                    }
                    //console.log(timedif+" "+data.personName);


                    //data isimli objeyi kullanabilirsin
                });
            <?php  } ?>
            /*const {
                location,
                personName,
                gw_name
            } = data*/
        }, 5000);
    });

    /*
    var customPopup = "<b>My office</b><br/><img src='http://netdna.webdesignerdepot.com/uploads/2014/05/workspace_06_previo.jpg' alt='maptime logo gif' width='150px'/>";

    // specify popup options
    var customOptions =
        {
            'maxWidth': '400',
            'width': '200',
            'className' : 'popupCustom'
        }*/
</script>