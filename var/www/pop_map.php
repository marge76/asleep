<?php
session_start();
if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
	header ("Location: mapcluster.php");
} else {
	$guid = $_SESSION['guid'];
}

$dur = 1;
$dur_in = $dur;
$petname = '';
$date = new DateTime();
$lat = 51.507889;
$long = -0.099705;

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$dur = $_POST['duration'];
	$petname = $_POST['petname'];
	$lat = $_POST['lat'];
	$long = $_POST['long'];
	$dur_in = $dur;
}

switch ($dur) {
    case 2:
        $dur = $date->modify('-1 month');
	$dur = date_format($dur, 'Y-m-d');
        break;
    case 3:
        $dur = $date->modify('-1 year');
        $dur = date_format($dur, 'Y-m-d');
        break;
    default:
        $dur = $date->modify('-1 week');
	$dur = date_format($date, 'Y-m-d');
	break; 
}

require_once 'PJBS.php';
#create an instance 
$drv = new PJBS();
#connect to a JDBC data source with
$drv->connect("jdbc:derby://localhost:6414/gaiandb", "gaiandb", "passw0rd");
#prep query
$sql = "select * from members where guid='$guid'";
#execute query
$res = $drv->exec($sql);
$array = $drv->fetch_array($res);
$petnames = array();
if ($petname == '') {
        $petname = $array[PETNAME];
        $duration = 1;
}
while ($array[PETNAME] !=NULL) {
        array_push($petnames,$array[PETNAME]);
        $array = $drv->fetch_array($res);
}

#execute a query
$sql = "select sum(distance) as DISTANCE from runlog where guid='".$guid."' and petname = '".$petname."' and day >= '".$dur."'"; 
$res = $drv->exec($sql);
$array = $drv->fetch_array($res);
echo $array[DISTANCE];
?>
<html>
<head>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta charset="utf-8">
<title>Maps</title>
<style>
      html, body, #map-canvas {
        height: 85%;
	width: 85%;
        margin: 0px;
        padding: 0px
      }
</style>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
//var populationvar = <?php print $array[DISTANCE]; ?>;

// Create an object containing LatLng - centre over South Bank.
var citymap = {};

citymap['<?php print $petname;?>'] = {
  center: new google.maps.LatLng(<?php print $lat; ?>, <?php print $long; ?>), population: <?php print $array[DISTANCE]; ?>
};
var cityCircle;

function initialize() {
  // Create the map.
  var mapOptions = {
    zoom: <?php $zoom=16-($dur_in*2); print $zoom; ?>,
    center: new google.maps.LatLng(<?php print $lat; ?>, <?php print $long; ?>),
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
      radius: Math.sqrt(citymap[city].population) * <?php print ($dur_in * $dur_in * 13); ?>
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
<li><a href="graph/graphs.php">Graphs</a></li>
<li><a href="securemenu.php">My Pets</a></li>
<li><a href="about.html">About</a></li>
</ul>
<br>
<ul>
<li><a href='mapcluster.php'>Members</a></li>
<li id=selected>My Pets</li>
</ul>
<p>
<?php
echo "<p>
<table><tr><td>";
echo "<FORM NAME ='sqlparam' METHOD ='POST' ACTION ='map.php'>
      Distance run by
<select name='petname'>";
  $petarrycnt = count($petnames);
  for ($x = 0; $x < $petarrycnt; $x++) {
        echo "<option value='";
        echo $petnames[$x];
        echo "' ";
        if ($petnames[$x]==$petname) {
                echo "selected";
        } elseif ($x==0) {
                echo "selected";
        }
        echo ">";
        echo $petnames[$x];
        echo "</option>";
  }
echo "</select>
 over the last :
<select name='duration'>
  <option ";
  if ($dur_in==1) echo " selected ";
  echo " value='1'>Week</option>
  <option ";
  if ($dur_in==2) echo " selected ";
  echo " value='2'>Month</option>
  <option ";
  if ($dur_in==3) echo " selected ";
  echo " value='3'>Year</option>
</select>
  starting at Lat: 
<INPUT TYPE = 'TEXT' Name ='lat'  value='$lat' maxlength=12 size=10>
 Long:
<INPUT TYPE = 'TEXT' Name ='long'  value='$long' maxlength=12 size=10>";?>
<INPUT TYPE = 'Submit' Name = 'Submit1'  VALUE = 'Go'>
</FORM>
</td></tr></table>
<div id="map-canvas"></div>      
</body>
</html>
