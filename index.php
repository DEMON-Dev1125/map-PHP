<!DOCTYPE html>

<?php
session_start();
/*
if(!isset($_SESSION['id'])){
header('Location:index.php');	
}*/ ?>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>HomJab-photographers</title>
    <style>
    /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
    #map {
        height: 100%;
    }

    /* Optional: Makes the sample page fill the window. */
    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
        user-select: none;
    }

    input-controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }

    #searchInput {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 50%;
    }

    #searchInput:focus {
        border-color: #4d90fe;
    }

    /* my code */
    #draw_poly {
        z-index: 10;
        position: absolute;
        top: 5px;
        left: calc((100% + 146px)/2);
        max-height: 26px;
        background: #ffffff;
        text-align: center;
        padding: 4px 5px 3px 4px;
        cursor: pointer;
    }
    #draw_poly:hover{
        background:#cacaca;
    }

    .moving,
    .moving * {
        cursor: crosshair !important;
    }

    #distanceBox {
        position: fixed;
        z-index: 100;
        display: none;
        border: 1px solid #4d90fe;
        border-radius: 10px;
        background: #fff;
        padding-left: 10px;
        padding-right: 10px;
    }

    /*  */
    </style>
</head>

<html>

<body>
    <p>
        <label>
            <input name="group1" type="radio" value="Active" checked id="active" />
            <span>Active</span>
        </label>
        <label>
            <input name="group1" type="radio" value="Approved" id="approved" />
            <span>Approved</span>
        </label>


        <label>
            <input class="with-gap" name="group1" type="radio" value="All" id="all" />
            <span>All</span>
        </label>

        <a href="photographer_map.php" style="float:right;font-size:20px;font-weight:800;margin-right:150px">Reset Map
        </a>



        <a href="dashboard.php" style="float:right;font-size:20px;font-weight:800;margin-right:50px">Home </a>


    </p>
    <input id="searchInput" class="input-controls" type="text" placeholder="Enter a location" />
    <!-- my code -->
    <!-- <div id="custom">
        <button id="draw_poly">Draw Polgyon</button>
    </div> -->
    <div id="distanceBox">
        <p id="distanceTxt"></p>
    </div>
    <!--  -->
    <div id="map"></div>

    <script>
    document.getElementsByName('group1').forEach(function(el) {
        el.addEventListener('click', function() {
            //alert($(this).val())
            initMap($(this).val());
        });

    })
    /*	
    $('input:radio[name="group1"]').change(
        function(){	
    				 
    	
    		initMap(this.value);
    		

            
        });
    	
    	*/

    var customLabel = {
        restaurant: {
            label: 'R'
        },
        bar: {
            label: 'B'
        }
    };
    ///////////my code////////////////

    var map;
    var line;
    var infoDistance;
    var nextV = 0;
    var btnClicked = 0;
    var compPoly = 0;
    var polygon;
    var mapDiv = document.getElementById('map');
    ///////////////////////////////
    function initMap(status) {
        var gmarkers = [];


        var map = new google.maps.Map(document.getElementById('map'), {
            center: new google.maps.LatLng(36.205189, -94.779694),
            zoom: 5,
            disableDoubleClickZoom: true
        });

        ////////////my code/////create a button/////////////
        var para = document.createElement("div");
        para.id = "draw_poly";
        var node = document.createTextNode("D");
        para.appendChild(node);
        document.getElementById("map").appendChild(para);
        ////////////////////////
        var infoWindow = new google.maps.InfoWindow;

        // Change this depending on the name of your PHP or XML file
        downloadUrl('generate_xml.php?status=' + status, function(data) {
            var xml = data.responseXML;
            var markers = xml.documentElement.getElementsByTagName('marker');
            Array.prototype.forEach.call(markers, function(markerElem) {
                var id = markerElem.getAttribute('id');
                var name = markerElem.getAttribute('name');
                var phone = markerElem.getAttribute('phone');
                var email = markerElem.getAttribute('email');
                var address = markerElem.getAttribute('address');
                var status = markerElem.getAttribute('status');
                var services = markerElem.getAttribute('services');
                var type = markerElem.getAttribute('type');
                var point = new google.maps.LatLng(
                    parseFloat(markerElem.getAttribute('lat')),
                    parseFloat(markerElem.getAttribute('lng')));



                var infowincontent = document.createElement('div');
                infowincontent.appendChild(document.createElement('br'));
                infowincontent.appendChild(document.createElement('br'));

                var strong = document.createElement('strong');
                strong.textContent = 'Status:' + status
                infowincontent.appendChild(strong);
                infowincontent.appendChild(document.createElement('br'));
                infowincontent.appendChild(document.createElement('br'));
                var strong = document.createElement('strong');


                strong.textContent = 'Name:' + name
                infowincontent.appendChild(strong);
                infowincontent.appendChild(document.createElement('br'));
                infowincontent.appendChild(document.createElement('br'));

                //phone			  
                var text = document.createElement('strong');
                text.textContent = 'Phone:' + phone
                infowincontent.appendChild(text);
                infowincontent.appendChild(document.createElement('br'));
                infowincontent.appendChild(document.createElement('br'));
                //email
                var text = document.createElement('strong');
                text.textContent = 'Email:' + email
                infowincontent.appendChild(text);
                infowincontent.appendChild(document.createElement('br'));
                infowincontent.appendChild(document.createElement('br'));
                //Address
                var text = document.createElement('strong');
                text.textContent = 'Address:' + address
                infowincontent.appendChild(text);
                infowincontent.appendChild(document.createElement('br'));
                infowincontent.appendChild(document.createElement('br'));
                //services			  
                var text = document.createElement('text');
                text.textContent = 'Services:' + services
                infowincontent.appendChild(text);
                infowincontent.appendChild(document.createElement('br'));
                infowincontent.appendChild(document.createElement('br'));

                var marker = new google.maps.Marker({
                    map: map,
                    position: point
                });
                marker.addListener('click', function() {
                    infoWindow.setContent(infowincontent);
                    infoWindow.open(map, marker);
                });
            });
        });


        const drawingManager = new google.maps.drawing.DrawingManager({
            // drawingMode: google.maps.drawing.OverlayType.MARKER,
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: [
                    google.maps.drawing.OverlayType.MARKER,
                    google.maps.drawing.OverlayType.CIRCLE,
                    google.maps.drawing.OverlayType.POLYGON,
                    google.maps.drawing.OverlayType.POLYLINE,
                    google.maps.drawing.OverlayType.RECTANGLE,
                ],
            },
            markerOptions: {
                icon: "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
            },
            circleOptions: {
                fillColor: "#ffff00",
                fillOpacity: 1,
                strokeWeight: 5,
                clickable: false,
                editable: true,
                zIndex: 1,
            },
        });
        drawingManager.setMap(map);



        var input = document.getElementById('searchInput');
        // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        var geocoder = new google.maps.Geocoder();
        var autocomplete = new google.maps.places.Autocomplete(input);

        var infowindow = new google.maps.InfoWindow();
        autocomplete.addListener('place_changed', function() {
            infowindow.close();

            var place = autocomplete.getPlace();
            if (!place.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            }

            for (var i = 0; i < gmarkers.length; i++) {
                gmarkers[i].setMap(null);
            }
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(lat, lng),
                animation: google.maps.Animation.BOUNCE,
                icon: 'https://img.icons8.com/fluent/48/000000/marker-storm.png',

                draggable: true,
                map: map
            });
            gmarkers.push(marker);


            //marker.setPosition(place.geometry.location);

            map.setCenter(place.geometry.location);
            //marker.setVisible(true);
            map.setZoom(13);




        });

        /////////////my code/////////////////
        var btnDistance = document.getElementById("draw_poly");
        map.addListener('click', function(e) {
            if (line && compPoly) {
                line.setMap(null);
                line.setPath([])
                line = null;
                infoDistance.setMap(null);
                polygon.setMap(null);
            }
            if (btnClicked) {
                startNewLine(e.latLng, map);
            }
        });
        map.addListener('mousemove', function(e) {
            if (line && line.getPath() && line.getPath().getLength() > (nextV - 1) && !compPoly) {
                var distance = google.maps.geometry.spherical.computeLength(line.getPath()) / 1000;
                line.getPath().setAt(nextV, e.latLng);

                showDistanceBox(e, distance);

                // infoDistance.setPosition({
                //     lat: e.latLng.lat() + 0.1,
                //     lng: e.latLng.lng()
                // });
                // infoDistance.setContent(distance.toFixed(2) + " km");
                // infoDistance.open(map);
            }

        });
        google.maps.event.addDomListener(btnDistance, "click", () => {
            // cursor change
            mapDiv.className = 'moving';
            compPoly = 0;
            if (line) {
                line.setMap(null);
                line.setPath([])
                line = null;
                infoDistance.setMap(null);
                polygon.setMap(null);
            }
            drawingManager.setDrawingMode(null);
            btnClicked = 1;
            // btnClicked = !btnClicked;
        });
        ////////////////////////////////////
    }



    function downloadUrl(url, callback) {
        var request = window.ActiveXObject ?
            new ActiveXObject('Microsoft.XMLHTTP') :
            new XMLHttpRequest;

        request.onreadystatechange = function() {
            if (request.readyState == 4) {
                request.onreadystatechange = doNothing;
                callback(request, request.status);
            }
        };

        request.open('GET', url, true);
        request.send(null);
    }

    ///////////////my code//////////////////////
    function startNewLine(latLng, map) {


        var lineSymbol = {
            path: "M 0,-1 0,1",
            strokeOpacity: 1,
            scale: 2,
        };
        line = new google.maps.Polyline({
            draggable: false,
            editable: false,
            strokeColor: "#274cd6",
            geodesic: true,
            strokeOpacity: 0,
            icons: [{
                icon: lineSymbol,
                offset: "0",
                repeat: "10px",
            }, ],
        });
        infoDistance = new google.maps.InfoWindow({
            disableAutoPan: true
        });
        infoDistance.open(map);

        nextV = 1;
        line.setMap(map);
        line.getPath().push(latLng);
        line.addListener('click', function(e) {
            nextV++;
        })
        line.addListener('dblclick', function(e) {
            mapDiv.className = '';
            removeDistanceBox();
            polygon = new google.maps.Polygon({
                draggable: false,
                editable: false,
                fillColor: "#274cd6",
                geodesic: true,
                strokeOpacity: 0,
                path: line.getPath()
            });
            var area = google.maps.geometry.spherical.computeArea(polygon.getPath()) / 1000000;
            var firstlatlng = line.getPath().getAt(0);
            line.getPath().push(firstlatlng);
            var distance = google.maps.geometry.spherical.computeLength(line.getPath()) / 1000;

            polygon.setMap(map)
            compPoly = 1;
            btnClicked = 0;

            ///
            var bounds = new google.maps.LatLngBounds();

            for (var i = 0; i < polygon.getPath().length; i++) {

                var point = new google.maps.LatLng(polygon.getPath().getAt(i).lat(), polygon.getPath().getAt(i)
                    .lng());
                bounds.extend(point);
            }
            ///
            var content = "<b>" + distance.toFixed(2) + "km</b><br/><b>" + area.toFixed(2) + "\u33A2</b>";
            infoDistance.setPosition(bounds.getCenter());
            // infoDistance.setContent(distance.toFixed(2) + " km" + area.toFixed(2) + "\u33A2");
            infoDistance.setContent(content);

            google.maps.event.clearInstanceListeners(line);
        })

        // google.maps.event.addListener(infoDistance, 'domready', function() {
        //     var l = $('#hook').parent().parent().parent().siblings();
        //     console.log("ffff", $('#hook'));
        //     for (var i = 0; i < l.length; i++) {
        //         if ($(l[i]).css('z-index') == 'auto') {
        //             $(l[i]).css('border-radius', '16px 16px 16px 16px');
        //             $(l[i]).css('border', '2px solid red');
        //         }
        //     }
        // });
    }

    function drawPolyline() {
        btnClicked = 1;
    }

    function showDistanceBox(e, dist) {
        var distanceBox = document.getElementById("distanceBox");
        distanceBox.style.display = "block";
        var l = e.domEvent.clientX;
        var t = e.domEvent.clientY;
        distanceBox.style.top = (t + 10) + "px";
        distanceBox.style.left = (l - 10) + "px";
        document.getElementById('distanceTxt').innerHTML = dist.toFixed(2) + " km";
    }

    function removeDistanceBox() {
        document.getElementById("distanceBox").style.display = "none";
    }
    
    ////////////////////////////////////////
    function doNothing() {}
    </script>
    <script defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTEgLNTOFltl4A-swglKna_DWvuCKSRF8&callback=initMap&libraries=places,drawing">
    </script>
</body>

</html>