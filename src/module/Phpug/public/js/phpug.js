/**
 * Copyright (c) 2011-2012 Andreas Heigl<andreas@heigl.org>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
var coord = new L.LatLng(0,0);
var zoom  = 2;
if($.cookie("map")){
    var mp = $.parseJSON($.cookie('map'));
    coord.lat = mp.lat;
    coord.lng = mp.lng;
    zoom      = mp.zoom;
}else{
    navigator.geolocation.getCurrentPosition(function(position){
        coord.lat = position.coords.latitude;
        coord.lng = position.coords.longitude;
        zoom = 8;
        map.setView(coord, zoom);
        return true;
    },function(){
        return true;
    },{timeout:3000});
}

var tileUrl = "http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";
var attrib = '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>';
var baseTile = [new L.TileLayer(tileUrl, {maxZoom: 18,attribution:attrib})];
var map = L.map('map',{
    center: coord,
    zoom: zoom,
    layers: baseTile
});
var oms = new OverlappingMarkerSpiderfier(map, {keepSpiderfied: true});

var lightIcon = L.Icon.Default;
var darkIcon  = L.Icon.Default.extend({options: {iconUrl: '/img/phpug/marker-desat.png'}});
var redIcon   = L.Icon.Default.extend({options:{iconUrl: 'img/phpug/marker-icon-orange.png'}});
var greenIcon   = L.Icon.Default.extend({options:{iconUrl: 'img/phpug/marker-desat.png'}});
var orangeIcon   = L.Icon.Default.extend({options:{iconUrl: 'img/phpug/marker-icon-orange.png'}});
var pointsLayer;

new L.Control.GeoSearch({
    provider: new L.GeoSearch.Provider.OpenStreetMap(),
    position: 'topcenter',
    showMarker: false,
    retainZoomLevel: true
}).addTo(map);

var createSelector = function(data){
    for (i in data) {
        item = data[i];
        $('#grouptype').append($('<option value="'+item.id+'">' + item.name + '</option>'));
    }
    $('#grouptype').append($('<option value="events">Events (via joind.in)</option>'));
    $('#grouptype').append($('<option value="mentoring">Mentoring (via phpmentoring.com)</option>'));
    $('#grouptype').bind('change',function(){
        var val = this.value;
        loadGroupData(val);
    });
    switch(window.location.hash) {
        case '#mentoring':
            loadGroupData('mentoring');
            $('#grouptype').val('mentoring');
            break;
        case '#events':
        case '#joindin':
            loadGroupData('events');
            $('#grouptype').val('events');
            break;
        default:
            loadGroupData($('#grouptype')[0].value);
    }
};

var loadGroupData = function(id){
    if (id == 'events') {
        loadEventData()
        return true;
    }
    if (id == 'mentoring') {
        loadMentoringData();
        return true;
    }
    $.ajax({
        'type' : 'GET',
        'url'  : 'api/rest/listtype.json/' + id,
        'dataType' : 'json',
        'success' : function(data){
            data = transformToGeoJson(data);
            if ('undefined' != typeof pointsLayer) {
                map.removeLayer(pointsLayer)
            }
            var geojsonMarkerOptions = {
                    radius: 8,
                    fillColor: "#FF6788",
                    color: "YELLOW",
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.5
            };
            pointsLayer = L.geoJson(data, {
                pointToLayer: function (feature, latlng) {
                    icon = {};
                    if (! feature.properties.active) {
                        icon = {icon : orangeIcon};
                    }
                    $.extend(icon, geojsonMarkerOptions);
                    marker = L.marker(latlng, icon);
                    oms.addMarker(marker);
                    return marker;
                },
                onEachFeature: function (feature, pointsLayer) {
                    pointsLayer.on('click',openPopup);
                }
            }).addTo(map);
        }
    })
};

var openPopup = function(marker, foo){
    $.ajax({
        type: 'GET',
        url: "/api/rest/usergroup.json/" + marker.target.feature.properties.id,
        dataTpye: 'json',
        success : createPopup
    });
};

var createPopup = function(data) {
    var popup = new L.Popup({offset:new L.Point(0, -20), minWidth : 150, maxWidth: 300});
    latlng = new L.LatLng(data.group.latitude,data.group.longitude);
    var content = '<div class="popup">'
                + '<h4>'
                + '<a href="%url%" target="_blank">'
                + '%name%'
                + '</a>'
                + '</h4>'
                + '<h5>Next Event</h5>'
                + '<div id="next_event_%shortname%" class="next_event">Getting next event...</div>'
                + '<h5>Get in touch</h5>'
                + '%contacts%'
                + '</div>'
        ;
                
    var contact = '<a href="%url%" title="%value%" target="_blank">'
                + '<i class="fa-%faicon% fa"></i>'
                + '</a>';
    var contacts = [];


    if (data.group.icalendar_url) {
        contacts.push(contact.replace(/%type%/,'icalendar').replace(/%url%/,data.group.icalendar_url).replace(/%value%/,'iCal-File').replace(/%faicon%/,'calendar'));
    }
    icons = {
        'twitter' : 'twitter',
        'github' : 'github',
        'mail'   : 'envelope',
        'facebook' : 'facebook',
        'meetup' : 'meetup',
        'google-plus' : 'google-plus',
        'bitbucket' : 'bitbucket'
    }
    for (i in data.contacts) {
        cont = data.contacts[i];
        contacts.push(contact.replace(/%type%/,cont.type.toLowerCase()).replace(/%url%/,cont.url).replace(/%value%/,cont.name).replace(/%faicon%/,icons[cont.type.toLowerCase()]));
    }
    if (data.edit) {
        var edit = '<a href="ug/edit/'+data.group.shortname +'" title="Edit"><i class="fa fa-edit"></i></a>';
        contacts.push(edit);
    }
    contacts = contacts.join('</li><li>');
    if (contacts) {
        contacts = '<ul><li>' + contacts + '</li></ul>';
    }
    content = content.replace(/%url%/,data.group.url)
           .replace(/%name%/,data.group.name)
           .replace(/%shortname%/,data.group.shortname)
           .replace(/%contacts%/, contacts);
    oms.addListener('click', function(marker) {
        popup.setContent(content);
        popup.setLatLng(marker.getLatLng());
        map.openPopup(popup, data.group.shortname);
        pushNextMeeting(popup, data.group.shortname);
    });
    oms.addListener('spiderfy', function(markers) {
        for (var i = 0, len = markers.length; i < len; i ++) markers[i].setIcon(new darkIcon());
        map.closePopup();
    });
    oms.addListener('unspiderfy', function(markers) {
        for (var i = 0, len = markers.length; i < len; i ++) markers[i].setIcon(new lightIcon());
    });
};

var pushNextMeeting = function(popup, id)
{
    $.ajax({
        type: 'POST',
        url: "/api/v1/usergroup/next-event/" + id,
        dataTpye: 'json',
        context : {'id':id, 'popup':popup},
        success : function(a){
            var content='<h6><a href="%url%">%title%</a></h6><dl title="%description%"><dt>Starts</dt><dd>%startdate%</dd><dt>Ends</dt><dd>%enddate%</dd><dt>Location:</dt><dd>%location%</dd></dl>';
            content = content.replace(/%url%/g, a.url)
                .replace(/%title%/g, a.summary)
                .replace(/%startdate%/g, a.start)
                .replace(/%enddate%/g, a.end)
                .replace(/%description%/g, a.description)
                .replace(/%location%/g, a.location)
            ;
            $('#next_event_' + this.id).html(content);
            var newContent = $('#next_event_' + this.id).closest('.popup').html();
            this.popup.setContent(newContent);
            this.popup.update();
        },
        error : function(a){
            $('#next_event_' + this.id).html('Could not retrieve an event.');
            var newContent = $('#next_event_' + this.id).parent('.popup').html();
            this.popup.setContent(newContent);
            this.popup.update();
        }
    })
}

var transformToGeoJson = function(data)
{
    var jsonGeo = {
            type : data.list.name,
            features : []
    };
    for (i in data.groups) {
        var point = data.groups[i];
        var feature = {
            'type' : 'Feature',
            'geometry' : {
                type : 'Point',
                coordinates : [point.longitude, point.latitude]
            },
            properties : {
                'name' : point.name,
                'url' : point.url,
                'id' : point.id,
                'active' : point.state===1?true:false
            }
        };
        jsonGeo.features.push(feature);
    }
    return jsonGeo;
}

/*
Event-Related stuff!
 */
