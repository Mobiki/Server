$(function () {

    mapHeight = 200;
    mapWidth = 200;

    _FLOOR_HEIGHT = 2.2;

    var _all_coordinates = [];
    var LATIDX = 1, LNGIDX = 0;
    var _color_attributes = [
        'fill',
        'stroke',
        'color'
    ];
    var max_X = 0, max_Y = 0, min_X = 0, min_Y = 0, _c = 0.0;
    var _WIDTH = 100;
    var _coefficient = 0.0;
    var _origin = [];
    var lyr = null;
    var features = null;
    var features_route = null;
    var _ROOM_INFOS = [];
    var _features_rooms_ids = [];
    var _v = 0;
    var _k = null;
    var _tmp_search_lists = [];
    var container = document.getElementById("map");
    var app = Q3D.application;

    project = new Q3D.Project({
        crs: "EPSG:4326",
        proj: "+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs",
        title: "site",
        baseExtent: [0, 0, 0, 0],
        rotation: 0,
        zShift: 1.0,
        width: 100.0,
        zExaggeration: 1.5
    });

    function changed_attributes(k, val) {
        return '#FFFFFF';
    }

    function darker(color, percent) {
        var f = parseInt(color.slice(1), 16), t = percent < 0 ? 0 : 255, p = percent < 0 ? percent * -1 : percent, R = f >> 16, G = f >> 8 & 0x00FF, B = f & 0x0000FF;
        return parseInt((0x1000000 + (Math.round((t - R) * p) + R) * 0x10000 + (Math.round((t - G) * p) + G) * 0x100 + (Math.round((t - B) * p) + B)).toString(16).slice(1), 16);
    }

    function shadeBlendConvert(p, from, to) {
        if (typeof(p) != "number" || p < -1 || p > 1 || typeof(from) != "string" || (from[0] != 'r' && from[0] != '#') || (typeof(to) != "string" && typeof(to) != "undefined"))return null; //ErrorCheck
        if (!this.sbcRip)this.sbcRip = function (d) {
            var l = d.length, RGB = new Object();
            if (l > 9) {
                d = d.split(",");
                if (d.length < 3 || d.length > 4)return null;//ErrorCheck
                RGB[0] = i(d[0].slice(4)), RGB[1] = i(d[1]), RGB[2] = i(d[2]), RGB[3] = d[3] ? parseFloat(d[3]) : -1;
            } else {
                switch (l) {
                    case 8:
                    case 6:
                    case 3:
                    case 2:
                    case 1:
                        return null;
                } //ErrorCheck
                if (l < 6)d = "#" + d[1] + d[1] + d[2] + d[2] + d[3] + d[3] + (l > 4 ? d[4] + "" + d[4] : ""); //3 digit
                d = i(d.slice(1), 16), RGB[0] = d >> 16 & 255, RGB[1] = d >> 8 & 255, RGB[2] = d & 255, RGB[3] = l == 9 || l == 5 ? r(((d >> 24 & 255) / 255) * 10000) / 10000 : -1;
            }
            return RGB;
        }
        var i = parseInt, r = Math.round, h = from.length > 9, h = typeof(to) == "string" ? to.length > 9 ? true : to == "c" ? !h : false : h, b = p < 0, p = b ? p * -1 : p, to = to && to != "c" ? to : b ? "#000000" : "#FFFFFF", f = sbcRip(from), t = sbcRip(to);
        if (!f || !t)return null; //ErrorCheck
        if (h)return "rgb(" + r((t[0] - f[0]) * p + f[0]) + "," + r((t[1] - f[1]) * p + f[1]) + "," + r((t[2] - f[2]) * p + f[2]) + (f[3] < 0 && t[3] < 0 ? ")" : "," + (f[3] > -1 && t[3] > -1 ? r(((t[3] - f[3]) * p + f[3]) * 10000) / 10000 : t[3] < 0 ? f[3] : t[3]) + ")");
        else return parseInt((0x100000000 + (f[3] > -1 && t[3] > -1 ? r(((t[3] - f[3]) * p + f[3]) * 255) : t[3] > -1 ? r(t[3] * 255) : f[3] > -1 ? r(f[3] * 255) : 255) * 0x1000000 + r((t[0] - f[0]) * p + f[0]) * 0x10000 + r((t[1] - f[1]) * p + f[1]) * 0x100 + r((t[2] - f[2]) * p + f[2])).toString(16).slice(f[3] > -1 || t[3] > -1 ? 1 : 3), 16);
    }

    function json_file(data) {

        if (typeof data.features === null) {
            alert('JSON File Invalid!');
            return false;
        }

        features = data.features;

        //Clear
        lyr = null;
        project.clearLayer();

        // Layer 0
        lyr = project.addLayer(new Q3D.DEMLayer({q: 1, type: "dem", name: "Flat Plane"}));

        //Re-coordinate

        var coord_check = function (__coor1, __coor2) {
            for (var x = 0; x < __coor1.length; x++) {
                for (var h = 0; h < __coor2.length; h++) {
                    if (__coor1[x][LNGIDX] == __coor2[h][LNGIDX] && __coor1[x][LATIDX] == __coor2[h][LATIDX]) {
                        return [__coor1[x][LNGIDX], __coor1[x][LATIDX]];
                    }
                }
            }

            return false;
        }

        var coord_changed = function (coordinates, __equals) {
            for (var x = 0; x < coordinates[0].length; x++) {
                for (var h = 0; h < __equals.length; h++) {
                    if (coordinates[0][x][LNGIDX] == __equals[h][LNGIDX] && coordinates[0][x][LATIDX] == __equals[h][LATIDX]) {
                        delete coordinates[0][x];
                    }
                }
            }

            coordinates = coordinates.filter(function (e) {
                return (e == null) ? false : true;
            });

            var _new_array = [];
            coordinates[0].forEach(function (v, i) {
                _new_array.push(v);
            });
            coordinates[0] = _new_array;

            if (!(
                    coordinates[0][0][LNGIDX] == coordinates[0][coordinates[0].length - 1][LNGIDX]
                    && coordinates[0][0][LATIDX] == coordinates[0][coordinates[0].length - 1][LATIDX]
                )) {
                coordinates[0].push(coordinates[0][0]);
            }

            return coordinates;
        }

        //Eşleştirme çalışıyor, fakat eşleşenleri room id'si eşit olan ilk değere koordinatları ekle.

        var _tmp_features = [];
        var __rcoordinates = [];

        data.features.forEach(function (k, i) {
            if (
                typeof k.geometry.type !== 'undefined'
                && k.geometry.type == 'LineString'
                && typeof k.geometry.coordinates !== 'undefined'
                && typeof k.properties.type !== 'undefined'
                && typeof k.properties.id !== 'undefined'
                && k.geometry.coordinates.length >= 3
                && !(k.id in __rcoordinates)
            ) {
                data.features.forEach(function (x, y) {
                    if (
                        typeof x.geometry.type !== 'undefined'
                        && x.geometry.type == 'LineString'
                        && typeof x.geometry.coordinates !== 'undefined'
                        && x.geometry.coordinates.length >= 3
                        && typeof k.properties.id !== 'undefined'
                        && typeof x.properties.id !== 'undefined'
                        && typeof k.id !== 'undefined'
                        && typeof x.id !== 'undefined'
                        && typeof x.properties.type !== 'undefined'
                        && x.properties.type == 'room'
                        && k.properties.id === x.properties.id
                        && k.id !== x.id
                    ) {
                        if (typeof __rcoordinates[x.properties.id] === 'undefined')
                            __rcoordinates[x.properties.id] = new Array();

                        x.geometry.coordinates.forEach(function (h, j) {
                            __rcoordinates[x.properties.id].push(h);
                        });

                    }
                });
            }
        });


        //TODO: merge bölümü pasif edildi şuanlık.
        if (false && __rcoordinates.length > 0) {

            __rcoordinates.forEach(function (v, k) {

                var _equals = [];
                var __current_coordinates = [];

                __rcoordinates.forEach(function (x, i) {

                    if (i == k) {
                        var _r = coord_check(v, x);
                        if (_r) {
                            _equals.push(_r);
                            __current_coordinates.push(v);
                        }
                    }

                });

                var _current_feature = null;

                //Eşleşme var ise
                if (_equals.length > 0) {

                    var _coords = null;

                    if (!_current_feature)
                        var _coords = coord_changed(__current_coordinates, _equals);

                    data.features.forEach(function (x, i) {
                        if (
                            typeof x.geometry.type !== 'undefined'
                            && x.geometry.type == 'LineString'
                            && typeof x.geometry.coordinates !== 'undefined'
                            && x.geometry.coordinates.length >= 3
                            && typeof k !== 'undefined'
                            && typeof x.properties.id !== 'undefined'
                            && typeof x.properties.type !== 'undefined'
                            && x.properties.type == 'room'
                            && k === x.properties.id
                        ) {
                            if (!_current_feature) {
                                _current_feature = x;
                                data.features[i].geometry.coordinates = _coords[0];
                                return false;
                            }

                            delete data.features[i];
                        }
                    });
                }
            });

            data.features = data.features.filter(function (e) {
                return (e == null) ? false : true;
            });
        }

        //LAT LON CALCULATE

        var _tmp_X = [];
        var _tmp_Y = [];

        var _room_ids = [];

        data.features.forEach(function (k, i) {

            if (typeof k.geometry.type !== 'undefined' && k.geometry.type == 'LineString' && typeof k.geometry.coordinates !== 'undefined' && k.geometry.coordinates.length >= 3) {
                k.geometry.coordinates.forEach(function (v, j) {
                    _tmp_X.push(v[LNGIDX]);
                    _tmp_Y.push(v[LATIDX]);
                });

                if (
                    typeof k.properties.id !== 'undefined' && k.properties.id > 0
                ) {
                    _room_ids.push(k.properties.id);
                }
            }
        });

        max_Y = Math.max.apply(null, _tmp_Y);
        min_Y = Math.min.apply(null, _tmp_Y);

        max_X = Math.max.apply(null, _tmp_X);
        min_X = Math.min.apply(null, _tmp_X);

        _origin = [( max_X + min_X ) / 2, ( max_Y + min_Y ) / 2];
        _coefficient = _WIDTH / ( max_X - min_X );

        _c = Math.abs(Math.cos(max_Y * Math.PI / 180));

        //Get All Rooms Name

        if (_room_ids.length > 0) {
            $.when(
                $.ajax({
                    method: 'POST',
                    url: 'http://localhost/mobiki.in/room/roomInfo',
                    dataType: 'json',
                    data: {id: _room_ids.toString()}
                })
            ).then(function (data) {
                    if (data && data.results && data.results.length > 0) {
                        _ROOM_INFOS = data.results;

                        _draw_2();

                        _run();
                    }

                }, function (error) {
                    alert('Problems occurred room info!');
                });

        } else {
            _draw_2();

            _run();
        }
    }

    function get_room_name(_id) {
        if (_ROOM_INFOS.length == 0)
            return false;

        for (var i = 0; i < _ROOM_INFOS.length; i++) {
            if (typeof _ROOM_INFOS[i].id !== 'undefined' && typeof _ROOM_INFOS[i].name !== 'undefined' && _id == _ROOM_INFOS[i].id && _ROOM_INFOS[i].name != 'null' && _ROOM_INFOS[i].name != null) {
                return _ROOM_INFOS[i].name;
            }
        }

        return false;
    }

    function get_room_category(_id) {
        if (_ROOM_INFOS.length == 0)
            return false;

        for (var i = 0; i < _ROOM_INFOS.length; i++) {
            if (typeof _ROOM_INFOS[i].id !== 'undefined' && typeof _ROOM_INFOS[i].category !== 'undefined' && _id == _ROOM_INFOS[i].id && _ROOM_INFOS[i].category != 'null' && _ROOM_INFOS[i].category != null) {
                return _ROOM_INFOS[i].category;
            }
        }

        return false;
    }

    function get_room_icon(_id) {
        if (_ROOM_INFOS.length == 0)
            return false;

        for (var i = 0; i < _ROOM_INFOS.length; i++) {
            if (typeof _ROOM_INFOS[i].id !== 'undefined' && typeof _ROOM_INFOS[i].icon !== 'undefined' && _id == _ROOM_INFOS[i].id && _ROOM_INFOS[i].icon != 'null' && _ROOM_INFOS[i].icon != null) {
                return _ROOM_INFOS[i].icon;
            }
        }

        return false;
    }

    var get_feature_id = function (_id) {
        if (_features_rooms_ids.length === 0)
            return false;

        var _tmp = [];

        for (var i = 0; i < _features_rooms_ids.length; i++) {
            if (_features_rooms_ids[i].room_id === _id)
                _tmp.push(_features_rooms_ids[i].feature_id);
        }

        return (_tmp.length > 0) ? _tmp : false;
    };

    function _draw_2() {

        if (!features && features.length == 0) {
            alert('JSON File Invalid!');
            return false;
        }

        var data = {};
        data.features = features;

        // Layer 1
        lyr = project.addLayer(new Q3D.PolygonLayer({
            q: 1,
            objType: "Extruded",
            type: "polygon",
            name: "odalar",
            l: {ht: 1, v: 0.619627253159}
        }));
        lyr.a = ["id"];

        var __s = 0;
        data.features.forEach(function (k, i) {

            if (typeof k.geometry.type !== 'undefined' && k.geometry.type == 'LineString' && typeof k.geometry.coordinates !== 'undefined' && k.geometry.coordinates.length >= 3) {

                var _coordinates = [];

                k.geometry.coordinates.forEach(function (v, j) {
                    var _d = convert(v[LNGIDX], v[LATIDX]);
                    _coordinates.push([_d[LNGIDX], _d[LATIDX]]);
                });

                _center = center(_coordinates);

                var height = (typeof k.properties.height !== 'undefined' && !isNaN(parseFloat(k.properties.height))) ? k.properties.height : _FLOOR_HEIGHT;
                var _height = (k.properties.type !== 'room' ? 0 : (k.properties.type == 'group' ? 2 : height ));
                lyr.f[__s] = {
                    h: _height,
                    m: __s,
                    polygons: [[_coordinates]],
                    centroids: [[_center[LNGIDX], _center[LATIDX], 1]],
                    zs: [1]
                };

                var _name = __s + 1;
                if (typeof k.properties.id !== 'undefined') {
                    _name = get_room_name(k.properties.id);
                }

                _name = '';

                lyr.f[__s].a = [{
                    'name': _name,
                    'type': (typeof k.properties.type !== 'undefined') ? k.properties.type : null,
                    'id': (typeof k.properties.id !== 'undefined') ? k.properties.id : null,
                    'color': (typeof k.properties.color !== 'undefined') ? '#' + k.properties.color : 0xffffff
                }];

                var _opts = {
                    c: ( typeof k.properties.color !== 'undefined' ? '#' + k.properties.color : 0xffffff ),
                    type: 1,
                    o: (height == 0 || (typeof k.properties.id !== 'undefined' && k.properties.id > 0)) ? 1 : 0.9
                };

                lyr.m[__s] = _opts;

                //features push
                if (typeof k.properties.id !== 'undefined' && k.properties.id > 0)
                    _features_rooms_ids.push({
                        'room_id': k.properties.id,
                        'feature_id': __s
                    });

                __s++;
            }
        });

        //Text Layer

        var __s = 0;

        lyr = project.addLayer(new Q3D.PointLayer({q: 1, objType: "Icon", type: "point", name: "odaisimleri"}));
        lyr.a = ["id"];

        data.features.forEach(function (k, i) {

            if (
                typeof k.geometry.type !== 'undefined'
                && k.geometry.type == 'Point'
                && typeof k.geometry.coordinates !== 'undefined'
                && k.geometry.coordinates.length == 2
                && typeof k.properties.id !== 'undefined'
                && parseInt(k.properties.id) > 0
                && (_name = get_room_name(k.properties.id))
            ) {

                var _opts = {
                    i: __s
                };

                var _d = convert(k.geometry.coordinates[LNGIDX], k.geometry.coordinates[LATIDX]);

                var height = (typeof k.properties.height !== 'undefined' && !isNaN(parseFloat(k.properties.height))) ? k.properties.height : _FLOOR_HEIGHT;

                lyr.f[__s] = {m: __s, pts: [[_d[LNGIDX], _d[LATIDX], height * 1.6]]};

                lyr.f[__s].a = [{
                    'feature_id': (typeof k.properties.id !== 'undefined' && k.properties.id > 0 && (_f_id = get_feature_id(k.properties.id))) ? _f_id : null,
                    'id': (typeof k.properties.id !== 'undefined') ? k.properties.id : null
                }];

                lyr.m[__s] = _opts;

                project.images[__s] = new Array;

                var canvas = document.createElement('canvas'),
                    context = canvas.getContext('2d'),
                    metrics = null,
                    textHeight = 20,
                    textWidth = 0,
                    actualFontSize = 2;

                context.font = "normal " + textHeight + "px Arial";
                metrics = context.measureText(_name);
                var textWidth = metrics.width;

                canvas.width = textWidth;
                canvas.height = textHeight;
                context.font = "normal " + textHeight + "px Arial";
                context.textAlign = "center";
                context.textBaseline = "middle";
                context.fillStyle = "#000000";
                context.fillText(_name, textWidth / 2, textHeight / 2);

                project.images[__s].texture = new THREE.Texture(canvas, 0, 0, 0, 0, 0);
                project.images[__s].texture.needsUpdate = true;

                project.images[__s].width = canvas.width;
                project.images[__s].height = canvas.height;

                __s++;
            }

        });

        //Draw Point

        var __s = 0;
        lyr = project.addLayer(new Q3D.PointLayer({q: 1, objType: "Icon", type: "point", name: "iconlar"}));

        data.features.forEach(function (k, i) {

            if (
                typeof k.geometry.type !== 'undefined'
                && k.geometry.type == 'Point'
                && typeof k.geometry.coordinates !== 'undefined'
                && k.geometry.coordinates.length == 2
                && typeof k.properties.id !== 'undefined'
                && parseInt(k.properties.id) > 0
                && (_icon = get_room_icon(k.properties.id))
                && imageExists((_icon = '/images/icon/' + _icon + '.png'))
            ) {

                var _opts = {};

                _opts['i'] = __s;

                var _d = convert(k.geometry.coordinates[LNGIDX], k.geometry.coordinates[LATIDX]);

                lyr.f[__s] = {m: __s, pts: [[_d[LNGIDX], _d[LATIDX], _FLOOR_HEIGHT * 2.55]]};

                lyr.m[__s] = _opts;

                project.images[__s] = new Array;

                project.images[__s].texture = THREE.ImageUtils.loadTexture(_icon);
                project.images[__s].texture.wrapS = THREE.RepeatWrapping;
                project.images[__s].texture.wrapT = THREE.RepeatWrapping;
                project.images[__s].width = 100;
                project.images[__s].height = 100;

                __s++;
            }

        });
    }

    function _run() {
        $('#map').empty();

        app.init(container);

        // load the project
        app.loadProject(project);

        app.addEventListeners();

        if (parseInt($('#info-panel-fixed').css('height')) == 400)
            app.setCanvasSize(window.innerWidth, window.innerHeight - 400);
        else
            app.setCanvasSize(window.innerWidth, window.innerHeight);

        app.start();

        //App.unblockUI();

    }

    function json_way_file(data) {

        if (typeof data.features === null) {
            alert('JSON File Invalid!');
            return false;
        }

        features_route = data.features;
        project.clearLayer();

        //Drawing Polygon
        _draw_2();

        var _routes_Lists = [];
        var _available_routes = [];

        //route Calc
        data.features.forEach(function (k, i) {
            if (typeof k.geometry.type !== 'undefined' && k.geometry.type == 'LineString' && typeof k.geometry.coordinates !== 'undefined' && k.geometry.coordinates.length == 2) {
                var _coordinates = [];

                var distance = 0.0;

                k.geometry.coordinates.forEach(function (v, j) {
                    var _d = convert(v[LNGIDX], v[LATIDX]);
                    _coordinates.push([_d[LNGIDX], _d[LATIDX]]);
                });

                distance = Math.sqrt(Math.pow(( _coordinates[1][LNGIDX] - _coordinates[0][LNGIDX] ), 2) + Math.pow(( _coordinates[1][LATIDX] - _coordinates[0][LATIDX] ), 2));

                _routes_Lists.push({
                    id: k.id,
                    coordinates: _coordinates,
                    distance: distance
                });
            }
        });

        function getIdsByCoord(lngidx, latidx, _external_ids) {

            if (!_routes_Lists || _routes_Lists.length == 0)
                return false;

            var _tmp = [];

            _routes_Lists.forEach(function (k, i) {
                for (var i = 0; i < k.coordinates.length; i++) {
                    if (k.coordinates[i][LNGIDX] == lngidx && k.coordinates[i][LATIDX] == latidx && _external_ids !== k.id) {
                        _tmp.push(k.id);
                        break;
                    }
                }
            });

            /*if(_external_ids && _tmp.length > 0)
             _tmp.push(_external_ids); */

            return (_tmp.length > 0) ? _tmp : false;
        }

        function getCoordById(id) {
            if (!_routes_Lists || _routes_Lists.length == 0)
                return false;

            for (var i = 0; i < _routes_Lists.length; i++) {
                if (id == _routes_Lists[i].id)
                    return _routes_Lists[i].coordinates;

            }

            return false;
        }

        function getDistanceById(id) {
            if (!_routes_Lists || _routes_Lists.length == 0)
                return false;

            for (var i = 0; i < _routes_Lists.length; i++) {
                if (id == _routes_Lists[i].id)
                    return _routes_Lists[i].distance;

            }
        }

        function arr_min(_ids) {
            var _min = false;
            var _min_id = false;

            _ids.forEach(function (v, n) {

                var _distance = getDistanceById(v);

                _ids.forEach(function (z, c) {

                    var _distance2 = getDistanceById(z);

                    if (_distance > _distance2) {
                        _min = _distance2;
                        _min_id = z;
                    }
                });
            });

            return ( _min && _min_id ) ? {
                'min': _min,
                'id': _min_id
            } : false;
        }

        var _tmp_ids = [];
        var _tmp_distance_count = 0.0;
        var _tmp_used_coordinates = [];

        function calcDistance(lngidx, latidx, _id) {

            if (_tmp_ids.indexOf(_id) !== -1)
                return false;

            var _ids = getIdsByCoord(lngidx, latidx, _id);

            if (_ids && _ids.length > 0) {

                var _tmp_distance = [];

                var min_id = arr_min(_ids);
                _tmp_distance_count += min_id.min;
                _tmp_ids.push(min_id.id);

                var _coord = getCoordById(min_id.id);

                for (var i = 0; i < _coord.length; i++) {
                    if (min_id.id < 4)
                        calcDistance(_coord[i][LNGIDX], _coord[i][LATIDX], min_id.id);
                }
            }
        }

        //Tek bir noktadan itibaren her bir coord al ve eşleşenler ile uzunlukları push et. Sonra dijkstras algoritmasını uyarla. TODO
        function routeCalc() {
            if (!_routes_Lists || _routes_Lists.length == 0)
                return false;

            _routes_Lists.forEach(function (k, i) {

                if (!(k.id == 1))
                    return false;

                var _used_id_lists = []; //tracking

                var _tmp = false;

                for (var i = 0; i < k.coordinates.length; i++) {
                    calcDistance(k.coordinates[i][LNGIDX], k.coordinates[i][LATIDX], k.id);
                }

                /*
                 if(_tmp)
                 console.log('-'); */
            });
        }

        routeCalc(); //calis...


        // Layer 0
        lyr = project.addLayer(new Q3D.DEMLayer({q: 1, type: "dem", name: "Flat Plane"}));

        // Route Layer
        lyr = project.addLayer(new Q3D.LineLayer({
            q: 1,
            objType: "Line",
            type: "line",
            name: "route",
            l: {ht: 1, v: 0.619627253159}
        }));
        lyr.a = ["id"];

        var __s = 0;

        //TODO:Route
        var _lines = [];

        data.features.forEach(function (k, i) {
            if (typeof k.geometry.type !== 'undefined' && k.geometry.type == 'LineString' && typeof k.geometry.coordinates !== 'undefined' && k.geometry.coordinates.length == 2) {
                var _coordinates = [];

                if (typeof k.id !== 'undefined' && (k.id == 1 || k.id == 2 || k.id == 3)) {

                    k.geometry.coordinates.forEach(function (v, j) {
                        var _d = convert(v[LNGIDX], v[LATIDX]);
                        _coordinates.push([_d[LNGIDX], _d[LATIDX]]);
                    });

                    _center = center(_coordinates);

                    //Route çizdirme burda olucak. TODO
                    lyr.f[__s] = {m: __s, lines: [_coordinates], zs: 5};

                    lyr.m[__s] = {
                        c: 0x000000,
                        type: 2, //Type => LineBasic,
                    };

                    lyr.f[__s].a = [{
                        'type': (typeof k.properties.type !== 'undefined') ? k.properties.type : null,
                        'id': (typeof k.properties.id !== 'undefined') ? k.properties.id : null
                    }];

                    __s++;
                }


            }

        });

        function get_room_id_for_check_coordinates(lngidx, latidx) {
            for (var i = 0; i < data.features.length; i++) {
                if (
                    typeof data.features[i].geometry.type !== 'undefined'
                    && data.features[i].geometry.type == 'Point'
                    && typeof data.features[i].geometry.coordinates !== 'undefined'
                    && data.features[i].geometry.coordinates.length == 2
                    && typeof data.features[i].properties.id !== 'undefined'
                    && parseInt(data.features[i].properties.id) > 0
                ) {
                    if (data.features[i].geometry.coordinates[LNGIDX] == lngidx && data.features[i].geometry.coordinates[LATIDX] == latidx)
                        return data.features[i].properties.id;
                }
            }

            return false;
        }

        _run();
    }

    function o_room_clicked(data, _room_id, layerId) {
        if (!features || features.length == 0)
            return false;

        for (var i = 0; i < features.length; i++) {
            if (
                typeof features[i].geometry.type !== 'undefined'
                && features[i].geometry.type == 'LineString'
                && typeof features[i].geometry.coordinates !== 'undefined'
                && features[i].geometry.coordinates.length >= 3
                && typeof features[i].properties.id !== 'undefined'
                && features[i].properties.id > 0
            ) {

                if (_room_id == features[i].properties.id) {

                    var _coordinates = [];

                    features[i].geometry.coordinates.forEach(function (v, j) {
                        var _d = convert(v[LNGIDX], v[LATIDX]);
                        _coordinates.push([_d[LNGIDX], _d[LATIDX]]);
                    });

                    _center = center(_coordinates);

                    return {
                        'layer_id': layerId,
                        'feature_id': get_feature_id(_room_id),
                        'centeroid': _center
                    };
                }
            }
        }
    }

    function getRoomLocation(_id) {
        //App.blockUI();

        var _room_id = parseInt(_id);
        var _f = o_room_clicked(features, _room_id, 1); //1 => layer 1
        Q3D.application.highlightFeature(_f.layer_id, _f.feature_id);
        Q3D.application.queryMarker.position.set(_f.centeroid[LNGIDX], _f.centeroid[LATIDX], 4.18 + 2.1);
        Q3D.application.queryMarker.visible = true;

        $.ajax({
            method: 'POST',
            url: '/room/details/_room_id',
            dataType: 'json',
            data: {},
            success: function (data) {
                noty({
                    text        : 'Kat: '+data.floor+'<br><br>Oda: '+data.name,
                    type        : "success",
                    dismissQueue: true,
                    closeWith   : ['click', 'backdrop'],
                    modal       : true,
                    layout      : 'center',
                    theme       : 'relax',
                    maxVisible  : 10,
                    progressBar: false
                });
            }

        });
        //App.unblockUI();
    }

    function __k() {
        _k = setTimeout(function () {
            if ($('#o_room_id').length > 0 && features && features.length > 0) {
                getRoomLocation($('#o_room_id').val());
            }
        }, 2000);
    }

    __k();

    function convert(longitude, latitude) {
        return [-( _origin[LNGIDX] - longitude) * _coefficient * _c, ( -_origin[LATIDX] + latitude ) * _coefficient];
    }

    function center(coordinates) {
        var _t_X = [];
        var _t_Y = [];

        coordinates.forEach(function (v, i) {
            _t_X.push(v[LNGIDX]);
            _t_Y.push(v[LATIDX]);
        });

        var _max_Y = Math.max.apply(null, _t_Y);
        var _min_Y = Math.min.apply(null, _t_Y);

        var _max_X = Math.max.apply(null, _t_X);
        var _min_X = Math.min.apply(null, _t_X);

        return [( _max_X + _min_X ) / 2, ( _max_Y + _min_Y ) / 2];
    }

    function imageExists(image_url) {

        var http = new XMLHttpRequest();

        http.open('HEAD', image_url, false);
        http.send();

        return http.status != 404;
    }

    function containsAll(needles, haystack) {

        var _status = true;

        for (var i = 0; i < needles.length; i++) {

            if (needles[i].equals(haystack)) {
                _status = false;
                break;
            }

        }

        return _status;
    }

    //<editor-fold desc="Step 1: Trigger">
    var trigger = setTimeout(function(){
        if($('#floorPlan a').length > 0){
            $('#floorPlan a.floorListClick:first-child').trigger('click');
        }
        clearTimeout(trigger);
    }, 500);
    //</editor-fold>

    //<editor-fold desc="Step 2: Is Floor Changed">
    $(document).on('click', '.floorListClick', function (event) {
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

        $('#floorPlan .floorListClick').removeClass('active');
        $(this).addClass('active');

        var level  = Number($(this).data('value'));
        var jsonFile = 'http://localhost/mobiki.in/building/getBuildingJson?level={L}'.replace('{L}', level);

        $.get(jsonFile, function (data) {
            if (data) {
                data = osm_geojson.osm2geojson(data);
                json_file(data);
                //setTimeout(App.unblockUI(), 2000);
            } else {
                /*App.unblockUI();
                noty({
                    text        : 'Problems occurred drawn floor plans!',
                    type        : "error",
                    dismissQueue: true,
                    closeWith   : ['click', 'backdrop'],
                    modal       : true,
                    layout      : 'center',
                    theme       : 'relax',
                    maxVisible  : 10,
                    progressBar: false
                });*/
            }
        }).fail(function () {
            /*App.unblockUI();
            noty({
                text        : 'Problems occurred drawn floor plans!',
                type        : "error",
                dismissQueue: true,
                closeWith   : ['click', 'backdrop'],
                modal       : true,
                layout      : 'center',
                theme       : 'relax',
                maxVisible  : 10,
                progressBar: false
            });*/
        });

    });
    //</editor-fold>

    // attach the .equals method to Array's prototype to call it on any array
    Array.prototype.equals = function (array) {
        // if the other array is a falsy value, return
        if (!array)
            return false;

        // compare lengths - can save a lot of time
        if (this.length != array.length)
            return false;

        for (var i = 0, l = this.length; i < l; i++) {
            // Check if we have nested arrays
            if (this[i] instanceof Array && array[i] instanceof Array) {
                // recurse into the nested arrays
                if (!this[i].equals(array[i]))
                    return false;
            }
            else if (this[i] != array[i]) {
                // Warning - two different object instances will never be equal: {x:20} != {x:20}
                return false;
            }
        }
        return true;
    }
    // Hide method from for-in loops
    Object.defineProperty(Array.prototype, "equals", {enumerable: false});
});