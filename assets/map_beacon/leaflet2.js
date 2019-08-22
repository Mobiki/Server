$(function() {
    var map, svg, g;
    map = new L.Map('map-2d').setView(new L.LatLng(0,0), 19);
    map.attributionControl.setPrefix('');
    svg = d3.select(map.getPanes().overlayPane).append('svg');
    g = svg.append('g');
    way = svg.append('g');

    map._layersMaxZoom  = 24;
    map._layersMinZoom  = 18;

    var _color_attributes = [
        'fill',
        'stroke',
        'color'
    ];

    var _columns = ['name', 'value'];

    var _colors = {
        room_check_true     : '#030',
        way_check_true      : '#db731c',
        route_check_true    : '#1caadb',
        data_check_color    : '#550000',
        room_name_check     : '#a66bbe',
        diff_id_room_check  : '#16a085'
    };

    var jsonOutside;
    var jsonWayOutside;
    var _current_json;
    var _currentType;

    var _attributes_datas = [];
    var _tmp_attributes_datas = [];
    var _tmp_attributes_way_datas = [];
    var _tmp_attributes_rooms_all_datas = [];
    var _shift_save_attributes = [];
    var _route_check_datas = [];
    var _no_match_lists = [];
    var _sync_floor_lists = [];
    var _tmp_coordinates_datas = [];

    var __loading_showing = false;

    var __error_messages = [];

    var __t = [];

    var _b_id = 0; //building id
    var _level = 0; //floor level

    d3.selection.prototype.first = function() {
        return d3.select(this[0][0]);
    };

    d3.selection.prototype.last = function() {
        var last = this.size() - 1;
        return d3.select(this[0][last]);
    };

    d3.selection.prototype.index = function() {
        var last = this.size() - 1;
        return d3.select(this[0][index]);
    };

    var _MESSAGE_TEMPLATE = '<div class="alert alert-{T}">{H} {M}</div>';
    function getFloorName(_level){
        if(_sync_floor_lists.length === 0)
            return '';

        for(var i=0; i<_sync_floor_lists.length; i++){
            if(_sync_floor_lists[i].level == _level){
                return _sync_floor_lists[i].name
            }
        }
    }

    function getJStorage(_title)
    {
        var _title = $.jStorage.get(_title);
        return _title ? _title : false;
    }

    function setJStorage(_title, _value)
    {
        $.jStorage.setTTL(_title, 10000);
        return $.jStorage.set(_title, _value ? _value : null);
    }

    function setMessage(_title, _message)
    {
        var _t = getJStorage(_title);
        var _p = JSON.parse(_t);
        var _status = false;

        if( _p.length == 0 )
            _p = new Array();
        else if( _p.length > 0 ){
            for(_i in _p){
                if( _p[_i] == _message ){
                    _status = true;
                    break;
                }
            }
        }

        /*
         if(!_status){
         _p.push(_message);
         _p = JSON.stringify(_p);
         }*/

        return setJStorage( _title, _p );
    }

    function jStorageLoader()
    {
        if( _b_id <= 0 )
            return false;

        var _l = JSON.parse( getJStorage( 'messages_' + _b_id + '_' + _level + '_lists' ) );

        $('#message-box').html('');

        if( _l.length > 0 ){

            $('#message-box').append('<h6>Old Messages</h6>');

            _l.forEach(function(v, k){
                $('#message-box').append( v );
            });

        }
    }

    function json_file(error, file, way_file){

        if(!__loading_showing)
            __loading_showing = true;

        if(error){
            alert('File XML Error Problem');
            return false;
        }

        jsonOutside = collection = osm_geojson.osm2geojson(file); //json clone

        if(way_file){
            jsonWayOutside = osm_geojson.osm2geojson(way_file);
        }

        reset = function(){
            var bottomLeft, topRight;
            bottomLeft = project(bounds[0]);
            topRight = project(bounds[1]);

            _bottomLeft = project(_bounds[0]);
            _topRight = project(_bounds[1]);

            svg.attr('width', topRight[0] - bottomLeft[0]).attr('height', bottomLeft[1] - topRight[1]).style('margin-left', bottomLeft[0] + 'px').style('margin-top', topRight[1] + 'px');
            g.attr('transform', 'translate(' + -bottomLeft[0] + ',' + -topRight[1] + ')');
            way.attr('transform', 'translate(' + -bottomLeft[0] + ',' + -topRight[1] + ')');

            _feature.attr('d', path);
            return feature.attr('d', path);
        };

        project = function(x){
            var point;
            point = map.latLngToLayerPoint(new L.LatLng(x[1], x[0]));
            return [point.x, point.y];
        };

        bounds = d3.geo.bounds(jsonOutside);

        if(way_file)
            _bounds = d3.geo.bounds(jsonWayOutside);

        path = d3.geo.path().projection(project);

        if(file){

            g.selectAll('path').remove();
            _tmp_attributes_datas = [];
            feature = g.selectAll('path').data(jsonOutside.features).enter().append('path').style('fill', function(d){
                if(
                    typeof d.properties.id !== 'undefined' && d.properties.id > 0
                    && typeof d.geometry.type !== 'undefined' && d.geometry.type == 'LineString'
                )
                    _tmp_attributes_datas.push({id:d.id, properties:d.properties});
            });

            if(!$('#used-room-check-attribute-btn').is(':checked')){
                g.selectAll('path').style("fill", function (d) {
                    if( typeof d.properties.color !== 'undefined' ){
                        var v = d.properties.color.toString().replace('#', '');
                        return '#' + v;
                    }
                    else
                        return '';
                });
            }
        }

        if(way_file){

            var coord_check = function (__coor1, __coor2){
                for(var x=0; x<__coor1.length; x++){
                    for(var h=0; h<__coor2.length; h++){
                        if(__coor1[x][0] == __coor2[h][0] && __coor1[x][1] == __coor2[h][1]){
                            return true;
                        }
                    }
                }

                return false;
            }

            //Route Check
            var _tmp_coordinates = [];
            var _tmp_coordinates_all = [];

            jsonWayOutside.features.forEach(function(v, i){
                if(
                    typeof v.geometry.type !== 'undefined'
                    && v.geometry.type == 'LineString'
                    && typeof v.geometry.coordinates !== 'undefined'
                ){
                    v.geometry.coordinates.forEach(function(k, n){
                        _tmp_coordinates_all.push(k);
                    });
                }
            });

            var __midpoint = []; //Way'in ortak noktaların Listesi
            for(var i=0; i < _tmp_coordinates_all.length; i++){

                for(var z=0; z < _tmp_coordinates_all.length; z++){

                    if(
                        coord_check([ _tmp_coordinates_all[i] ], [ _tmp_coordinates_all[z] ])
                        && z !== i
                        && !coord_check([ _tmp_coordinates_all[i] ], __midpoint)
                    ){
                        __midpoint.push(_tmp_coordinates_all[i]);
                    }

                }

            }

            var __endpoints = []; //Way'in Uç noktaların Listesi
            for(var i=0; i < _tmp_coordinates_all.length; i++){
                if(!coord_check([ _tmp_coordinates_all[i] ], __midpoint))
                    __endpoints.push(_tmp_coordinates_all[i]);
            }

            var __nomatchpoint = []; //Pointlerin Endpoint ile eşleşip eşleşmediği kontrol ediliyor.
            jsonWayOutside.features.forEach(function(v, i){
                if(
                    typeof v.geometry.type !== 'undefined'
                    && v.geometry.type == 'Point'
                    && typeof v.geometry.coordinates !== 'undefined'
                    && v.geometry.coordinates.length == 2
                    && !coord_check([ v.geometry.coordinates ], __endpoints)
                ){
                    __nomatchpoint.push(v.geometry.coordinates);
                }
            });

            if(__nomatchpoint.length > 0)
                _no_match_lists = __nomatchpoint; //global değişkene ata

            way.selectAll('path').remove();
            _tmp_attributes_way_datas = [];

            _feature = way.selectAll('path').data(jsonWayOutside.features).enter().append('path').style('fill', function(d){
                if(
                    typeof d.geometry.type !== 'undefined'
                    && d.geometry.type === 'Point'
                    && typeof d.properties.id !== 'undefined'
                )
                    _tmp_attributes_way_datas.push({id:d.id, properties:d.properties});
            });

            way.selectAll('path').style("stroke", function (d) {

                var _return = '';

                if(d.geometry.type == 'LineString' || d.geometry.type == 'Point'){

                    if(
                        typeof d.geometry.type !== 'undefined'
                        && d.geometry.type == 'LineString'
                        && typeof d.properties.id !== 'undefined'
                        && ( d.properties.id == -1 || d.properties.id == '-1' )
                    ){
                        d3.select(this).style('fill', '#DF8620');
                    }else{
                        d3.select(this).style('fill', '#07103A');
                    }

                    if(d.geometry.type == 'LineString')
                        d3.select(this).style('stroke-width', '2');

                    _return = '#07103A';
                }
                else
                    _return = '#000000';

                if($('#used-route-check-attribute-btn').is(":checked")){
                    for(var i=0; i<_no_match_lists.length; i++){
                        if(
                            typeof d.geometry.coordinates !== 'undefined'
                            && d.geometry.coordinates.length == 2
                            && (_no_match_lists[i][0] === d.geometry.coordinates[0] && _no_match_lists[i][1] === d.geometry.coordinates[1])
                        ){
                            d3.select(this).style('fill', _colors.route_check_true); //True
                            break;
                        }
                    }
                }

                if(_return)
                    return _return;
            });

            jsonWayOutside.features.forEach(function(v, i){
                if(
                    typeof v.geometry.type !== 'undefined'
                    && v.geometry.type == 'Point'
                    && typeof v.geometry.coordinates !== 'undefined'
                    && v.geometry.coordinates.length == 2
                ){
                    if(!coord_check(_tmp_coordinates, v.geometry.coordinates)){
                        _route_check_datas.push(v.geometry.coordinates);
                    }
                }
            });
        }

        var southWest = L.latLng(bounds[0][1], bounds[0][0]),
            northEast = L.latLng(bounds[1][1], bounds[1][0]),
            neigh_bounds = L.latLngBounds(southWest, northEast);
        map.fitBounds(neigh_bounds);

        feature.on('click', function(d){

            _current_json = d;

            if (d3.event.ctrlKey) {

                if(_shift_save_attributes.length === 0 && _current_json)
                    _shift_save_attributes.push(_current_json.id);

                if( !( _shift_save_attributes.indexOf(d.id) > -1) )
                    _shift_save_attributes.push(d.id);

                //form bilgilerini temizle.
                $('#room-text').attr('disabled', false);
                $('#room-text-btn').attr('disabled', 'disabled');
                $('#room-text').val('');

                table_refresh();

            }else{

                _shift_save_attributes = []; //seçilen arrayı temizle.
            }

            tableRowClicked(d, 'p'); //p=>plan
            if (!d3.event.ctrlKey)
                auto_checked_colorized();
            d3.select(this).style('fill', '#A52A2A');
        });

        _feature.on('click', function(d){
            _current_json = d;

            tableRowClicked(d, 'w'); //w => way
            auto_checked_colorized();
            d3.select(this).style('fill', '#A52A2A');
        });

        jStorageLoader();

        getRoomsCount();

        auto_checked_sending();

        map.on('viewreset', function(){
            return reset();
        });

        reset();

        if(__loading_showing){
            $('#ajax-loading').hide(500);
        }
    }

    function tabulate(data, header) {

        d3.select("#sidebar-properties-wrapper #table-box table").remove();
        var table = d3.select("#sidebar-properties-wrapper #table-box").append("table").attr("class","table table-bordered");
        thead = table.append("thead"),
            tbody = table.append("tbody");

        thead.append("tr")
            .selectAll("th")
            .data(header)
            .enter()
            .append("th")
            .text(function(column) { return column; });

        var rows = tbody.selectAll("tr")
            .data(data)
            .enter()
            .append("tr");

        var cells = rows.selectAll("td")
            .data(function(row) {
                return header.map(function(column) {
                    return {column: column, value: row[column]};
                });
            })
            .enter()
            .append("td")
            .each(function(d, i){
                if(d.column == 'name' && d.value == 'type'){
                    d3.select(this.nextSibling).attr('data-edit-source', '_property_type_lists');
                    d3.select(this.nextSibling).attr('data-edit-type', 'select');
                }else if(d.column == 'name' && d.value == 'color'){
                    d3.select(this.nextSibling).attr('data-edit-source', '_property_color_lists');
                    d3.select(this.nextSibling).attr('data-edit-type', 'select');
                }
            })
            .html(function(d) { return d.value; });

        return table;
    }

    function changed_attributes(k, val, auto){
        var auto = auto || false;
        return (_color_attributes.indexOf(k) > -1) ? ((!auto ? '#' : '') + val.toString().replace('#', '').toUpperCase()) : val;
    }

    function tableRowClicked(x, type) {
        _currentType = type ? type : null;

        $('#room-text').attr('disabled', false);
        $('#room-text-btn').attr('disabled', 'disabled');
        $('#room-text').val('');

        if(type == 'p'){

            if(_shift_save_attributes.length > 0){

                for(i in _shift_save_attributes){
                    jsonOutside.features.forEach(function (d) { // loop through json data to match td entry
                        if ( typeof d.id != 'undefined' && _shift_save_attributes[i] == d.id && typeof d.properties != 'undefined') {

                            for(k in d.properties){
                                if( typeof __t !== 'undefined' && !(__t.indexOf(k) > -1) )
                                    __t.push(k);
                            }

                        };
                    });
                }

                var _m = [];
                for(i in __t)
                    _m.push({
                        name  : __t[i],
                        value : '',
                        delete : "<button class=\"btn btn-outline btn-danger btn-sm del-attribute-btn\"><span class=\"fa fa-trash-o\"></span></button>"
                    });

                var table = tabulate(_m, _columns.concat(['delete']));

                table_refresh();

            }else{

                __t = [];

                jsonOutside.features.forEach(function (d) { // loop through json data to match td entry
                    if ( typeof x.id != 'undefined' && x.id == d.id && typeof x.properties != 'undefined') {

                        var _t = [];
                        for(k in x.properties)
                            _t.push({
                                name  : k,
                                value : changed_attributes(k, x.properties[k]),
                                delete : "<button class=\"btn btn-outline btn-danger btn-sm del-attribute-btn\"><span class=\"fa fa-trash-o\"></span></button>"
                            });

                        var table = tabulate(_t, _columns.concat(['delete']));

                        table_refresh();
                    };
                });

            }

        }else{

            if(_shift_save_attributes.length > 0){

                for(i in _shift_save_attributes){
                    jsonWayOutside.features.forEach(function (d) { // loop through json data to match td entry
                        if ( typeof d.id != 'undefined' && _shift_save_attributes[i] == d.id && typeof d.properties != 'undefined') {

                            for(k in d.properties){
                                if( typeof __t !== 'undefined' && !(__t.indexOf(k) > -1) )
                                    __t.push(k);
                            }

                        };
                    });
                }

                var _m = [];
                for(i in __t)
                    _m.push({
                        name  : __t[i],
                        value : '',
                        delete : "<button class=\"btn btn-outline btn-danger btn-sm del-attribute-btn\"><span class=\"fa fa-trash-o\"></span></button>"
                    });

                var table = tabulate(_m, _columns.concat(['delete']));

                table_refresh();

            }else{

                __t = [];

                jsonWayOutside.features.forEach(function (d) { // loop through json data to match td entry
                    if ( typeof x.id != 'undefined' && x.id == d.id && typeof x.properties != 'undefined') {

                        var _t = [];
                        for(k in x.properties)
                            _t.push({
                                name  : k,
                                value : changed_attributes(k, x.properties[k]),
                                delete : "<button class=\"btn btn-outline btn-danger btn-sm del-attribute-btn\"><span class=\"fa fa-trash-o\"></span></button>"
                            });

                        var table = tabulate(_t, _columns.concat(['delete']));

                        table_refresh();
                    };
                });

            }

        }

        if(_shift_save_attributes.length === 0){

            var _t = $('#sidebar-properties-wrapper #table-box table tbody tr');

            if(_t.length > 0){

                //1. column
                var room_l = $('#sidebar-properties-wrapper #table-box table tbody tr td:contains("id")').parent('tr').index();
                var room_id = $('#sidebar-properties-wrapper #table-box table tbody tr:eq('+room_l+') td:eq(1)').text();

                if(room_l >= 0 && typeof room_id !== 'undefined' && room_id > 0 && _b_id > 0){

                    var _URL    = decodeURIComponent(_ROOM_INFO).replace('{B}', _b_id);
                    _URL        = _URL.replace('{L}', _level);

                    $.ajax({
                        method  : 'GET',
                        url     : _URL,
                        data    : { id : room_id },
                        dataType: 'json',
                        success : function(data){
                            if(data && data.id && data.name)
                            {
                                $('#sidebar-properties-wrapper #table-box table tbody tr:eq('+room_l+') td').addClass('table-check');

                                if(data.name && data.name.length > 0){
                                    var _name = xssFilters.inHTMLData(data.name);
                                    $('#room-text').val(_name);
                                }
                            }else{
                                $('#sidebar-properties-wrapper #table-box table tbody tr:eq('+room_l+') td').removeClass('table-check');
                            }
                        },
                        error   : function(error){
                            alert('ID of the room check when problems occurred');
                        }
                    });
                }
            }

        }

        var $menu = $('#sidebar-properties-wrapper');
        var $menu2 = $('#sidebar-messages-wrapper');
        var $content = $('#main-wrapper');
        //Properties Slide in
        $content.removeClass('no-transition');
        if (!($menu.is(':visible') && $content.hasClass('col-md-10'))) {

            if ($menu2.is(':visible')) {
                // Slide out
                $menu2.animate({
                    right: -($menu2.outerWidth() + 10)
                }, function () {
                });
            }

            // Slide in
            $menu.show(500).animate({ right: 0 });
            $content.removeClass('col-md-12').addClass('col-md-10');
            $.cookie('offcanvas', 'show');
            $.cookie('offcanvas_type', 'menu');
        }
    }

    function table_refresh(){
        $('#sidebar-properties-wrapper #table-box table').editableTableWidget({preventColumns:[3]});
    }

    function getRoomsCount(){

        var __ids = [];

        for(_k in _tmp_attributes_datas){
            if(
                typeof _tmp_attributes_datas[_k].properties.id !== 'undefined'
                && _tmp_attributes_datas[_k].properties.id > 0
                && __ids.indexOf( _tmp_attributes_datas[_k].properties.id ) == -1
            )
                __ids.push( _tmp_attributes_datas[_k].properties.id );

        }

        $('span#map-rooms-count').text( Number( __ids.length ) );

        var _URL    = decodeURIComponent(_ROOM_COUNT).replace('{B}', _b_id);
        _URL        = _URL.replace('{L}', _level);

        $.ajax({
            method  : 'GET',
            url     : _URL,
            dataType: 'json',
            success : function(data){
                if(data && data.count)
                {
                    $('span#db-rooms-count').text( Number( data.count ) );
                }
            }
        });
    }

    $('button#add-attribute-btn').on('click', function(event){
        event.preventDefault();

        //Eğer Blank varsa ekleme.
        if($('#sidebar-properties-wrapper #table-box table tr:last-child td:contains("BLANK")').length == 0)
            var row = d3.select("#sidebar-properties-wrapper #table-box table tbody").append("tr");

        for(i in _columns)
            row.append('td').html('BLANK');

        row.append('td').html("<button class=\"btn btn-outline btn-danger btn-sm del-attribute-btn\"><span class=\"fa fa-trash-o\"></span></button>");

        json_save();

        table_refresh();

        editedStatus();
    });

    $(document).on('blur', '#sidebar-properties-wrapper #table-box table tbody tr td', function(evt, val){
        json_save();
    });

    $(document).on('change', '#sidebar-properties-wrapper #table-box table tbody tr td', function(evt, val){
        if(_shift_save_attributes.length > 0){
            json_save();
            editedStatus();
            return val;
        }

        var room_l = $('#sidebar-properties-wrapper #table-box table tbody tr td:contains("id")').parent('tr').index();
        var room_id = $('#sidebar-properties-wrapper #table-box table tbody tr:eq('+room_l+') td:eq(1)').text();

        if(room_l >= 0 && typeof room_id != 'undefined' && room_id > 0 && _b_id > 0){

            var _URL    = decodeURIComponent(_ROOM_INFO).replace('{B}', _b_id);
            _URL        = _URL.replace('{L}', _level);

            $.ajax({
                method  : 'GET',
                url     : _URL,
                data    : { id : Number(room_id) },
                dataType: 'json',
                success : function(data){

                    if(data && data.id && data.name)
                    {
                        $('#sidebar-properties-wrapper #table-box table tbody tr:eq('+room_l+') td').addClass('table-check');

                        if(data.name && data.name.length > 0){
                            var _name = xssFilters.inHTMLData(data.name);
                            $('#room-text').val(_name);
                        }

                        json_save();
                        auto_checked_colorized();
                    }else{
                        $('#sidebar-properties-wrapper #table-box table tbody tr:eq('+room_l+') td').removeClass('table-check');
                    }
                },
                error   : function(error){
                    alert('ID of the room check when problems occurred');
                }
            });

            editedStatus();
        }
    });

    $('button#sidebar-properties-wrapper-btn').on('click', function(event){
        event.preventDefault();
        if($(this).css('right') == 0 + 'px'){
            $(this).css('right', '').animate(300);
            $('#sidebar-properties-wrapper').css('width', '').css('right', '').animate(300);
        }else{
            $(this).css('right', 0).animate(300);
            $('#sidebar-properties-wrapper').css('width', 0).css('right', 0).animate(300);
        }
    });

    $('#diff-id-room-check-attribute-btn').on('change', function(event){
        event.preventDefault();

        var _this = this;

        if(!jsonOutside) return false;

        $('#ajax-loading').show(500);

        auto_checked_sending();

        $('#ajax-loading').hide(500);
    });

    $('#used-room-check-attribute-btn').on('change', function(event){
        event.preventDefault();

        var _this = this;

        if(!jsonOutside) return false;

        $('#ajax-loading').show(500);

        auto_checked_sending();

        $('#ajax-loading').hide(500);
    });

    $('#used-way-check-attribute-btn').on('change', function(event){
        event.preventDefault();

        var _this = this;

        if(!jsonWayOutside) return false;

        $('#ajax-loading').show(500);

        auto_checked_sending();

        $('#ajax-loading').hide(500);
    });

    $('#used-route-check-attribute-btn').on('change', function(event){
        event.preventDefault();

        var _this = this;

        if(!jsonWayOutside) return false;

        $('#ajax-loading').show(500);

        auto_checked_sending();

        $('#ajax-loading').hide(500);
    });

    $('#room-name-check-attribute-btn').on('change', function(event){
        event.preventDefault();

        var _this = this;

        if(!jsonWayOutside) return false;

        $('#ajax-loading').show(500);

        auto_checked_sending();

        $('#ajax-loading').hide(500);
    });

    function auto_checked_sending(){

        var ajaxReqs = [];

        if(($('#used-room-check-attribute-btn').is(":checked") || $('#diff-id-room-check-attribute-btn').is(":checked") || $('#used-way-check-attribute-btn').is(":checked")) && _b_id > 0){

            var _str = '';
            for(_i in _tmp_attributes_datas){
                if(typeof _tmp_attributes_datas[_i].properties.id !== 'undefined')
                    _str += _tmp_attributes_datas[_i].properties.id + ',';
            }

            _str = _str.slice(0, - 1);

            var _URL    = decodeURIComponent(_ROOM_INFO).replace('{B}', _b_id);
            _URL        = _URL.replace('{L}', _level);

            ajaxReqs.push($.ajax({
                method  : 'GET',
                url     : _URL,
                data    : { id : _str},
                dataType: 'json',
                success : function(data){
                    if(data && data.length > 0)
                    {
                        for(_i in data){
                            for(_y in _tmp_attributes_datas){
                                if(typeof _tmp_attributes_datas[_y].properties.id != 'undefined' &&_tmp_attributes_datas[_y].properties.id == data[_i].id){
                                    var _s = (data[_i].status && data[_i].status == true) ? true : false;
                                    _tmp_attributes_datas[_y] = {id:_tmp_attributes_datas[_y].id, properties:_tmp_attributes_datas[_y].properties, status: _s};
                                    break;
                                }
                            }
                        }
                    }
                },
                error   : function(error){
                    alert('Auto check when problems occurred');
                }
            }));

            if( $('#diff-id-room-check-attribute-btn').is(":checked") ){

                var _URL    = decodeURIComponent(_ROOM_INFO + '/?type=all').replace('{B}', _b_id);
                _URL        = _URL.replace('{L}', _level);

                ajaxReqs.push($.ajax({
                    method  : 'GET',
                    url     : _URL,
                    dataType: 'json',
                    success : function(data){
                        if(data && data.length > 0)
                        {
                            _tmp_attributes_rooms_all_datas = data;
                        }
                    },
                    error   : function(error){
                        alert('Auto check when problems occurred');
                    }
                }));
            }

        }else if($('#room-name-check-attribute-btn').is(':checked') && _b_id > 0){

            var _str = '';
            for(_i in _tmp_attributes_way_datas){
                if(typeof _tmp_attributes_way_datas[_i].properties.id !== 'undefined')
                    _str += _tmp_attributes_way_datas[_i].properties.id + ',';
            }

            _str = _str.slice(0, - 1);

            var _URL    = decodeURIComponent(_ROOM_INFO).replace('{B}', _b_id);
            _URL        = _URL.replace('{L}', _level);

            ajaxReqs.push($.ajax({
                method  : 'GET',
                url     : _URL,
                data    : { id : _str},
                dataType: 'json',
                success : function(data){
                    if(data && data.length > 0)
                    {
                        for(_i in data){
                            for(_y in _tmp_attributes_way_datas){
                                if(typeof _tmp_attributes_way_datas[_y].properties.id !== 'undefined' &&_tmp_attributes_way_datas[_y].properties.id == data[_i].id){
                                    var _s = (data[_i].status && data[_i].status == true) ? true : false;
                                    _tmp_attributes_way_datas[_y] = {id:_tmp_attributes_way_datas[_y].id, properties:_tmp_attributes_way_datas[_y].properties, status: _s, name: data[_i].name + ""};
                                    break;
                                }
                            }
                        }
                    }
                },
                error   : function(error){
                    alert('Auto check when problems occurred');
                }
            }));
        }

        //finished check ajax
        $.when.apply($, ajaxReqs).then(function() {

            if(_tmp_attributes_datas.length == 0 || _tmp_attributes_way_datas.length == 0)
                return false;

            other_function();

            auto_checked_colorized();
        });

        if(_tmp_attributes_datas.length == 0 && $('#used-route-check-attribute-btn').is(":checked")){
            auto_checked_colorized();
        }else if(_tmp_attributes_way_datas.length == 0 && $('#room-name-check-attribute-btn').is(":checked")){
            auto_checked_colorized();
        }
    }

    function other_function()
    {
        if( $('#diff-id-room-check-attribute-btn').is(":checked") ){

            var __ids = [];

            g.selectAll('path').style("stroke", function(d){
                if(
                    typeof d.geometry.type !== 'undefined'
                    && d.geometry.type == 'LineString'
                    && typeof d.properties.id !== 'undefined'
                    && d.properties.id > 0
                )
                {
                    if( __ids.indexOf( d.properties.id ) == -1 )
                        __ids.push( d.properties.id );
                }
            });

            var __msg = ''; //not found id
            var __msg2 = ''; //
            for(_y in _tmp_attributes_datas){

                if(
                    typeof _tmp_attributes_datas[_y].properties.id !== 'undefined'
                    && __ids.indexOf( _tmp_attributes_datas[_y].properties.id )
                    && _tmp_attributes_datas[_y].status === false
                ){
                    __msg += _tmp_attributes_datas[_y].properties.id + ',';
                }
            }

            var __ids2 = [];
            for(_k in _tmp_attributes_rooms_all_datas){
                if( __ids2.indexOf( _tmp_attributes_rooms_all_datas[_k].id ) == -1 )
                    __ids2.push( _tmp_attributes_rooms_all_datas[_k].id );
            }

            for(_k in __ids2){
                if(
                    typeof __ids2[_k] !== 'undefined'
                    && __ids.indexOf( __ids2[_k] ) == -1
                ){
                    __msg2 += __ids2[_k] + ',';
                }
            }

            __msg = __msg.substring(0, __msg.length - 1);
            __msg2 = __msg2.substring(0, __msg2.length - 1);

            if( __msg.length > 0 ){

                var _msg = _MESSAGE_TEMPLATE.replace('{T}', 'danger')
                    .replace('{M}', __msg + ' ID\'si DB\'ye kayıtlı değildir.')
                    .replace('{H}', '<h4><b> Room ID Check (Count:'+__msg.split(',').length+')</b></h4>');
                $('#message-box').empty();
                $('#message-box').prepend( _msg );

                setMessage('messages_' + _b_id + '_' + _level + '_lists', _msg);
            }

            if( __msg2.length > 0 ){

                var _msg2 = _MESSAGE_TEMPLATE.replace('{T}', 'danger')
                    .replace('{M}', __msg2 + ' ID\'leri harita üzerinde tanımlanmamış.')
                    .replace('{H}', '<h4><b> Room ID Check (Count:'+__msg2.split(',').length+')</b></h4>');
                $('#message-box').empty();

                $('#message-box').prepend( _msg2 );

                setMessage('messages_' + _b_id + '_' + _level + '_lists', _msg2);
            }

        }
    }

    function auto_checked_colorized()
    {
        g.selectAll('path').style("fill", function (d) {

            var _color = '';
            if( typeof d.properties.color != 'undefined' ){
                var v = d.properties.color.toString().replace('#', '');
                _color = "#" + v;
            }

            if($('#used-room-check-attribute-btn').is(":checked")){

                if(typeof d.properties.type !== 'undefined' && d.properties.type == 'room' && typeof d.properties.id !== 'undefined' && d.properties.id > 0)
                {
                    for(_y in _tmp_attributes_datas){

                        if(
                            typeof _tmp_attributes_datas[_y].properties.id !== undefined
                            && _tmp_attributes_datas[_y].properties.id == d.properties.id
                            && typeof _tmp_attributes_datas[_y].status !== 'undefined'
                            && _tmp_attributes_datas[_y].status == true
                        ){
                            _color = _colors.room_check_true; //True
                            break;
                        }
                    }
                }else if(
                    typeof d.geometry.type !== 'undefined'
                    && d.geometry.type == 'LineString'
                    && typeof d.properties.id !== 'undefined'
                    && ( d.properties.id == -1 || d.properties.id == '-1' )
                ){
                    _color = '#DF8620';
                }
            }else if($('#diff-id-room-check-attribute-btn').is(":checked")){

                if(typeof d.geometry.type !== 'undefined' && d.geometry.type == 'LineString' && typeof d.properties.id !== 'undefined' && d.properties.id > 0)
                {
                    for(_y in _tmp_attributes_datas){

                        if(
                            typeof _tmp_attributes_datas[_y].properties.id !== undefined
                            && _tmp_attributes_datas[_y].properties.id == d.properties.id
                            && _tmp_attributes_datas[_y].status === false
                        ){
                            _color = _colors.diff_id_room_check; //True
                            break;
                        }
                    }
                }

            }else{
                if ( typeof _current_json !== 'undefined' && typeof _current_json.id !== 'undefined' && _current_json.id == d.id && typeof _currentType !== 'undefined' && _currentType == 'p') {
                    _color = '#A52A2A';
                }
            }

            return _color;
        });

        way.selectAll('path').style("stroke", function (d) {

            var _return = '';

            if($('#used-way-check-attribute-btn').is(":checked")){
                if(
                    typeof d.geometry.type !== 'undefined'
                    && d.geometry.type == 'Point'
                    && typeof d.properties.id !== 'undefined'
                    && d.properties.id > 0
                )
                {
                    for(_y in _tmp_attributes_datas){
                        if(
                            typeof _tmp_attributes_datas[_y].properties.id !== undefined
                            && _tmp_attributes_datas[_y].properties.id == d.properties.id
                            && typeof _tmp_attributes_datas[_y].status !== 'undefined'
                            && _tmp_attributes_datas[_y].status == true
                        ){
                            d3.select(this).style('fill', _colors.way_check_true); //True
                            break;
                        }
                    }
                }

                if(d.geometry.type == 'LineString')
                    d3.select(this).style('stroke-width', '2');

            }else if($('#room-name-check-attribute-btn').is(":checked")){

                if(
                    typeof d.geometry.type !== 'undefined'
                    && d.geometry.type == 'Point'
                    && typeof d.properties.id !== 'undefined'
                    && d.properties.id > 0
                )
                {
                    var _tmp_status = false;
                    for(_y in _tmp_attributes_way_datas){

                        if(
                            typeof _tmp_attributes_way_datas[_y].properties.id !== 'undefined'
                            && _tmp_attributes_way_datas[_y].properties.id == d.properties.id
                            && typeof _tmp_attributes_way_datas[_y].name !== 'undefined'
                            && typeof d.properties.name !== 'undefined'
                            && _tmp_attributes_way_datas[_y].name == d.properties.name
                            && _tmp_attributes_way_datas[_y].name.length == d.properties.name.length
                        ){
                            _tmp_status = true;
                            break;
                        }

                    }

                    if(!_tmp_status){
                        d3.select(this).style('fill', _colors.room_name_check); //True
                    }
                }

                if(d.geometry.type == 'LineString')
                    d3.select(this).style('stroke-width', '2');

            }else{

                if(d.geometry.type == 'LineString' || d.geometry.type == 'Point'){

                    if(
                        typeof d.geometry.type !== 'undefined'
                        && d.geometry.type == 'LineString'
                        && typeof d.properties.id !== 'undefined'
                        && ( d.properties.id == -1 || d.properties.id == '-1' )
                    ){
                        d3.select(this).style('fill', '#DF8620');
                    }else{
                        d3.select(this).style('fill', '#07103A');
                    }

                    if(d.geometry.type == 'LineString')
                        d3.select(this).style('stroke-width', '2');

                    _return = '#07103A';
                }
                else
                    _return = '#000000';
            }

            if($('#used-route-check-attribute-btn').is(":checked")){
                for(var i=0; i<_no_match_lists.length; i++){
                    if(
                        typeof d.geometry.coordinates !== 'undefined'
                        && d.geometry.coordinates.length == 2
                        && (_no_match_lists[i][0] === d.geometry.coordinates[0] && _no_match_lists[i][1] === d.geometry.coordinates[1])
                    ){
                        d3.select(this).style('fill', _colors.route_check_true); //True
                        break;
                    }
                }
            }

            if(_return)
                return _return;
        });
    }

    $(document).on('click', 'button.del-attribute-btn', function(event){
        event.preventDefault();
        $(this).parents('tr').remove();

        json_save();

        if(_shift_save_attributes.length > 0)
            auto_checked_colorized();

        editedStatus();
    });

    function json_save(){

        if(_currentType == 'p'){

            var _tmp = {};
            for (var y = 0; y < $('#sidebar-properties-wrapper #table-box table tbody tr').length; y++) {
                _tmp[$('#sidebar-properties-wrapper #table-box table tbody tr:eq('+y+') td:eq(0)').text()] = changed_attributes($('#sidebar-properties-wrapper #table-box table tbody tr:eq('+y+') td:eq(0)').text(), $('#sidebar-properties-wrapper #table-box table tbody tr:eq('+y+') td:eq(1)').text(), true);
            };

            if(_shift_save_attributes.length > 0){

                jsonOutside.features.forEach(function (d, i) {

                    for(_k in _shift_save_attributes){

                        if( typeof d.id != 'undefined' && _shift_save_attributes[_k] === d.id){

                            for(_y in _tmp_attributes_datas){

                                if(typeof _tmp_attributes_datas[_y].id !== 'undefined' && typeof _shift_save_attributes[_k] !== 'undefined' && _tmp_attributes_datas[_y].id === _shift_save_attributes[_k]){
                                    _tmp_attributes_datas[_y] = {id:_tmp_attributes_datas[_y].id, properties:_tmp, status:true };
                                    break;
                                }

                            }

                            jsonOutside.features[i].properties = _tmp;
                        }
                    }
                });

            }else{

                jsonOutside.features.forEach(function (d, i) {

                    if( typeof _current_json.id != 'undefined' && _current_json.id === d.id){

                        for(_y in _tmp_attributes_datas){

                            if(typeof _tmp_attributes_datas[_y].id != false && typeof _current_json.id != false && _tmp_attributes_datas[_y].id == _current_json.id){
                                _tmp_attributes_datas[_y] = {id:_tmp_attributes_datas[_y].id, properties:_tmp, status:true };
                                break;
                            }

                        }

                        jsonOutside.features[i].properties = _tmp;
                    }

                });
            }
        }else{ // ccurrent Type => way[w]
            var _tmp = {};
            for (var y = 0; y < $('#sidebar-properties-wrapper #table-box table tbody tr').length; y++) {
                _tmp[$('#sidebar-properties-wrapper #table-box table tbody tr:eq('+y+') td:eq(0)').text()] = changed_attributes($('#sidebar-properties-wrapper #table-box table tbody tr:eq('+y+') td:eq(0)').text(), $('#sidebar-properties-wrapper #table-box table tbody tr:eq('+y+') td:eq(1)').text(), true);
            };

            if(_shift_save_attributes.length > 0){

                jsonWayOutside.features.forEach(function (d, i) {

                    for(_k in _shift_save_attributes){

                        if( typeof d.id !== 'undefined' && _shift_save_attributes[_k] === d.id){

                            jsonWayOutside.features[i].properties = _tmp;
                        }
                    }
                });

            }else{

                jsonWayOutside.features.forEach(function (d, i) {

                    if( typeof _current_json.id != 'undefined' && _current_json.id === d.id){

                        jsonWayOutside.features[i].properties = _tmp;
                    }

                });
            }
        }

        getRoomsCount(); //recount
    }

    $('a#save-attribute-btn').on('click', function(event){

        if( $('#sidebar-properties-wrapper #table-box table').length == 0)
        {
            alert('Please select a point on the map.');
            return false;
        }

        if(!_b_id && !$(this).val() > 0){
            alert('Please Select Buildings');
            return false;
        }

        if(typeof jsonOutside == 'undefined' || jsonOutside.length == 0)
        {
            alert('Please select building and Floor');
            return false;
        }

        json_save();

        var _data = osm_geojson.geojson2osm(jsonOutside);
        var _w_data = osm_geojson.geojson2osm(jsonWayOutside);

        var _level      = Number($('select.floors-select option:selected').attr('value'));

        var _URL    = decodeURIComponent(_MAP_SAVE + '/type={P}').replace('{B}', _b_id);

        _URL    = _URL.replace('{L}', _level);
        _F_URL    = _URL.replace('{P}', 'p');
        _URL = _URL.replace('{P}', 'y');

        if(_w_data){
            queue()
                .defer(function(url, callback) {
                    d3.html(url).header("Content-Type","application/x-www-form-urlencoded").send('POST', 'data='+_data, function(error, _result) {
                        callback(error, _result);
                    });
                }, _F_URL)
                .defer(function(url, callback) {
                    d3.html(url).header("Content-Type","application/x-www-form-urlencoded").send('POST', 'data='+_w_data, function(error, _result) {
                        callback(error, _result);
                    });
                }, _URL)
                .await(json_save_func);
        }else{
            queue()
                .defer(function(url, callback) {
                    d3.html(url).header("Content-Type","application/x-www-form-urlencoded").send('POST', 'data='+_data, function(error, _result) {
                        callback(error, _result);
                    });
                }, _F_URL)
                .await(json_save_func);
        }

        function json_save_func(error, _plan, _way){

            if(error){
                alert('Save Map when problems occurred');
                return false;
            }

            _plan = _plan.textContent;
            _way = _way.textContent;

            if(_plan.indexOf('success') > -1 && _way.indexOf('success') > -1){
                alert(_plan);
                return false;
            }else if(_plan.indexOf('success') > -1 && _way.indexOf('success') == -1){
                alert(_way);
                return false;
            }else{
                alert(_plan);
                return false;
            }
        }
    });

    $(document).on('change', 'select[name=floor_modal_lists_1]', function(event){
        event.preventDefault();

        $('select[name=floor_modal_lists_2]').html('');

        var _this = this;

        _sync_floor_lists.forEach(function(v, k){
            if(v.level !== $('option:selected', _this).val())
                $('select[name=floor_modal_lists_2]').append('<option value="'+ xssFilters.inHTMLData( v.level ).toString() +'">'+ xssFilters.inHTMLData( v.name ).toString() +'</option>')
        });

    });

    $('select[name=buildings-map]').on('change', function(event){
        App.blockUI({
            message : "Yükleniyor..."
        });
        event.preventDefault();

        _b_id   = Number($('option:selected', this).val());
        _URL    = decodeURIComponent(_FLOOR_LISTS).replace('{B}', _b_id);

        $.ajax({
            method  : 'GET',
            url     : _URL,
            dataType: 'json',
            success : function(data){
                if(data && data.length > 0)
                {

                    _sync_floor_lists = data; //put global variables

                    $('select[name=floors-map]').prop('disabled', false);
                    $('select[name=floors-map]').html('');

                    data.forEach(function(v, k){
                        $('select[name=floors-map]').append('<option value="'+ xssFilters.inHTMLData( v.level ) +'">'+ xssFilters.inHTMLData( v.name ) +'</option>')
                    });

                    $('select[name=floor_modal_lists_1]').html( $('select[name=floors-map]').html() );
                    $('select[name=floor_modal_lists_1] option:first', document).attr('selected', 'selected').parent().trigger('change');

                    $('select[name=floors-map] option:first').trigger('change');

                }else{
                    alert('An error occurred while is being towed floor information.');
                }
            },
            error   : function(error){
                alert('An error occurred while is being towed floor information.');
            }
        });
        setTimeout(App.unblockUI, 1000);
    });

    var _FLOOR_CHECK_LISTS1 = []
    _FLOOR_CHECK_LISTS2 = [];

    var _floor = null;

    $('select[name=floors-map]').on('change', function(event){
        App.blockUI({
            message : "Yükleniyor..."
        });
        event.preventDefault();

        if(!_b_id || Number(_b_id) <= 0){
            alert('Please Defined Select Building.');
            return false;
        }

        $('#ajax-loading').show(500);

        _p_name = $('option:selected', this).parent().attr('label');
        _level  = Number($('option:selected', this).attr('value'));

        $('#floors-map-title').text(xssFilters.inHTMLData(_p_name));

        var _URL    = decodeURIComponent(__URL + '/?type={P}').replace('{B}', _b_id);
        _URL        = _URL.replace('{L}', _level);
        _F_URL      = _URL.replace('{P}', 'p');
        _URL        = _URL.replace('{P}', 'y');

        queue()
            .defer(d3.xml, _F_URL, 'application/xml')
            .defer(d3.xml, _URL, 'application/xml')
            .await(json_file)

        var _URL    = decodeURIComponent(_ROOM_LISTS).replace('{B}', _b_id);
        var _URL    = _URL.replace('{L}', _level);

        $('#room-text').typeahead('destroy');

        $('#room-text').typeahead({
            ajax: {
                url: _URL,
                triggerLength: 1,
                preProcess: function (data) {
                    _search_process = true;
                    $('#room-text-btn').attr('disabled', false);
                    if (data.success === false) {
                        return false;
                    }

                    if(data.length > 0){
                        $('#room-text-btn').attr('disabled', 'disabled');
                    }else{
                        $('#room-text-btn').attr('disabled', false);
                    }

                    // We good!
                    return data;
                }
            },
            onSelect : function(item){
                var room_l = $('#sidebar-properties-wrapper #table-box table tbody tr td:contains("id")').parent('tr').index();
                //var room_class_check = $('#sidebar-properties-wrapper #table-box table tbody tr:eq('+room_l+') td.table-check:eq(1)').length;
                if(room_l >= 0){
                    $('#sidebar-properties-wrapper #table-box table tbody tr:eq('+room_l+') td:eq(1)').text(xssFilters.inHTMLData(item.value));
                    json_save();
                }
            }
        });

        $('#sidebar-properties-wrapper #table-box').empty();
        $('#room-text').val('');
        setTimeout(App.unblockUI, 1000);
    });

    $('#room-text-btn').on('click', function(event){
        event.preventDefault();

        if($('#room-text').val().length === 0)
            return false;

        var _URL    = decodeURIComponent(_ROOM_ADD).replace('{B}', _b_id);
        var _URL    = _URL.replace('{L}', _level);
        var _name   = $('#room-text').val();

        $.ajax({
            method  : 'POST',
            url     : _URL,
            dataType: 'json',
            data    : { name : _name, _token: $('meta[name="csrf_token"]').attr('content') },
            success : function(data) {
                if(data && data.status && data.id > 0)
                {
                    var room_l = $('#sidebar-properties-wrapper #table-box table tbody tr td:contains("id")').parent('tr').index();

                    if(room_l >= 0){
                        $('#sidebar-properties-wrapper #table-box table tbody tr:eq('+room_l+') td:eq(1)').text(data.id);
                        $('#room-text-btn').attr('disabled', 'disabled');
                        json_save();
                    }
                }
            },
            error   : function(error) {
                alert(JSON.stringify(error));
            }
        });
    });

    function editedStatus(){
        window.onbeforeunload = function (event) {
            var message = 'Sure you want to leave?';
            if (typeof event == 'undefined') {
                event = window.event;
            }
            if (event) {
                event.returnValue = message;
            }
            return message;
        }
    }

    function all_data_coordinates_checking(){

        if(typeof _b_id === 'undefined' && Number(_b_id) > 0){
            alert('Please Defined Select Building.');
            return false;
        }

        if(typeof _level === 'undefined'){
            alert('Please Defined Select Floor.');
            return false;
        }

        $('#coordinates-result-lists').html('');

        var _sync_tmp = [];
        _tmp_coordinates_datas = [];

        /*_sync_floor_lists.forEach(function(v, k){

         var _URL        = decodeURIComponent(__URL + '/?type=p').replace('{B}', _b_id);
         _URL        = _URL.replace('{L}', Number(v.level));

         var _fnc = function() {
         return $.ajax({
         method  : 'GET',
         url     : _URL,
         dataType: 'xml',
         cache   : true
         });
         };

         var _URL2        = decodeURIComponent(_ROOM_LOCATION_LISTS).replace('{B}', _b_id);
         _URL2        = _URL2.replace('{L}', Number(v.level));

         var _fnc2 = function(){
         return $.ajax({
         method  : 'GET',
         url     : _URL2,
         dataType: 'json'
         });
         };

         var _fnc3 = function(){
         return v;
         }

         $.when(_fnc(), _fnc2(), _fnc3()).then(function( data, data2, m ) {

         var _data = osm_geojson.osm2geojson(data[0]);
         var _data_2 = data2[0];

         console.log(m);

         if(_data){

         var _ids = [];

         _data.features.forEach(function(z, i){
         if(
         typeof z.geometry.type !== 'undefined'
         && z.geometry.type == 'Point'
         && typeof z.properties.id !== 'undefined'
         && z.properties.id > 0
         ){

         var _tmp_status = false;

         if(_data_2 && _data_2.length > 0){
         for(var i=0;i<_data_2.length;i++){
         if(
         _data_2[i].id === z.properties.id
         && typeof _data_2[i].location !== 'undefined'
         && _data_2[i].location !== null
         && _data_2[i].location.toString().split(',').length == 2
         && typeof z.geometry.coordinates !== 'undefined'
         && z.geometry.coordinates.length == 2
         && _data_2[i].location.toString().split(',')[0] == z.geometry.coordinates[1]
         && _data_2[i].location.toString().split(',')[1] == z.geometry.coordinates[0]
         )
         _tmp_status = true;
         }
         }

         if(!_tmp_status){
         _ids.push({
         id  : z.properties.id,
         coordinates : z.geometry.coordinates
         });
         }
         }
         });

         if(_ids && _ids.length > 0){

         var _t = '';
         _ids.forEach(function(c, k){
         _t += c.id +',';
         });

         _t = _t.slice(0, -1);

         $('#coordinates-result-lists').prepend(
         _MESSAGE_TEMPLATE.replace('{H}', '<h4>'+xssFilters.inHTMLData(m.name)+'</h4>')
         .replace('{T}', 'danger')
         .replace('{M}', _t + ' ID\'lere sahip odaların Lokasyonları tanımlanmamış.')
         );

         _tmp_coordinates_datas.push({
         level : m.level,
         ids   : _ids
         });
         }
         }
         });

         }); */

        var _URL        = decodeURIComponent(__URL + '/?type=p').replace('{B}', _b_id);
        _URL        = _URL.replace('{L}', Number(_level));

        var _fnc = function() {
            return $.ajax({
                method  : 'GET',
                url     : _URL,
                dataType: 'xml',
                cache   : true
            });
        };

        var _URL2        = decodeURIComponent(_ROOM_LOCATION_LISTS).replace('{B}', _b_id);
        _URL2        = _URL2.replace('{L}', Number(_level));

        var _fnc2 = function(){
            return $.ajax({
                method  : 'GET',
                url     : _URL2,
                dataType: 'json'
            });
        };

        $.when(_fnc(), _fnc2()).then(function( data, data2) {

            var _data = osm_geojson.osm2geojson(data[0]);
            var _data_2 = data2[0];

            if(_data){

                var _ids = [];
                var _tmp = [];

                _data.features.forEach(function(z, i){
                    if(
                        typeof z.geometry.type !== 'undefined'
                        && z.geometry.type == 'Point'
                        && typeof z.properties.id !== 'undefined'
                        && z.properties.id > 0
                    ){

                        var _tmp_status = false;

                        if(_data_2 && _data_2.length > 0){
                            for(var i=0;i<_data_2.length;i++){
                                if(
                                    _data_2[i].id === z.properties.id
                                    && typeof _data_2[i].location !== 'undefined'
                                    && typeof z.geometry.coordinates[0] !== 'undefined'
                                    && typeof z.geometry.coordinates[1] !== 'undefined'
                                    && _data_2[i].location.toString().split(',')[0] !== false
                                    && _data_2[i].location.toString().split(',')[1] !== false
                                    && _data_2[i].location.toString().split(',')[0] == z.geometry.coordinates[1]
                                    && _data_2[i].location.toString().split(',')[1] == z.geometry.coordinates[0]
                                ){
                                    _tmp_status = true;
                                }
                            }
                        }

                        if(!_tmp_status){
                            _ids.push({
                                id  : z.properties.id,
                                coordinates : z.geometry.coordinates
                            });
                        }
                    }
                });

                if(_ids && _ids.length > 0){

                    var _t = '';
                    _ids.forEach(function(c, k){
                        _t += c.id +',';
                    });

                    _t = _t.slice(0, -1);

                    $('#coordinates-result-lists').prepend(
                        _MESSAGE_TEMPLATE.replace('{H}', '<h4><b>'+xssFilters.inHTMLData(getFloorName(_level))+' ('+_ids.length+')</b></h4>')
                            .replace('{T}', 'danger')
                            .replace('{M}', _t + ' ID\'lere sahip odaların Lokasyonları tanımlanmamış.')
                    );

                    _tmp_coordinates_datas.push({
                        level : _level,
                        ids   : _ids
                    });

                    $('#coordinate-data-update-attribute-btn').prop('disabled', false);

                }else{
                    $('#coordinates-result-lists').html(
                        _MESSAGE_TEMPLATE.replace('{H}', '')
                            .replace('{T}', 'warning')
                            .replace('{M}', xssFilters.inHTMLData(getFloorName(_level)) + ' koordinatlar güncel!')
                    );

                    $('#coordinate-data-update-attribute-btn').prop('disabled', true);
                }
            }
        });
    }

    function all_data_coordinates_updating(){
        if(!_b_id || Number(_b_id) <= 0){
            alert('Please Defined Select Building.');
            return false;
        }

        if(!_tmp_coordinates_datas || _tmp_coordinates_datas.length == 0){
            alert('Please Checking Coordinates.');
            return false;
        }

        _tmp_coordinates_datas.forEach(function(v, k){

            var _URL        = decodeURIComponent(_ROOM_LOCATION_UPDATE).replace('{B}', _b_id);
            _URL        = _URL.replace('{L}', Number(v.level));

            var _fnc = function() {
                return $.ajax({
                    method  : 'POST',
                    data    : { _token: $('meta[name="csrf_token"]').attr('content'), ids : v.ids },
                    url     : _URL,
                    dataType: 'json'
                });
            };

            $.when(_fnc()).then(function( data ) {
                if(data && typeof data.status !== 'undefined' && data.status){
                    $('#coordinates-result-lists').html(
                        _MESSAGE_TEMPLATE.replace('{H}', '')
                            .replace('{T}', 'success')
                            .replace('{M}', xssFilters.inHTMLData(getFloorName(_level)) + ' başarıyla koordinatlar güncellendi.')
                    );
                }else if(data && data.status !== 'undefined' && !data.status){
                    $('#coordinates-result-lists').html(
                        _MESSAGE_TEMPLATE.replace('{H}', '')
                            .replace('{T}', 'danger')
                            .replace('{M}', 'Problem Oluştu')
                    );
                }

                $('#ajax-modal-loading').hide(500);
            });
        });
    }

    function all_data_checking(){

        if(!_b_id || Number(_b_id) <= 0){
            alert('Please Defined Select Building.');
            return false;
        }

        if( ! ($('select[name=floor_modal_lists_1]').val() && $('select[name=floor_modal_lists_2]').val() && $('input[name=floor_modal_type]:checked').val() ) ){
            alert('Please Select First Floor, Second floor and Type.');
            return false;
        }

        var _type = $('input[name=floor_modal_type]:checked').val();
        var _floor1 = Number($('select[name=floor_modal_lists_1]').val());
        var _floor2 = Number($('select[name=floor_modal_lists_2]').val());

        var _URL        = decodeURIComponent(__URL + '/?type={P}').replace('{B}', _b_id);
        _URL        = _URL.replace('{L}', _floor1);
        _URL        = _URL.replace('{P}', _type);

        var _URL2        = decodeURIComponent(__URL + '/?type={P}').replace('{B}', _b_id);
        _URL2        = _URL2.replace('{L}', _floor2);
        _URL2        = _URL2.replace('{P}', _type);


        /*var _scan_tmp = [];
         _sync_floor_lists.forEach(function(v, k){
         _sync_floor_lists.forEach(function(z, n){

         if( !_scan_tmp.indexOf(z.level) ){*/

        var _fnc_1 = function() {
            return $.ajax({
                method  : 'GET',
                url     : _URL,
                dataType: 'xml',
                cache   : true
            });
        };

        var _fnc_2 = function() {
            return $.ajax({
                method  : 'GET',
                url     : _URL2,
                dataType: 'xml',
                cache   : true
            });
        };

        $.when(_fnc_1(), _fnc_2()).then(function( data, data2 ) {
            check_json_file(
                {
                    level   : _floor1,
                    ajax    : data[2].responseText
                }
                ,
                {
                    level   : _floor2,
                    ajax    : data2[2].responseText
                }
                ,
                _floor1,
                _floor2,
                _type
            );
        });
    }

    function check_json_file(file1, file2, _level1, _level2, _type){

        _FLOOR_CHECK_LISTS1 = [];
        _FLOOR_CHECK_LISTS2 = [];

        var _floor1 = null,
            _floor2 = null;

        if(file1 && file1.ajax && file1.ajax){
            _floor1   = osm_geojson.osm2geojson(file1.ajax);
        }

        if(file2 && file2.ajax && file2.ajax){
            _floor2     = osm_geojson.osm2geojson(file2.ajax);
        }

        if(
            _floor1
            && _floor2
            && typeof _floor1.features !== 'undefined'
            && typeof _floor2.features !== 'undefined'
            && _floor1.features.length > 0
            && _floor2.features.length > 0
        ){

            _floor1.features.forEach(function(v, k){

                if(
                    typeof v.geometry !== 'undefined'
                    && typeof v.geometry.type !== 'undefined'
                ){

                    var type = (v.geometry.type == 'LineString') ? 'line' : 'point';

                    if(
                        typeof v.properties !== 'undefined'
                        && typeof v.properties.id !== 'undefined'
                        && (v.properties.id > 0 || v.properties.id == -1)
                    ){

                        _FLOOR_CHECK_LISTS1.push({
                            'floor'     : file1.level,
                            'id'        : v.properties.id,
                            'type'      : type,
                            'json_id'   : ( typeof v.id !== 'undefined' ) ? v.id : '0'
                        });

                    }

                }

            });

            _floor2.features.forEach(function(v, k){

                if(
                    typeof v.geometry !== 'undefined'
                    && typeof v.geometry.type !== 'undefined'
                ){

                    var type = (v.geometry.type == 'LineString') ? 'line' : 'point';

                    if(
                        typeof v.properties !== 'undefined'
                        && typeof v.properties.id !== 'undefined'
                        && (v.properties.id > 0 || v.properties.id == -1)
                    ){

                        _FLOOR_CHECK_LISTS2.push({
                            'floor'     : file2.level,
                            'id'        : v.properties.id,
                            'type'      : type,
                            'json_id'   : ( typeof v.id !== 'undefined' ) ? v.id : null
                        });

                    }

                }

            });

            if( _FLOOR_CHECK_LISTS1.length > 0 && _FLOOR_CHECK_LISTS2.length > 0 ){

                $('#message-box').html('');

                var _found = false;
                var _tmp = [];
                var _is_first = false;

                _FLOOR_CHECK_LISTS1.forEach(function(v, k){
                    _FLOOR_CHECK_LISTS2.forEach(function(v2, k2){
                        if( v.id === v2.id && v.floor !== v2.floor){

                            _found = true;

                            if(!_is_first){

                                var _msg = _MESSAGE_TEMPLATE.replace('{T}', 'danger')
                                    .replace('{M}', v.json_id + ' ' + getFloorName(v.floor) + ' ( ID:'+v.id+' ) ile Çakışma var!')
                                    .replace('{H}', '<h4><b>' + getFloorName(_level1) + ' => ' + getFloorName(_level2) + ' Karşılaştırması</b></h4>');

                                $('#message-box').prepend( _msg );

                                setMessage('messages_' + _b_id + '_' + _level + '_lists', _msg);

                                if(_level == _level1 || _level == _level2){
                                    colorizedByID( v.id, _type, v.type );
                                }

                                _is_first = true;
                            }else{

                                var _msg = _MESSAGE_TEMPLATE.replace('{T}', 'danger')
                                    .replace('{M}', v2.json_id + ' ' + getFloorName(v2.floor) + ' ( ID:'+v.id+' ) ile Çakışma var!')
                                    .replace('{H}', '<h4><b>' + getFloorName(_level1) + ' => ' + getFloorName(_level2) + ' Karşılaştırması</b></h4>');

                                $('#message-box').prepend( _msg );

                                setMessage('messages_' + _b_id + '_' + _level + '_lists', _msg);

                                if(_level == _level1 || _level == _level2){
                                    colorizedByID( v2.id, _type, v2.type );
                                }
                            }
                        }
                    });
                });

                if( !_found ){

                    var _msg = _MESSAGE_TEMPLATE.replace('{T}', 'warning').replace('{M}', 'Çakışma yok!')
                        .replace('{H}', '');

                    $('#message-box').prepend(_msg);
                }

            }

        }else{

        }
    }

    function colorizedByID(_id, _type, __type)
    {

        if(_type == 'p')

            g.selectAll('path').style("stroke", function (d) {

                if(
                    typeof d.properties.id !== 'undefined'
                    && d.properties.id == _id
                    && typeof d.geometry.type !== 'undefined'
                    && (d.geometry.type == 'LineString' || d.geometry.type == 'Point')
                ){
                    d3.select(this).style('fill', _colors.data_check_color);
                }

            });

    }

    $('#data-check-attribute-btn').on('click', function(event){
        event.preventDefault();

        $('#ajax-modal-loading').show(500);

        all_data_checking();

        $('#ajax-modal-loading').hide(500);
    });

    $('#coordinate-data-check-attribute-btn').on('click', function(event){
        event.preventDefault();

        $('#ajax-modal-loading').show(500);

        all_data_coordinates_checking();

        $('#ajax-modal-loading').hide(500);
    });

    $('#coordinate-data-update-attribute-btn').on('click', function(event){
        event.preventDefault();

        $('#ajax-modal-loading').show(500);

        all_data_coordinates_updating();
    });

    $('#all-data-check-modal').on('shown.bs.modal', function (e) {
        if( !_sync_floor_lists || _sync_floor_lists.length == 0){
            $(this).modal('hide');
            $('select[buildings-map]').focus();
        }
    });

    $('#room-text').attr('disabled', 'disabled');
    $('#room-text-btn').attr('disabled', 'disabled');

    var _h = setTimeout(function () {
        $('select[name=buildings-map] option:first').trigger('change');
        clearTimeout(_h);
    }, 500);
});