var loadEventData = function(){
    $.ajax({
        'type' : 'GET',
        'url'  : 'event',
        'dataType' : 'json',
        'success' : function(data){
            data = transformEventsToGeoJson(data);
            if ('undefined' != typeof pointsLayer) {
                map.removeLayer(pointsLayer);
            }
            pointsLayer = L.geoJson(data, {
                pointToLayer: function(feature, latlng){
                    var options = {};
                    marker = L.marker(latlng, options);
                    oms.addMarker(marker);
                    return marker;
                }
            }).addTo(map);
            var popup = new L.Popup({offset:new L.Point(0, -20), minWidth : 150, maxWidth: 300});

            oms.addListener('click', function(marker){
                popup.setContent(marker.feature.desc);
                popup.setLatLng(marker.getLatLng());
                map.openPopup(popup);
            });
            oms.addListener('spiderfy', function(markers) {
                map.closePopup();
            });
            oms.addListener('unspiderfy', function(markers) {
            });
        }
    });
};

var transformEventsToGeoJson = function(data)
{
    var jsonGeo = {
        type: 'test',
        features: []
    };
    for (i in data.events) {
        var point = data.events[i];
        if (! point.longitude || isNaN(point.longitude) || ! point.latitude || isNaN(point.latitude)) {
            continue;
        }
        var content = '<div class="popup">'
            + '<h4>'
            + '<a href="%url%" target="_blank">'
            + '%name%'
            + '</a>'
            + '</h4>'
            + '<dl><dt>Start:</dt><dd>%start%</dd><dt>End:</dt><dd>%end%</dd></dl>';
        content = content.replace('%url%', point.website_uri)
            .replace('%name%', point.name)
            .replace('%start%', new Date(point.start_date))
            .replace('%end%', new Date(point.end_date))
        var feature = {
            'type' : 'Feature',
            'geometry': {
                type : 'Point',
                coordinates : [point.longitude, point.latitude]
            },
            properties : {
                name : point.name,
                url  : point.website_uri,
                start: new Date(point.start_date),
                end  : new Date(point.end_date)
            },
            desc : content
        }
        jsonGeo.features.push(feature);
    }
    return jsonGeo;
}

