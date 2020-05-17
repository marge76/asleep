<?php
$dur = #_GET('duration');
$dur_in = $dur;
$sort = #_GET('sort');
$species = "all";
$petage = 0;
$petsex = "all";
$location = 0;

$date = new DateTime();

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$dur = $_POST['duration'];
	$dur_in = $dur;
	$sort = $_POST['sort'];
	$petage = $_POST['petage'];
	$petsex = $_POST['petsex'];
	$species = $_POST['species'];
	$location = $_POST['location'];
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

$sql = "select * from runlog r,members m where r.guid=m.guid and r.petname=m.petname and day >= '".$dur."'";

if ($petsex!="all") {
	$sql = $sql." and petsex = '".$petsex."'";
}

if ($location!=0) {
	$sql = $sql." and loc_id = ".$location;
}

if ($species!="all") {
	$sql = $sql." and pettype = '".$species."'";
}

if ($petage!=0) {
	switch ($petage) {
		case 1:
			$sql = $sql." and petage < 6 ";
			break;
		case 2:
			$sql = $sql." and petage between 6 and 11";
			break;
		case 3:
			$sql = $sql." and petage between 12 and 17";
			break;
		case 4:
			$sql = $sql." and petage between 18 and 23";
			break;
		case 5:
			$sql = $sql." and petage between 24 and 29";
			break;
		case 6:
			$sql = $sql." and petage => 30";
			break;
	}
}

if ($sort == 2) {
     $sql = $sql." order by topspeed desc";
} else {
	$sql = $sql." order by distance desc";
}

#execute the query
$res = $drv->exec($sql);
$array = $drv->fetch_array($res);
?>
<html>
<head>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body>

<FORM NAME ="sqlparam" METHOD ="POST" ACTION ="leaderboard.php">

Duration:
<select name="duration">
  <option <?php if ($dur_in == 1) { print 'selected'; } ?> value="1">Day</option>
  <option <?php if ($dur_in == 2) { print 'selected'; } ?> value="2">Week</option>
  <option <?php if ($dur_in == 3) { print 'selected'; } ?> value="3">Month</option>
</select>
Order by:
<select name="sort">
  <option <?php if ($sort == 1) { print 'selected'; } ?> value="1">Distance</option>
  <option <?php if ($sort == 2) { print 'selected'; } ?> value="2">Top speed</option>
</select>
<br><br>
 Species: <select name="species"><option <?php if ($species == "all") { print ' selected '; } ?> value="all">All</option><option <?php if ($species == "Chipmonk") { print ' selected '; } ?>value="Chipmonk">Chipmonk</option><option <?php if ($species == "Chinchilla") { print ' selected '; } ?>value="Chinchilla">Chinchilla</option><option <?php if ($species == "Degu") { print ' selected '; } ?>value="Degu">Degu</option><option <?php if ($species == "Gerbil") { print ' selected '; } ?>value="Gerbil">Gerbil</option><option <?php if ($species == "Hamster") { print ' selected '; } ?>value="Hamster">Hamster</option><option <?php if ($species == "Mouse") { print ' selected '; } ?>value="Mouse">Mouse</option><option <?php if ($species == "Rat") { print ' selected '; } ?>value="Rat">Rat</option></select> Age: <select name="petage"><option <?php if ($petage == 0) { print ' selected '; } ?>value="0">All</option><option <?php if ($petage == 1) { print ' selected '; } ?>value="1">0-6 months</option><option <?php if ($petage == 2) { print ' selected '; } ?>value="2">6-11 months</option><option <?php if ($petage == 3) { print ' selected '; } ?>value="3">12-17 months</option><option <?php if ($petage == 4) { print ' selected '; } ?>value="4">18-23 months</option><option <?php if ($petage == 5) { print ' selected '; } ?>value="5">24-29 months</option><option <?php if ($petage == 6) { print ' selected '; } ?>value="6">30+ months</option></select>  Sex: <select name="petsex"><option <?php if ($petsex == "all") { print ' selected '; } ?>value="all">All</option><option <option <?php if ($petsex == "M") { print ' selected '; } ?>value="M">Male</option><option <option <?php if ($petsex == "F") { print ' selected '; } ?>value="F">Female</option></select>  Location: <select name="location"><option option <?php if ($location == 0) { print ' selected '; } ?> value="0">All</option><option <?php if ($location == 1) { print ' selected '; } ?>value="1">United Kingdom</option><option option <?php if ($location == 2) { print ' selected '; } ?>value="2">USA</option><option option <?php if ($location == 3) { print ' selected '; } ?>value="3">Canada</option><option option <?php if ($location == 4) { print ' selected '; } ?>value="4">Spain</option></select>            
         <INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Go"> </p>
</FORM>

<?php 
    $flagico = array ( 1=> "uk.jpg", 2=> "usa.jpg", 3=> "canada.jpg", 4=> "spain.jpg", 5=> "france.jpg", 6=> "arg.jpg", 7=> "aus.jpg", 8=> "brazil.jpg", 9=> "china.jpg", 10=> "italy.jpg", 11=> "rus.jpg");
    echo '<table id="leaderboard" width=100%><th>Day</th><th>Owner</th><th>Pet</th><th>Location</th><th>Species</th><th>Distance (m)</th><th>Top Speed (m/s)</th></tr>';
    if ($array[PETNAME] == NULL) {
       echo '<tr><td>None</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
    } else {
    $topnum = 25;
    $num = 0;
    while ($num != $topnum) {
	echo '<tr><td>'.$array[DAY].'</td><td>'.$array[USERNAME].'</td><td><span id="highlight" title="Sex: '.$array[PETSEX].'&#13;Age: '.$array[PETAGE].' months&#13;Breed: '.$array[BREED].'">'.$array[PETNAME].'</span></td><td><img src="/images/'.$flagico[$array[LOC_ID]].'" width="20" height="12">  '.$array[CITY].'</td><td>'.$array[PETTYPE].'</td><td>'.$array[DISTANCE].'</td><td>'.$array[TOPSPEED].'</td></tr>';
	$num = $num + 1;
	$array = $drv->fetch_array($res);
	if ($array[GUID] == NULL) {
		$num = 25;
	}
    }
    }
    echo '</table>';
?>
<br>     
</body>
</html>
