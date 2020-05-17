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
$l = 0;
$user_name = "root";
$pass_word = "SQLPw0rd";
$database = "aatw";
$server = "127.0.0.1";
$db_handle = mysql_connect($server, $user_name, $pass_word);
$db_found = mysql_select_db($database, $db_handle);
if ($db_found) {
	$SQL = "SELECT * FROM fav_locs WHERE GUID='".$guid."'";
     $result = mysql_query($SQL);
	$locname = array();
	$arrlat = array();
	$arrlong = array();
	while (list($discard, $locname[$l],$arrlat[$l],$arrlong[$l]) = mysql_fetch_row($result)) {
		$l++;
	}
     mysql_close($db_handle);
}
if ($locname[0]=="") {
	$lat = 51.507889;
	$long = -0.099705;
} else {
	$lat = $arrlat[0];
	$long = $arrlong[0];
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
?>
<html>
<head>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta charset="utf-8">
<title>Distance Map</title>
<style>
      html, body, #map-canvas {
        height: 85%;
	width: 85%;
        margin: 0px;
        padding: 0px
      }
</style>
    <script type="text/javascript" src="//www.google.com/jsapi?autoload={'modules':[{name:'maps',version:3,other_params:''}]}"></script>
    <script type="text/javascript">
      /**
       * A distance widget that will display a circle that can be resized and will
       * provide the radius in km.
       *
       * @param {google.maps.Map} map The map to attach to.
       *
       * @constructor
       */
      function DistanceWidget(map) {
        this.set('map', map);
        this.set('position', map.getCenter());

        var marker = new google.maps.Marker({
          //draggable: true,
          //title: 'Distance'
        });

        // Bind the marker map property to the DistanceWidget map property
        marker.bindTo('map', this);

        // Bind the marker position property to the DistanceWidget position
        // property
        marker.bindTo('position', this);

        // Create a new radius widget
        var radiusWidget = new RadiusWidget();

        // Bind the radiusWidget map to the DistanceWidget map
        radiusWidget.bindTo('map', this);

        // Bind the radiusWidget center to the DistanceWidget position
        radiusWidget.bindTo('center', this, 'position');
      }
      DistanceWidget.prototype = new google.maps.MVCObject();


      /**
       * A radius widget that add a circle to a map and centers on a marker.
       *
       * @constructor
       */
       function RadiusWidget() {
         var circle = new google.maps.Circle({
		strokeColor: '#FF0000',
		strokeOpacity: 0.8,
		strokeWeight: 2,
		fillColor: '#FF0000',
		fillOpacity: 0.35
         });

         // Set the distance property value, default to 50km.
         this.set('distance', <?php $distkm = $array[DISTANCE]/1000; print $distkm; ?>);

         // Bind the RadiusWidget bounds property to the circle bounds property.
         this.bindTo('bounds', circle);

         // Bind the circle center to the RadiusWidget center property
         circle.bindTo('center', this);

         // Bind the circle map to the RadiusWidget map
         circle.bindTo('map', this);

         // Bind the circle radius property to the RadiusWidget radius property
         circle.bindTo('radius', this);
       }
       RadiusWidget.prototype = new google.maps.MVCObject();


       /**
        * Update the radius when the distance has changed.
        */
       RadiusWidget.prototype.distance_changed = function() {
         this.set('radius', this.get('distance') * 1000);
       };


      function init() {
        var mapDiv = document.getElementById('map-canvas');
        var map = new google.maps.Map(mapDiv, {
          center: new google.maps.LatLng(<?php print $lat; ?>, <?php print $long; ?>),
          zoom: <?php $zoom=16-($dur_in*2); print $zoom; ?>,
          mapTypeId: google.maps.MapTypeId.ROADMAP
          });
        var distanceWidget = new DistanceWidget(map);
      }

      google.maps.event.addDomListener(window, 'load', init);
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
  starting at Latitude: 
<INPUT TYPE = 'TEXT' id='inlat' Name ='lat' value='$lat' maxlength=12 size=10>
 Longitude:
<INPUT TYPE = 'TEXT' id='inlong' Name ='long' value='$long' maxlength=12 size=10>";
if ($l > 0) {
echo "<button type='button' onclick='mylocs()'>Locations</button>"; }
?>
<INPUT TYPE = 'Submit' Name = 'Submit1'  VALUE = 'Go'>
</FORM>
</td></tr></table>
<p id='locs'></p>
<script>
document.getElementById("locs").innerHTML = "Please ";
function mylocs() {
document.getElementById("locs").innerHTML = "Please choose one of your saved locations: <select id='sellocs'>
<?php
for ($c = 0; $c < $l; $c++) {
echo "<option value='".$arrlat[$c].",".$arrlong[$c]."'>".$locname[$c]."</option>";
}
?></select>";
}
/*function selectloc(mySelect) {
	var sp = mySelect.value.split(",");
    document.getElementById("inlat").value = sp[0];
    document.getElementById("inlong").value = sp[1];
}*/
</script>
<div id="map-canvas"></div>
</body>
</html>
