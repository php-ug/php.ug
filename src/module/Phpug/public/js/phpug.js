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
var geojsonLayer = new L.GeoJSON();

var coord = new L.LatLng(20,0);
var zoom  = 2;
if($.cookie("mapLat")){
    coord.lat = $.cookie("mapLat");
}
if($.cookie("mapLng")){
    coord.lng = $.cookie("mapLng");
}
if($.cookie("mapZoom")){
    zoom = $.cookie("mapZoom");
}

var map = new L.Map("map", {
    center: coord,
    zoom: zoom
});
var cloudmadeUrl = "http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    attrib = 'Map data &copy; 2012 OpenStreetMap contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery &copy; 2012 CloudMade',
cloudmade = new L.TileLayer(cloudmadeUrl, {maxZoom: 18,attribution:attrib});

// add the CloudMade layer to the map
map.addLayer(cloudmade);

map.addLayer(geojsonLayer);

// Display the name property on click
geojsonLayer.on("featureparse", function (e) {
    if (e.properties && e.properties.name){
        var content = "<h4>"+e.properties.name+"</h4>"
                    + "<ul>"
                    + "<li><a target=\"_blank\" href=\""+e.properties.url+"\">Homepage</a></li>";
        if (e.properties.ical) {
            content += "<li><a href=\""+e.properties.ical+"\">Calendar</a></li>";
        }
        content    += "</ul>"
        e.layer.bindPopup(content);
    }
});

// Populate our geojson layer with data

$.ajax({
    type: "POST",
    url: "/m/map/poi",
    dataType: "json",
    success: function (response) {
        geojsonLayer.addGeoJSON(response);
    }
});


window.onbeforeunload = function(e){
    $.cookie("mapLat", map.getCenter().lat);
    $.cookie("mapLng", map.getCenter().lng);
    $.cookie("mapZoom", map.getZoom());
}
    
$('#grouptype').change(function(){
    var t=$(this).attr('value');
    param = "";
    if(t){
        param="/"+t;
    }
    $.ajax({
        type: "POST",
        url: "/m/map/poi"+param,
        dataType: "json",
        success: function (response) {
            geojsonLayer.clearLayers();
            geojsonLayer.addGeoJSON(response);
        }
    });
});