/* Mentoring-related stuff */

var loadMentoringData = function(){
    $.ajax({
        'type' : 'GET',
        'url'  : 'mentoring',
        'dataType' : 'json',
        'success' : function(data){
            data = transformMentoringToGeoJson(data);
            if ('undefined' != typeof pointsLayer) {
                map.removeLayer(pointsLayer);
            }
            var counter = 0;
            pointsLayer = L.geoJson(data, {
                pointToLayer: function(feature, latlng){
                    markerOptions = {icon: new darkIcon()};
                    if (feature.properties.typ == 'mentor') {
                        markerOptions = {icon: new redIcon()};
                    }
                    marker = L.marker(latlng, markerOptions);
                    oms.addMarker(marker);
                    return marker;
                }
            }).addTo(map);

            var popup = new L.Popup({offset:new L.Point(0, -20), minWidth : 150, maxWidth: 300});

            oms.addListener('click', function(marker){
                console.log(marker.feature.properties.name);
                console.log(marker);
                popup.setContent(marker.feature.desc);
                popup.setLatLng(marker.getLatLng());
                map.openPopup(popup);
            });
            oms.addListener('spiderfy', function(markers) {
                //  for (var i = 0, len = markers.length; i < len; i ++) markers[i].setIcon(new darkIcon());
                map.closePopup();
            });
            oms.addListener('unspiderfy', function(markers) {
                //    for (var i = 0, len = markers.length; i < len; i ++) markers[i].setIcon(new lightIcon());
            });
        }
    });
};

var transformMentoringToGeoJson = function(data)
{
    var jsonGeo = {
        type : 'mentoring',
        features : []
    };
    var point, content;

    for (i in data.apprentices) {
        point = data.apprentices[i];
        if (! point.lon || isNaN(point.lon) || ! point.lat || isNaN(point.lat)) {
            continue;
        }
        url = 'https://github.com/phpmentoring/phpmentoring.github.com/wiki/Mentors-and-Apprentices#apprentices-currently-accepting-mentors';
        content = '<div class="popup">'
        + '<h4>'
        + '<a href="%url%" target="_blank">'
        + '%name%'
        + '</a> '
        + '<a href="%github%"><i class="fa fa-github"></i></a>'
        + '</h4>'
        + '<h5>%location% - Looking for mentorship</h5>'
        + '<p>%description%</p>';
        content = content.replace('%url%', url)
            .replace('%name%', point.name)
            .replace('%location%', point.location)
            .replace('%github%', 'https://github.com/' + point.github)
            .replace('%description%', point.description)
            .replace('%type%', point.type);

        var feature = {
            'type' : 'Feature',
            'geometry': {
                type : 'Point',
                coordinates : [point.lon, point.lat]
            },
            properties : {
                typ  : point.type,
                name : point.name,
                desc : point.description,
                url  : url,
                github : 'https://github.com/' + point.github
            },
            desc : content
        }
        jsonGeo.features.push(feature);
    }
    for (i in data.mentors) {
        point = data.mentors[i];
        if (! point.lon || isNaN(point.lon) || ! point.lat || isNaN(point.lat)) {
            continue;
        }
        url = 'https://github.com/phpmentoring/phpmentoring.github.com/wiki/Mentors-and-Apprentices#mentors-currently-accepting-an-apprentice';
        content = '<div class="popup">'
        + '<h4>'
        + '<a href="%url%" target="_blank">'
        + '%name%'
        + '</a> '
        + '<a href="%github%"><i class="fa fa-github"></i></a>'
        + '</h4>'
        + '<h5>%location% - accepting apprentices</h5>'
        + '<p>%description%</p><p>%type%</p>';
        content = content.replace('%url%', url)
            .replace('%name%', point.name)
            .replace('%location%', point.location)
            .replace('%github%', 'https://github.com/' + point.github)
            .replace('%description%', point.description)
            .replace('%type%', point.type);

        var feature = {
            'type' : 'Feature',
            'geometry': {
                type : 'Point',
                coordinates : [point.lon, point.lat]
            },
            properties : {
                typ  : point.type,
                name : point.name,
                desc : point.description,
                url  : '',
                github : 'https://github.com/' + point.github
            },
            desc : content
        }
        jsonGeo.features.push(feature);
    }

    return jsonGeo;
}



$.ajax({
    type: 'GET',
    url: "/api/rest/listtype.json",
    dataTpye: 'json',
    success : createSelector
});



window.onbeforeunload = function(e){
    $.cookie("map", JSON.stringify({lat:map.getCenter().lat, lng:map.getCenter().lng, zoom:map.getZoom()}));
};
