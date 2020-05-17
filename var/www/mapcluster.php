<!DOCTYPE>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Members map</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <style type="text/css">
      body {
        margin: 0;
        padding: 10px 20px 20px;
        font-family: Arial;
        font-size: 16px;
      }

      #map-container {
        padding: 0px;
        border-width: 0px;
        border-style: solid;

        width: 85%;
      }

      #map {
        width: 85%;
        height: 75%;

      }

    </style>

    <script src="http://maps.googleapis.com/maps/api/js?v=3&amp;sensor=false"></script>
<script type="text/javascript" src="markercluster/src/data.json"></script>
<script src="markercluster/src/markerclusterer.js" type="text/javascript"></script>
    <script type="text/javascript">
      var script = '<script type="text/javascript" src="../src/markerclusterer';
      if (document.location.search.indexOf('compiled') !== -1) {
        script += '_packed';
      }
      script += '.js"><' + '/script>';
      document.write(script);
    </script>

    <script type="text/javascript">
      function initialize() {
        var center = new google.maps.LatLng(51.5286417,-0.1015987);

        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 3,
          center: center,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var markers = [];
        for (var i = 0; i < 100; i++) {
          var dataPhoto = data.photos[i];
          var latLng = new google.maps.LatLng(dataPhoto.latitude,
              dataPhoto.longitude);
          var marker = new google.maps.Marker({
            position: latLng
          });
          markers.push(marker);
        }
        var markerCluster = new MarkerClusterer(map, markers);
      }
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
 <img src="wrr.gif" alt="world rodent racing">
<ul>
<li><a href="index.html">Home</a></li>
<li><a href="winners.php">Winners</a></li>
<li id=selected>Maps</li>
<li><a href="graph/graphs.php">Graphs</a></li>
<li><a href="securemenu.php">My Pets</a></li>
<li><a href="about.html">About</a></li>
</ul>
<br>
<ul>
<?php
session_start();
if (isset($_SESSION['login']) && $_SESSION['login'] != '') {
        print "<li id=selected>Members</li>";
        print "<li><a href='map.php'>My Pets</a></li>";
} else {
        print "<li id=selected>Members</li>";
}
?>
</ul>
<p>
    <div id="map-container"><div id="map"></div></div>
 </p>
 </body>
</html>