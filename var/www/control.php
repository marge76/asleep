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

#get petnames
$sql = "select distinct * from new com.ibm.db2j.GaianQuery('select petname from members where username='{$uname}'') GQ";
$res = $drv->exec($sql);
$array = $drv->fetch_array($res);
$petarray = array();
while ($array[PETNAME] != NULL) {
	array_push($petarray, $array[PETNAME]);
	$array = $drv->fetch_array($res);
}

#get Start Distances for Pets
$date = new DateTime();
$dur = date_format($date, 'Y-m-d');
$startdist = array();
$petarcnt = count($petarray);
for ($p=0; $p<$petarcnt; $p++) {
	$sql = "select distinct * from new com.ibm.db2j.GaianQuery('select distance from runlog where guid=''{$guid}'' and petname=''{$petarray[$p]}'' and day = ''{$dur}''') GQ order by distance desc";
	$res = $drv->exec($sql);
	$array = $drv->fetch_array($res);
	if ($array[DISTANCE]!=NULL) {
	     $startdist[$p] = floatval($array[DISTANCE]);
	} else {
		$startdist[$p] = 0.0;
	}
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
<li id=selected>Wheel</li>
<li><a href = config.php>Settings</a></li>
<li><a href = gentest.php>Test</a></li>
</ul>
<p>
<h2>Control wheel</h2>
<p>
<?php 
if (!file_exists("/home/pi/aatw/running.lck")) {
echo "<form action='startq.php' method='post'>";
?>
Username: <INPUT TYPE = 'TEXT' Name ='uname' readonly='true' value="<?PHP print $uname;?>" maxlength="20">
<?php
echo "<p>
Wheel Diameter (cm): <INPUT TYPE = 'TEXT' id = 'whldia' Name ='diameter' value='".$diameter."' maxlength='20'><p>";
        $petarrycnt = count($petarray);
        for ($x = 0; $x < $petarrycnt; $x++) {
               echo "<input type='radio' id='petrad' name='petname' value='{$petarray[$x]}' ";
                if ($petarray[$x]==$petname) {
                   echo "checked";
                } elseif ($x==0) {
                   echo "checked";
                }
               echo " onClick='setStartD(this)'>";
               echo $petarray[$x];
        }
echo "<p>
Starting Distance (m): <INPUT TYPE = 'TEXT' id='strtd' Name ='distance' readonly='true' value='".$startdist[0]."' maxlength='20'>
<p>
<INPUT TYPE = 'Submit' Name = 'StartMon'  VALUE = 'Start Recording'>
</form>";
} else { 
echo "<form action='stopq.php' method='post'>
<INPUT TYPE = 'Submit' Name = 'StopMon'  VALUE = 'Stop Recording'>
</form><p>";
}
?>
<script>
function setStartD(myRadio) {
<?php
	$distList = "{".$petarray[0].":".$startdist[0];
	for ($l = 1; $l < $petarcnt; $l++){
	     $distList = $distList.",".$petarray[$l].":".$startdist[$l];
	}
	$distList = $distList."};";
	print 'var distList = '.$distList;
?>
	var x = document.getElementById("strtd");
	var y = myRadio.value;
	x.value = distList[y];
}
</script>

</body>
</html>
