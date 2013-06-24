<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
    <script src="scripts/jquery.min.js"></script>
    <style type="text/css">
      html { 
        height: 100%;
        font-family: 'Lato', sans-serif;
      }
      a:hover {
        text-decoration: None;
      }
      a {
        color: #444444;
      }
      body { 
        height: 100%;
        margin: 0; 
        padding: 0;
        font-family: 'Lato', sans-serif;
        overflow: hidden;
        background-color: #f3f4f4;
      }
      #map-canvas { 
        height: 100%
      }
      .map-label {
        background-color: #f3f4f4;
        overflow: hidden;
        z-index: 2147483647 !important;
        -webkit-transform: translateZ(1000);
        color: #333333;
        font-size: 25px;
        margin-top: -195px;
        margin-left: -60px;
        border-radius: 100px;
        height: 150px;
        width: 150px;
        border: 10px #333333 solid;
      }

      .score {
        z-index: 2147483647 !important;
        font-size: 35px;
      }
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCv3GjNM-TpvB0e5B5Uf_yTJFD4UyVRC7o&sensor=true">
    </script>
    <script src='scripts/infobox.js'></script>
    <?
      session_start();
      include("ESF_config.php");

      // Opens a connection to a MySQL server.
      $mysqli = new mysqli($server, $username, $password, $database);

      /* check connection */
      if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
      }

      $stmt = $mysqli->stmt_init();
      $stmt->prepare("SELECT `id`, `name`, `score`, `coordinates`, `image`, `url`, `kwH` FROM `GreenLeuven`");
      $stmt->execute();
      $stmt->store_result();
      $stmt->bind_result($id, $name, $score, $coordinates, $image, $url, $kwh);
      $locations = array();
      while ($stmt->fetch()) {
        array_push($locations, array('id'=>$id, 'name'=>$name, 'score'=>$score, 'coordinates'=>$coordinates, 'image'=>$image, 'url'=>$url, 'kwh'=>$kwh));
      }
      $stmt->close();

      echo("<script> window.locations = " . json_encode($locations) . "</script>");
    ?>
    <script type="text/javascript">

      function componentToHex(c) {
          var hex = c.toString(16);
          return hex.length == 1 ? "0" + hex : hex;
      }

      function rgbToHex(r, g, b) {
          return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
      }

      //simple function to aid with string templating in javascript
      function template(string,data){
        return string.replace(/%(\w*)%/g,function(m,key){
          return data.hasOwnProperty(key)?data[key]:"";
        });
      }

      function openMenu(location) {
        if (window.menu_opened && window.menu_location == location.name) {
          window.menu_opened = false;
          $('#map-canvas').stop().animate({'width':'100%'}, 500);
          $('#detail').stop().animate({'margin-top': '0px'});
          $('#detail').html('');
        } else {
          window.menu_opened = true;
          window.menu_location = location.name;
          $('#map-canvas').stop().animate({'width':'75%'}, 500);
          $('#detail').stop().animate({'margin-top': '-' + $(window).height()});
          var content = '<div style="text-align: center;"><a href="%url%"><h1 style="display: inline-block;">%name%</h1></a><hr style="width: 85%; margin-top: -10px;"><img src=%image% style="width: 85%; border-radius: 10px; margin-bottom: 25px;"></img><div style="font-size: 25px;">Energy Efficiency Score: %score%<br> Energy Consumed: %kwh% KWh</div></div>';
          $('#detail').html(template(content, {'name': location.name, 'image': location.image, 'url': location.url, 'kwh': location.kwh, 'score': location.score}));
        }
      }

      function initialize() {
        navigator.geolocation.getCurrentPosition(setCenter)
        function addMarker(map, location) {

          var r = Math.floor((255*(100-location.score))/100); 
          var g = Math.floor((255*location.score)/100);
          var color = rgbToHex(r, g, 0);
          location.coordinates = location.coordinates.replace(/ /g,'')
          var temp_location = location.coordinates.split(',')
          var latitude = parseFloat(temp_location[0]);
          var longitude = parseFloat(temp_location[1]); 
          var new_marker = new google.maps.Marker({
            position: new google.maps.LatLng(latitude, longitude),
            map: map,
            title: "",
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                strokeColor: color,
                scale: 8
            },
            animation: google.maps.Animation.DROP
          });

          var labelText = "<div class='map-label'><br><div style='max-width: 80px; margin-right: auto; margin-left: auto;'>%name%</div><div class='score' style='color: %color%;'>%score%<div></div>";
          var customText = template(labelText, {'name': location.name, 'score': location.score, 'color': color});

          var myOptions = {
             content: customText,
             boxStyle: {
               textAlign: "center",
               fontSize: "16pt",
               width: "50px"
             },
             disableAutoPan: true,
             pixelOffset: new google.maps.Size(-25, 0),
             position: map.getCenter(),
             closeBoxURL: "",
             isHidden: false,
             pane: "mapPane",
             enableEventPropagation: true
          };

          var ib = new InfoBox(myOptions);
          $('#marker' + location.id).css('color', color);

          google.maps.event.addListener(new_marker, 'mouseover', function() {
            ib.open(map, new_marker); 
          });

          google.maps.event.addListener(new_marker, 'mouseout', function() {
            ib.close();
          });

          google.maps.event.addListener(new_marker, 'click', function() {
            openMenu(location);
          });
        }
        function setCenter(position) {
          var mapOptions = {
            center: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
            zoom: 14,
            mapTypeId: google.maps.MapTypeId.ROADMAP
          };
          var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

          // Flat UI Styling -- Totally optional, but looks classy
          var style = [{
              "stylers": [{
                  "visibility": "on"
              }]
          }, {
              "featureType": "road",
                  "stylers": [{
                  "visibility": "on"
              }, {
                  "color": "#ffffff"
              }]
          }, {
              "featureType": "road.arterial",
                  "stylers": [{
                  "visibility": "on"
              }, {
                  "color": "#fee379"
              }]
          }, {
              "featureType": "road.highway",
                  "stylers": [{
                  "visibility": "on"
              }, {
                  "color": "#fee379"
              }]
          }, {
              "featureType": "landscape",
                  "stylers": [{
                  "visibility": "on"
              }, {
                  "color": "#f3f4f4"
              }]
          }, {
              "featureType": "water",
                  "stylers": [{
                  "visibility": "on"
              }, {
                  "color": "#7fc8ed"
              }]
          }, {}, {
              "featureType": "road",
                  "elementType": "labels",
                  "stylers": [{
                  "visibility": "off"
              }]
          }, {
              "featureType": "poi.park",
                  "elementType": "geometry.fill",
                  "stylers": [{
                  "visibility": "on"
              }, {
                  "color": "#83cead"
              }]
          }, {
              "elementType": "labels",
                  "stylers": [{
                  "visibility": "on"
              }]
          }, {
              "featureType": "landscape.man_made",
                  "elementType": "geometry",
                  "stylers": [{
                  "weight": 0.9
              }, {
                  "visibility": "on"
              }]
          }]

          map.setOptions({
              styles: style
          });

          for (var i = 0; i < window.locations.length; i++) {
            addMarker(map, window.locations[i]);
          }
        }
        window.menu_opened = false;
      }

      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    <script>
      $(document).ready(function() {
        setTimeout(function() {changeZIndex();}, 500);
        function changeZIndex() {
          var zIndex100 = $('*').filter(function(){ return $(this).css('z-index') === '100'; });
          zIndex100.css('z-index', '201');       
        }
      });
    </script>
  </head>
  <body>
    <div id="map-canvas">
    </div>
    <div id='detail' style='width: 25%; height: 100%; float: right; z-index: 2147483647 !important; border: 10px #444444 solid; background-color: #f3f4f4; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; 
'>
    </div>
  </body>
</html>