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

$memtwitt="";
$user_name = "root";
$pass_word = "SQLPw0rd";
$database = "aatw";
$server = "127.0.0.1";

$loc = $_GET['loc'];

if ($loc==1) {
	$id=$_GET['id'];
	if ($id != "") {
		$db_handle = mysql_connect($server, $user_name, $pass_word);
		$db_found = mysql_select_db($database, $db_handle);
		if ($db_found) {
			$SQL = "DELETE FROM fav_locs WHERE GUID='".$guid."' and lat='".$id."'";
	        $result = mysql_query($SQL);
		   mysql_close($db_handle);
		}
	} else {
		$db_handle = mysql_connect($server, $user_name, $pass_word);
		$db_found = mysql_select_db($database, $db_handle);
		if ($db_found) {
		   $addlocname = $_POST['addlocname'];
		   $addlat = $_POST['addlat'];
		   $addlong = $_POST['addlong'];
		   if (($addlocname!="")&&($addlat!="")&&($addlong!="")) {
			   $SQL = "INSERT INTO fav_locs VALUES('$guid', '$addlocname', $addlat, $addlong)";
		        $result = mysql_query($SQL);
			   mysql_close($db_handle);
		   }
		}
	}
}

$db_handle = mysql_connect($server, $user_name, $pass_word);
$db_found = mysql_select_db($database, $db_handle);
if ($db_found) {
	$SQL = "SELECT memtwitt FROM login WHERE GUID='".$guid."' and L1=".$uname;
        $result = mysql_query($SQL);
	list($memtwitt) = mysql_fetch_row($result);
	$SQL = "SELECT whld FROM login WHERE GUID='".$guid."' and L1=".$uname;
     $result = mysql_query($SQL);
	list($diameter) = mysql_fetch_row($result);
	$SQL = "SELECT * FROM fav_locs WHERE GUID='".$guid."'";
        $result = mysql_query($SQL);
	   $l = 0;
	   $locname = array();
	   $lat = array();
	   $long = array();
	   while (list($discard, $locname[$l],$lat[$l],$long[$l]) = mysql_fetch_row($result)) {
		$l++;
	   }
        mysql_close($db_handle);
}

if ($diameter=="")
	$diameter = "0.0";

