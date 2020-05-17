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


#Set location
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


#Get current Twitter, WheelDia and Locations
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

#Set Twitter
$tw = $_GET['tw'];
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
} 

#Set Wheel Diameter
$setwhld = $_GET['setwhld'];
if ($setwhld==1){
		$diameter = $_POST['whld'];
		$db_handle = mysql_connect($server, $user_name, $pass_word);
		$db_found = mysql_select_db($database, $db_handle);
		if ($db_found) {
			$SQL = "UPDATE login SET whld=".$diameter." where GUID='".$guid."' and L1=".$uname;
			$result = mysql_query($SQL);
			mysql_close($db_handle);
		}
}
$distance = "0.0";
$startdist = "0.0";
$tpspd = "0.0";

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
<li id=selected>Settings</li>
<li><a href = gentest.php>Test</a></li>
</ul>
<p>
<h2>Configure settings</h2>
<p>
<form action="config.php?tw=1" method="post">
Twitter: <INPUT TYPE = 'TEXT' Name ='memtwitt' value="<?php print $memtwitt;?>" maxlength="30">
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Set Twitter Account">
</form>
<p>
<form action="config.php?setwhld=1" method="post">
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
     echo "<td><a href='config.php?loc=1&id=".$lat[$n]."'>Remove</a></td>";
	echo "</tr>";
}
}
?>

</table>	
<form action="config.php?loc=1" method="post">
Location <INPUT TYPE = 'TEXT' Name ='addlocname' value="" maxlength="60">
Latitude <INPUT TYPE = 'TEXT' Name ='addlat' value="" maxlength="30">
Longitude <INPUT TYPE = 'TEXT' Name ='addlong' value="" maxlength="30">
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Add Location">
</form>

</body>
</html>
