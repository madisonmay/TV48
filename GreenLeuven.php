<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map-canvas { height: 100% }
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCv3GjNM-TpvB0e5B5Uf_yTJFD4UyVRC7o&sensor=true">
    </script>
    <script src='scripts/infobox.js'></script>
    <script type="text/javascript">
      function initialize() {
        navigator.geolocation.getCurrentPosition(setCenter)
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
        }
      }
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
    <div id="map-canvas"/>
  </body>
</html>