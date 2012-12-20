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
var attrib = 'Map data &copy; 2012 OpenStreetMap contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery &copy; 2012 CloudMade';
var baseTile = [new L.TileLayer(tileUrl, {maxZoom: 18,attribution:attrib})];
var map = L.map('map',{
    center: coord,
    zoom: zoom,
    layers: baseTile
});

var createSelector = function(data){
    for (i in data) {
        item = data[i];
        $('#grouptype').append($('<option value="'+item.id+'">' + item.name + '</option>'));
    }
    $('#grouptype').bind('change',function(){
        var val = this.value;
        loadGroupData(val);
    });
    loadGroupData($('#grouptype')[0].value);
};

var loadGroupData = function(id){
    $.ajax({
        'type' : 'GET',
        'url'  : 'api/rest/listtype.json/' + id,
        'dataType' : 'json',
        'success' : function(data){
            data = transformToGeoJson(data);
            if ('undefined' != typeof pointsLayer) {
                console.log('removing old layer');
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
//                pointToLayer: function (feature, latlng) {
//                    return L.circleMarker(latlng, geojsonMarkerOptions)
//                },
                onEachFeature: function (feature, pointsLayer) {
                    pointsLayer.on('click',openPopup);
                }
            }).addTo(map)
        }
    })
};

var openPopup = function(marker, foo){
    console.log(marker);
    $.ajax({
        type: 'GET',
        url: "/api/rest/usergroup.json/" + marker.target.feature.properties.id,
        dataTpye: 'json',
        success : createPopup
    });
};

var createPopup = function(data) {
    console.log(data);
    var popup = new L.Popup({offset:new L.Point(0, -20)});
    latlng = new L.LatLng(data.group.latitude,data.group.longitude);
    popup.setLatLng(latlng);
    var content = '<div class="popup">'
                + '<h1>'
                + '<a href="%url%" target="_blank">'
                + '%name%'
                + '</a>'
                + '</h1>'
                + '%contacts%'
                + '</div>';
                
    var contact = '<a class="%type%" href="%url%" target="_blank">'
                + '%value%'
                + '</a>';
    var contacts = [];
    
    if (data.group.icalendar_url) {
        contacts.push(contact.replace(/%type%/,'icalendar').replace(/%url%/,data.group.icalendar_url).replace(/%value%/,'iCal-File'));
    }
    for (i in data.contacts) {
        cont = data.contacts[i];
        contacts.push(contact.replace(/%type%/,cont.type.toLowerCase()).replace(/%url%/,cont.url).replace(/%value%/,cont.name));
    }
    contacts = contacts.join('</li><li>');
    if (contacts) {
        contacts = '<ul><li>' + contacts + '</li></ul>';
    }
    content = content.replace(/%url%/,data.group.url)
           .replace(/%name%/,data.group.name)
           .replace(/%contacts%/, contacts);
    popup.setContent(content);
    map.openPopup(popup);
};

var transformToGeoJson = function(data)
{
    var jsonGeo = {
            type : data.list.name,
            features : []
    };
    console.log(data);
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
                'id' : point.id
            }
        };
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