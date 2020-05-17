<?php

session_start();
if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
        header ("Location: /login/login3.php");
}
$guid = $_SESSION['guid'];
$uname = $_SESSION['uname'];
require_once 'PJBS.php';
#create an instance
$drv = new PJBS();
#connect to a JDBC data source with
$drv->connect("jdbc:derby://localhost:6414/gaiandb", "gaiandb", "passw0rd");

#Get Wheel Diameter
$user_name = "root";
$pass_word = "SQLPw0rd";
$database = "aatw";
$server = "127.0.0.1";
$db_handle = mysql_connect($server, $user_name, $pass_word);
$db_found = mysql_select_db($database, $db_handle);
if ($db_found) {
	$SQL = "SELECT whld FROM login WHERE GUID='".$guid."' and L1=".$uname;
     $result = mysql_query($SQL);
	list($diameter) = mysql_fetch_row($result);
	mysql_close($db_handle);
}

if ($diameter=="")
	$diameter = "0.0";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	$petname = $_POST['petname'];
	$diameter = $_POST['diameter'];
	$distance = $_POST['distance'];
	$tpspd = $_POST['tpspd'];
	$date = new DateTime();
	$dur = date_format($date, 'Y-m-d');
	#get current distance
	$sql = "select distinct * from new com.ibm.db2j.GaianQuery('select distance from runlog where guid=''{$guid}'' and petname=''{$petname}'' and day = ''{$dur}''') GQ order by distance desc";
	$res = $drv->exec($sql);
	$array = $drv->fetch_array($res);
	if ($array[DISTANCE]!=NULL) {
	        $currentdist = floatval($array[DISTANCE]);
	} else {
		$currentdist = 0.0;
	}
	$cir = floatval($diameter) * 3.14;
	$cir = $cir / 100;
	$fltdis = $currentdist + $cir;
	$distance = round($fltdis, 4);
	if ($currentdist == 0.0) { 
		$sql = "select * from new com.ibm.db2j.GaianQuery('insert into runlog values(''{$guid}'',''{$petname}'',''{$dur}'',{$distance},{$tpspd})') GQ";
	} else {
		$sql = "select * from new com.ibm.db2j.GaianQuery('update runlog set distance = {$distance} where GUID = ''{$guid}'' and petname = ''{$petname}'' and day = ''{$dur}''') GQ";
	}
	$res = $drv->exec($sql);
	#get current top speed
  	$sql = "select distinct * from new com.ibm.db2j.GaianQuery('select topspeed from runlog where guid=''{$guid}'' and petname=''{$petname}'' and day = ''{$dur}''') GQ order by topspeed desc";
	$res = $drv->exec($sql);
	$array = $drv->fetch_array($res);
	if ($array[TOPSPEED]!=NULL) {
	        $currenttpspd = floatval($array[TOPSPEED]);
			if ($tpspd > $currenttpspd) {
				$sql = "select * from new com.ibm.db2j.GaianQuery('update runlog set topspeed = {$tpspd} where GUID = ''{$guid}'' and petname = ''{$petname}'' and day = ''{$dur}''') GQ";
				$res = $drv->exec($sql);
			}
	}
} else {
	$distance = "0.0";
	$startdist = "0.0";
	$tpspd = "0.0";
}

#get petnames
$sql = "select distinct * from new com.ibm.db2j.GaianQuery('select petname from members where username='{$uname}'') GQ";
$res = $drv->exec($sql);
$array = $drv->fetch_array($res);
$petarray = array();
while ($array[PETNAME] != NULL) {
	array_push($petarray, $array[PETNAME]);
	$array = $drv->fetch_array($res);
}

?>
<html>
<head>
<title>System Control</title>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>

<body>
<img src="wrr.gif" alt="world rodent racing">
<ul>
<li><a href="index.html">Home</a></li>
<li><a href = securemenu.php>Pet Details</a></li>
<li><A HREF = /images/image_gallery.php>My pictures</A></li>
<li id=selected>Control</li>
<?php if ($uname == "'siteadmin'") { print '<li><A HREF = /temp/fileupload.php>Upload</A></li>'; } ?>
<li><a href="/login/logout.php">Logout</a></li>
</ul>
<p>
<ul>
<li><a href = control.php>Wheel</a></li>
<li><a href = config.php>Settings</a></li>
<li id=selected>Test</li>
</ul>
</p>
<h2>Create Test Entries</h2>
<p>
<form action="gentest.php" method="post">
Username: <INPUT TYPE = 'TEXT' Name ='uname' readonly='true' value="<?PHP print $uname;?>" maxlength="20">
<p>
<?php 
	$petarrycnt = count($petarray);
	for ($x = 0; $x < $petarrycnt; $x++) {
	       echo "<input type='radio' name='petname' value='{$petarray[$x]}' ";
		if ($petarray[$x]==$petname) {
		   echo "checked";
		} elseif ($x==0) {
		   echo "checked"; 
		}
	       echo ">";
	       echo $petarray[$x];
	}
?>
<p>
Wheel Diameter (cm): <INPUT TYPE = 'TEXT' Name ='diameter' value="<?PHP print $diameter;?>" maxlength="20">
<p>
<?php if ($distance != 0.0) {
	echo "Distance (m): <INPUT TYPE = 'TEXT' Name ='distance' readonly='true' value='{$distance}' maxlength='20'>";
	echo "<p>";
	}
?>
Top Speed (m/s): <INPUT TYPE = 'TEXT' Name = 'tpspd' value="<?PHP print $tpspd;?>" maxlength="20">
<p>
<INPUT TYPE = "Submit" Name = "WriteEnt"  VALUE = "Create Test Entry">
</form>

</body>
</html>
