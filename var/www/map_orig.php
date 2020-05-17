<?php
$dur = #_GET('duration');
$dur_in = $dur;

$date = new DateTime();

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$dur = $_POST['duration'];
	$dur_in = $dur;
}

switch ($dur) {
    case 2:
        $dur = $date->modify('-1 week');
	$dur = date_format($dur, 'Y-m-d');
        break;
    case 3:
        $dur = $date->modify('-1 month');
        $dur = date_format($dur, 'Y-m-d');
        break;
    default:
	$dur = date_format($date, 'Y-m-d');
	break; 
}

require_once 'PJBS.php';
#create an instance with something like
$drv = new PJBS();
#connect to a JDBC data source with
$drv->connect("jdbc:derby://localhost:6414/gaiandb", "gaiandb", "passw0rd");

#execute a query
$sql = "select * from new com.ibm.db2j.GaianQuery('select * from runlog where day >= ''".$dur."'' order by distance desc','with_provenance') GQ";

$res = $drv->exec($sql);
$array = $drv->fetch_array($res);
?>
<html>
<head>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta charset="utf-8">
<title>Distance Travelled</title>
<style>
      html, body, #map-canvas {
        height: 85%;
	width: 85%;
        margin: 0px;
        padding: 0px
      }
</style>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <script>
// First, create an object containing LatLng and population for each city.
var citymap = {};
citymap['Hamster_1'] = {
  center: new google.maps.LatLng(51.507889, -0.099705),
  population: 2
};
var cityCircle;

function initialize() {
  // Create the map.
  var mapOptions = {
    zoom: 14,
    center: new google.maps.LatLng(51.507889, -0.099),
    mapTypeId: google.maps.MapTypeId.TERRAIN
  };

  var map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
  // Construct the circle for each value in citymap.
  // Note: We scale the area of the circle based on the population.
  for (var city in citymap) {
    var populationOptions = {
      strokeColor: '#FF0000',
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: '#FF0000',
      fillOpacity: 0.35,
      map: map,
      center: citymap[city].center,
      radius: Math.sqrt(citymap[city].population) * 100
};
    // Add the circle for this city to the map.
    cityCircle = new google.maps.Circle(populationOptions);
  }
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
<li><a href="graph/graphs.html">Graphs</a></li>
<li><a href="securemenu.php">My Account</a></li>
<li><a href="about.html">About</a></li>
</ul>
<p>

<FORM NAME ="sqlparam" METHOD ="POST" ACTION ="map.php">

<p>Duration:
<select name="duration">
  <option <?php if ($dur_in == 1) { print 'selected'; } ?> value="1">Day</option>
  <option <?php if ($dur_in == 2) { print 'selected'; } ?> value="2">Week</option>
  <option <?php if ($dur_in == 3) { print 'selected'; } ?> value="3">Month</option>
</select>
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Go">
</p>
</FORM>
<p>
<p>

<div id="map-canvas"></div>      
</body>
</html>
