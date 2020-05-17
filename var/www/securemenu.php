<?PHP
session_start();
if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
	header ("Location: /login/login3.php");
}
$guid = $_SESSION['guid'];
$uname = $_SESSION['uname'];
?>

<html>
<head>
<title>My Pets</title>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>

<body>
<img src="wrr.gif" alt="world rodent racing">
<ul>
<li><a href="index.html">Home</a></li>
<li id=selected>Pet Details</li>
<li><A HREF = /images/image_gallery.php>My pictures</A></li>
<li><A HREF = control.php>Control</A></li>
<?php if ($uname == "'siteadmin'") { print '<li><A HREF = /temp/fileupload.php>Upload File</A></li>'; } ?>
<li><a href="/login/logout.php">Logout</a></li>
</ul>
<p>
<h2>My Pets</h2>
<p>
<?php
$flagico = array ( 1=> "uk.jpg", 2=> "usa.jpg", 3=> "canada.jpg", 4=> "spain.jpg", 5=> "france.jpg", 6=> "arg.jpg", 7=> "aus.jpg", 8=> "brazil.jpg", 9=> "china.jpg", 10=> "italy.jpg", 11=> "rus.jpg");
require_once 'PJBS.php';
#create an instance with something like
$drv = new PJBS();
#connect to a JDBC data source with
$drv->connect("jdbc:derby://localhost:6414/gaiandb", "gaiandb", "passw0rd");
#execute a query
$sql = "select distinct * from new com.ibm.db2j.GaianQuery('select * from members where GUID = ''".$guid."''') GQ"; 
$res = $drv->exec($sql);
$array = $drv->fetch_array($res);
    echo '<table id="leaderboard" width=65%><th>Pet Name</th><th>City</th><th>Age (mths)</th><th>Sex</th><th>Species</th><th>Breed</th><th width=5></th><th width=5></th></tr>';
    if ($array[PETNAME] == NULL) {
       echo '<tr><td>None</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
    } else {
    $topnum = 25;
    $num = 0;
    while ($num != $topnum) {
	echo '<tr><td>'.$array[PETNAME].'</td><td><img src="/images/'.$flagico[$array[LOC_ID]].'" width="20" height="12">  '.$array[CITY].'</td><td>'.$array[PETAGE].'</td><td>'.$array[PETSEX].'</td><td>'.$array[PETTYPE].'</td><td>'.$array[BREED].'</td>';
     echo "<td><a href='edit.php?petname=".$array[PETNAME]."'>Edit</a></td>";
     echo "<td><a href='remove.php?petname=".$array[PETNAME]."'>Remove</a></td>";
	echo "</tr>";
	$num = $num + 1;
	$array = $drv->fetch_array($res);
	if ($array[PETNAME] == NULL) {
		$num = 25;
	}
     }
     }
    echo '</table>';
?>
<p>
<ul><li><A HREF = register.php>Register pet</A></li></ul>
<p>
<h2>Recent runs</h2>
<p>
<?php
$sql = "select distinct * from new com.ibm.db2j.GaianQuery('select * from runlog where GUID = ''".$guid."'' ORDER BY DAY DESC') GQ ORDER BY DAY DESC"; 
$res = $drv->exec($sql);
$array = $drv->fetch_array($res);
    echo '<table id="leaderboard" width=65%><th>Date</th><th>Pet Name</th><th>Distance (m)</th><th>Top Speed (m/s)</th></tr>';
    if ($array[PETNAME] == NULL) {
       echo '<tr><td>None</td><td></td><td></td><td></td></tr>';
    } else {
    $topnum = 5;
    $num = 0;
    while ($num != $topnum) {
	echo '<tr><td>'.$array[DAY].'</td><td>'.$array[PETNAME].'</td><td>'.$array[DISTANCE].'</td><td>'.$array[TOPSPEED].'</td></tr>';
	$num = $num + 1;
	$array = $drv->fetch_array($res);
	if ($array[PETNAME] == NULL) {
		$num = 5;
	}
     }
     }
    echo '</table>';
?>
<p>
<h2>Recent wins</h2>
<p>
<?php
$sql = "select distinct * from new com.ibm.db2j.GaianQuery('select * from winners where GUID = ''".$guid."'' ORDER BY DAY DESC') GQ ORDER BY DAY DESC"; 
$res = $drv->exec($sql);
$array = $drv->fetch_array($res);
    echo '<table id="leaderboard" width=65%><th>Date</th><th>Pet Name</th><th>Distance (m)</th><th>Top Speed (m/s)</th></tr>';
    if ($array[PETNAME] == NULL) {
       echo '<tr><td>None</td><td></td><td></td><td></td></tr>';
    } else {
    $topnum = 5;
    $num = 0;
    while ($num != $topnum) {
	echo '<tr><td>'.$array[DAY].'</td><td>'.$array[PETNAME].'</td><td>'.$array[DISTANCE].'</td><td>'.$array[TOPSPEED].'</td></tr>';
	$num = $num + 1;
	$array = $drv->fetch_array($res);
	if ($array[PETNAME] == NULL) {
		$num = 5;
	}
     }
     }
    echo '</table>';
?>
</body>
</html>