$dst = $_GET['dst'];
if ($dst == 1) {
	$petname = $_GET['petname'];
	if ($petname != "") {
		$date = new DateTime();
		$dur = date_format($date, 'Y-m-d');
		#get current distance
		$sql = "select distinct * from new com.ibm.db2j.GaianQuery('select distance from runlog where guid=''{$guid}'' and petname=''{$petname}'' and day = ''{$dur}''') GQ order by distance desc";
	     $res = $drv->exec($sql);
	     $array = $drv->fetch_array($res);
		if ($array[DISTANCE]!=NULL) {
		        $startdist = floatval($array[DISTANCE]);
		} else {
				$startdist = 0.0;
		}
	}
	$diameter = $_GET['whldia'];
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST'){

	$tw = $_GET['tw'];
	$setwhld = $_GET['setwhld'];

	if ($tw==1) {
		$memtwitt = $_POST['memtwitt'];
		$memtwitt = htmlspecialchars($memtwitt);
		$db_handle = mysql_connect($server, $user_name, $pass_word);
		$db_found = mysql_select_db($database, $db_handle);
		if ($db_found) {
			$SQL = "UPDATE login SET memtwitt='".$memtwitt."' where GUID='".$guid."' and L1=".$uname;
			$result = mysql_query($SQL);
			mysql_close($db_handle);
		}
	} elseif ($setwhld==1){
		$diameter = $_POST['whld'];
		$db_handle = mysql_connect($server, $user_name, $pass_word);
		$db_found = mysql_select_db($database, $db_handle);
		if ($db_found) {
			$SQL = "UPDATE login SET whld=".$diameter." where GUID='".$guid."' and L1=".$uname;
			$result = mysql_query($SQL);
			mysql_close($db_handle);
		}
	} else {

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
<li id=selected>Control</li>
<li><a href = securemenu.php>Back</a></li>
</ul>
<p>
<h2>Configure settings</h2>
<p>
<form action="control.php?tw=1" method="post">
Twitter: <INPUT TYPE = 'TEXT' Name ='memtwitt' value="<?php print $memtwitt;?>" maxlength="30">
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Set Twitter Account">
</form>
<p>
<form action="control.php?setwhld=1" method="post">
Wheel diameter: <INPUT TYPE = 'TEXT' Name ='whld' value="<?php print $diameter;?>" maxlength="30">
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Update Wheel Diameter">
</form>
<p>
<table id="leaderboard" width=45%><tr><th>My map locations</th><th>Latitude</th><th>Longitude</th><th></th></tr>
<?php
if ($l ==0) {
	echo "<tr><td>None</td><td></td><td></td><td></td></tr>";
} else {
for ($n = 0; $n < $l; $n++) {
echo '<tr><td>'.$locname[$n].'</td><td>'.$lat[$n].'</td><td>'.$long[$n].'</td>';
     echo "<td><a href='control.php?loc=1&id=".$lat[$n]."'>Remove</a></td>";
	echo "</tr>";
}
}
?>
</table>	
<form action="control.php?loc=1" method="post">
Location <INPUT TYPE = 'TEXT' Name ='addlocname' value="" maxlength="60">
Latitude <INPUT TYPE = 'TEXT' Name ='addlat' value="" maxlength="30">
Longitude <INPUT TYPE = 'TEXT' Name ='addlong' value="" maxlength="30">
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Add Location">
</form>
<p>
<h2>Control wheel</h2>
<p>
<?php 
if (!file_exists("/home/pi/aatw/running.lck")) {
echo "<form action='startq.php' method='post'>";
?>
Username: <INPUT TYPE = 'TEXT' Name ='uname' readonly='true' value="<?PHP print $uname;?>" maxlength="20">
<p>
<?php
echo "<p>
Wheel Diameter (cm): <INPUT TYPE = 'TEXT' id = 'whldia' Name ='diameter' value='".$diameter."' maxlength='20' onchange='diachange(this)'><p>";
        $petarrycnt = count($petarray);
        for ($x = 0; $x < $petarrycnt; $x++) {
               echo "<input type='radio' id='petrad' name='petname' value='{$petarray[$x]}' ";
                if ($petarray[$x]==$petname) {
                   echo "checked";
                } elseif ($x==0) {
                   echo "checked";
                }
               echo " onClick='myFunction(this)'>";
               echo $petarray[$x];
        }
echo "<p>
<table><tr><td>Starting Distance (m): <INPUT TYPE = 'TEXT' Name ='distance' value='".$startdist."' maxlength='20'></td><td><p id='demo'></p></td></tr></table>
<p>
<INPUT TYPE = 'Submit' Name = 'StartMon'  VALUE = 'Start Recording'>
</form>";
} else { 
echo "<form action='stopq.php' method='post'>
<INPUT TYPE = 'Submit' Name = 'StopMon'  VALUE = 'Stop Recording'>
</form><p>";
}
print '<script>
    document.getElementById("demo").innerHTML = "<a href=';
print "'control.php?dst=1&petname=";
print $petarray[0];
print "&whldia=";
print $diameter;
print "'>Get Current Distance</a>";
print '";';
print "</script>";
?>
<script>
function diachange(myDia) {
    var x = document.getElementById("petrad").value;
    document.getElementById("demo").innerHTML = "<a href='control.php?dst=1&petname="+x+"&whldia="+myDia.value+"'>Get Current Distance</a>";
}
function myFunction(myRadio) {
    var x = document.getElementById("whldia").value;
    document.getElementById("demo").innerHTML = "<a href='control.php?dst=1&petname="+myRadio.value+"&whldia="+x+"'>Get Current Distance</a>";
}
</script>
<p>
<h2>Create Test Entries</h2>
<p>
<form action="control.php" method="post">
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
<INPUT TYPE = "Submit" Name = "WriteEnt"  VALUE = "Write Entry To Runlog">
</form>

</body>
</html>
