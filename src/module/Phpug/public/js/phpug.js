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
// Defining some markers
var lightIcon = L.Icon.Default;
var darkIcon  = L.Icon.Default.extend({options: {iconUrl: '/img/phpug/marker-desat.png'}});
var redIcon   = L.Icon.Default.extend({options:{iconUrl: 'img/phpug/marker-icon-red.png'}});
var greenIcon   = L.Icon.Default.extend({options:{iconUrl: 'img/phpug/marker-icon-green.png'}});
var orangeIcon   = L.Icon.Default.extend({options:{iconUrl: 'img/phpug/marker-icon-orange.png'}});

var map = L.map('map');

var oms = new OverlappingMarkerSpiderfier(map, {keepSpiderfied: true});
oms.addListener('spiderfy', function(markers) {
    map.closePopup();
});

var openstreetmap = L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>',
    maxZoom: 18
})
// Create a tile-server for Esri-Satellite images
var esriSatellite = L.tileLayer('http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: '<a href="http://www.esri.com/">Esri</a>, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP and the GIS User Community',
    maxZoom : 18
})

// Create a point-layer from the joind.in-API
var joindin = L.layerJSON({
    // Get the stuff from this URL
    url: "https://api.joind.in/v2.1/events?filter=upcoming&verbose=yes&resultsperpage=100&tags[]=php",
    // Add a json-callback to get around the same-origin-policy
    jsonpParam : 'callback',
    // Use the "latitude" and "longitude"-properties of the events as lat/lng
    propertyLoc : ['latitude', 'longitude'],
    // Use the "name"-Property as name for each point
    propertyTitle: 'name',
    // Filter the resultset from joind.in by removing the "icon"-property
    // of each event and returning only the "events"-property
    filterData: function(e){
        for (var i = 0; i< e.events.length; i++) {
            delete e.events[i].icon;
        }
        return e.events;
    },
    // Bind a popup to each event
    buildPopup : function(e){
        var content = '<div class="popup">'
            + '<h4>'
            + '<a href="%url%" target="_blank">'
            + '%name%'
            + '</a>'
            + '</h4>'
            + '<dl><dt>Start:</dt><dd>%start%</dd><dt>End:</dt><dd>%end%</dd></dl>';

        if (center && center === e.shortname){
            map.setView(new L.LatLng(e.latitude,e.longitude), 8);
        }
        return content.replace('%url%', e.website_uri)
            .replace('%name%', e.name)
            .replace('%start%', new Date(e.start_date).toUTCString())
            .replace('%end%', new Date(e.end_date).toUTCString())
    },
    buildIcon : function(data){
        return new orangeIcon;
    },
    onEachMarker : function(e, marker) {
        oms.addMarker(marker);
        return;
    }
});

var phpug = L.layerJSON({
    url           : "api/rest/listtype.json/1",
    propertyLoc   : ['latitude', 'longitude'],
    propertyTitle : 'name',
    buildPopup    : function (data) {
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
            + '<i class="%cssClass%"></i>'
            + '</a>';
        var contacts = [];

        if (data.icalendar_url) {
            contacts.push(
                contact.replace(/%type%/, 'icalendar')
                    .replace(/%url%/, data.icalendar_url)
                    .replace(/%value%/, 'iCal-File')
                    .replace(/%cssClass%/, 'fa-calendar fa')
            );
        }

        for (i in data.contacts) {
            cont = data.contacts[i];
            contacts.push(
                contact.replace(/%type%/, cont.type.toLowerCase())
                    .replace(/%url%/, cont.url)
                    .replace(/%value%/, cont.name)
                    .replace(/%cssClass%/, cont.cssClass)
            );
        }
        if (data.edit) {
            var edit = '<a href="ug/edit/' + data.shortname + '" title="Edit"><i class="fa fa-edit"></i></a>';
            contacts.push(edit);
        }
        contacts = contacts.join('</li><li>');
        if (contacts) {
            contacts = '<ul><li>' + contacts + '</li></ul>';
        }
        content = content.replace(/%url%/, data.url)
            .replace(/%name%/, data.name)
            .replace(/%shortname%/, data.shortname)
            .replace(/%contacts%/, contacts);

        if (center && center === data.shortname){
            map.setView(new L.LatLng(data.latitude,data.longitude), 8);
        }
        return content;
    },
    filterData : function(e){
        return e.groups;
    },
    buildIcon : function(data){
        console.log(data);
        if (! data.state) {
            return new darkIcon();
        }
        return new L.Icon.Default;
    },
    onEachMarker  : function(e, marker){
        oms.addMarker(marker);
        return;
    }
});

