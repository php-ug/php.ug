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


var RedIcon = L.Icon.Default.extend({
    options: {
        iconUrl: 'img/phpug/marker-icon-orange.png'
    }
});

var redIcon = new RedIcon();

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
                        icon = {icon : redIcon};
                    }
                    $.extend(icon, geojsonMarkerOptions);
                    return L.marker(latlng, icon)
                },
                onEachFeature: function (feature, pointsLayer) {
                    pointsLayer.on('click',openPopup);
                }
            }).addTo(map)
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
    popup.setLatLng(latlng);
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
    popup.setContent(content);
    map.openPopup(popup, data.group.shortname);

    pushNextMeeting(popup, data.group.shortname);
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

$.ajax({
    type: 'GET',
    url: "/api/rest/listtype.json",
    dataTpye: 'json',
    success : createSelector
});



window.onbeforeunload = function(e){
    $.cookie("map", JSON.stringify({lat:map.getCenter().lat, lng:map.getCenter().lng, zoom:map.getZoom()}));
};