var mentoring = L.layerJSON({
    url : 'mentoring',
    propertyLoc : ['lat', 'lon'],
    propretyTitle : 'name',
    buildPopup : function(data){
        url = 'https://github.com/phpmentoring/phpmentoring.github.com/wiki/Mentors-and-Apprentices';
        hash_mentor = '#mentors-currently-accepting-an-apprentice';
        hash_apprentice = '#apprentices-currently-accepting-mentors';
        content = '<div class="popup">'
        + '<h4>'
        + '<a href="%url%" target="_blank">'
        + '%name%'
        + '</a> '
        + '<a href="%github%"><i class="fa fa-github"></i></a>'
        + '</h4>'
        + '<h5>%location% - Looking for %looking%</h5>'
        + '<p>%description%</p>';

        if (center && center.toLowerCase() === data.github.toLowerCase()){
            map.setView(new L.LatLng(data.lat,data.lon), 8);
        }
        return content.replace('%url%', url + (data.type=='mentor'?hash_mentor:hash_apprentice))
            .replace('%name%', data.name)
            .replace('%location%', data.location)
            .replace('%github%', 'https://github.com/' + data.github)
            .replace('%description%', data.description)
            .replace('%looking%', data.type=='mentor'?'apprentices':'mentorship')
            .replace('%type%', data.type);
    },
    filterData : function(e){
        items = [];
        for(var i = 0; i< e.apprentices.length; i++) {
            if(e.apprentices[i].lat == NaN) {
                e.apprentices[i].lat = "0";
            }
            if(e.apprentices[i].lon == NaN) {
                e.apprentices[i].lon = "0";
            }
            e.apprentices[i].lat = e.apprentices[i].lat.toString();
            e.apprentices[i].lon = e.apprentices[i].lon.toString();
            items.push(e.apprentices[i]);
        }
        for(var i = 0; i< e.mentors.length; i++) {
            if(e.mentors[i].lat == NaN) {
                e.mentors[i].lat = "0";
            }
            if(e.mentors[i].lon == NaN) {
                e.mentors[i].lon = "0";
            }
            e.mentors[i].lat = e.mentors[i].lat.toString();
            e.mentors[i].lon = e.mentors[i].lon.toString();
            items.push(e.mentors[i]);
        }

        return items;
    },
    onEachMarker : function(e,marker){
        oms.addMarker(marker);
        return;
    },
    buildIcon : function(data, title){
        if (data.type == 'mentor') {
            return new redIcon;
        }
        return new greenIcon;
    }
});

map.on('popupopen', function(p){
    var shortname = p.popup.getContent().match(/"next_event_([^"]+)"/)[1];
    if (! shortname){
        return false;
    }
    pushNextMeeting(p, shortname);
    return true;
});

var getQueryParameter = function(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split('&');
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split('=');
        if (decodeURIComponent(pair[0]) == variable) {
            return decodeURIComponent(pair[1]);
        }
    }

    return false;
}

var getUriGeolocation = function(){
    try {
        var obj = {
            lat : null,
            lng : null,
            zoom: 8
        };
        obj.lat = getQueryParameter('lat');
        obj.lng = getQueryParameter('lng');
        var mZoom = getQueryParameter('zoom');
        if (mZoom) obj.zoom = mZoom;
        if (!obj.lat || !obj.lng) return false;

        return obj;
    }catch(e){
        return false;
    }
}

var center = getQueryParameter('center');
var coord = new L.LatLng(0,0);
var zoom  = 2;
var loc = getUriGeolocation();
if(false !== loc) {
    coord.lat = loc.lat;
    coord.lng = loc.lng;
    zoom      = loc.zoom;
} else if($.cookie("map")){
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

map.setView(coord, zoom)
   .addLayer(openstreetmap);

switch(window.location.hash) {
    case '#mentoring':
        map.addLayer(mentoring);
        break;
    case '#events':
    case 'event':
    case 'joindin':
    case 'joind.in':
        map.addLayer(joindin);
        break;
    default:
        map.addLayer(phpug);
        break;
}

L.control.layers({
    'OpenStreetMap' : openstreetmap,
    'Satellite': esriSatellite
},{
    'PHP-Usergroups' : phpug,
    'joind.in' : joindin,
    'PHP-Mentoring' : mentoring
},{
    'position' : 'bottomleft'
}).addTo(map);

var oms = new OverlappingMarkerSpiderfier(map, {keepSpiderfied: true});
oms.addListener('spiderfy', function(markers) {
    map.closePopup();
});
oms.clearListeners('click');
oms.addListener('click', function(marker) {
    var info = getContent(marker);
    popup.setContent(info.desc);
    popup.setLatLng(marker.getLatLng());
    map.openPopup(popup, info.shortname);
});


new L.Control.GeoSearch({
    provider: new L.GeoSearch.Provider.OpenStreetMap(),
    position: 'topcenter',
    showMarker: false,
    retainZoomLevel: true
}).addTo(map);



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

window.onbeforeunload = function(e){
    $.cookie("map", JSON.stringify({lat:map.getCenter().lat, lng:map.getCenter().lng, zoom:map.getZoom()}));
};
