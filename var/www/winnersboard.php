<?php

require_once 'PJBS.php';
#create an instance
$drv = new PJBS();
#connect to a JDBC data source with
$drv->connect("jdbc:derby://localhost:6414/gaiandb", "gaiandb", "passw0rd");
#prep query
$db_host = 'localhost';
$db_user = 'root';
$db_pwd = 'SQLPw0rd';
$database = 'aatw';
$table = 'aatw_gallery';
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($database);
$back = 0;
$dur = $_GET['day'];
$back = $_GET['back'];
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body>
<p>
<table width="100%">
<tr><td width="50%"><h2>Latest Distance Winner</h2></td><td><h2>Latest Top Speed Winner</h2></tr>
<tr><td width="50%">
<?php
    $flagico = array ( 1=> "uk.jpg", 2=> "usa.jpg", 3=> "canada.jpg", 4=> "spain.jpg", 5=> "france.jpg", 6=> "arg.jpg", 7=> "aus.jpg", 8=> "brazil.jpg", 9=> "china.jpg", 10=> "italy.jpg", 11=> "rus.jpg");
    $sql = "select * from winners w,members m where w.guid=m.guid and w.petname=m.petname and w.distance IS NOT NULL order by day desc, distance desc";
    if ($back > 0) {
	$sql = "select * from winners w,members m where w.guid=m.guid and w.petname=m.petname and w.distance IS NOT NULL and day < '".$dur."' order by day desc, distance desc";
    }
    #execute query
    $res = $drv->exec($sql);
    $array = $drv->fetch_array($res);
    $quer = "SELECT id FROM ".$table." WHERE GUID='".$array[GUID]."' and PETNAME='".$array[PETNAME]."' LIMIT 1";
    $result = mysql_query($quer);
    if (mysql_num_rows($result) == 0) {
        $data = "no image";
    }
    else {
        list($id) = mysql_fetch_row($result);
    }

    echo '<table id="leaderboard" width=95%>';
    echo '<tr><td><img src="/images/smallgoldmedal.jpg"></td><td><img src="/images/image_gallery.php?show='.$id.'" width="200" height="130"></td></tr>';
    echo '<tr><td>Date</td><td>'.$array[DAY].'</td></tr>
	      <tr><td>Owner</td><td>'.$array[USERNAME].'</td></tr>
              <tr><td>Location</td><td><img src="/images/'.$flagico[$array[LOC_ID]].'" width="20" height="12">  '.$array[CITY].'</td></tr>
              <tr><td>Pet Name</td><td><span title="Sex: '.$array[PETSEX].'&#13;Age: '.$array[PETAGE].' months&#13;Breed: '.$array[BREED].'">'.$array[PETNAME].'</span></td></tr>
	      <tr><td>Species</td><td>'.$array[PETTYPE].'</td></tr>
              <tr><td>Distance (m)</td><td>'.$array[DISTANCE].'</td></tr>';
    echo '</table>';

?>
</td><td width="50%">

<?php
    $sql = "select * from winners w,members m where w.guid=m.guid and w.petname=m.petname and w.topspeed IS NOT NULL order by day desc, topspeed desc";
    if ($back > 0) {
	$sql = "select * from winners w,members m where w.guid=m.guid and w.petname=m.petname and w.topspeed IS NOT NULL and day < '".$dur."' order by day desc, topspeed desc";
    }
    #execute query
    $res = $drv->exec($sql);
    $array = $drv->fetch_array($res);
    $quer = "SELECT id FROM ".$table." WHERE GUID='".$array[GUID]."' and PETNAME='".$array[PETNAME]."' LIMIT 1";
    $result = mysql_query($quer);
    if (mysql_num_rows($result) == 0) {
        $data = "no image";
    }
    else {
        list($id) = mysql_fetch_row($result);
    }

    echo '<table id="leaderboard" width=95%>';
    echo '<tr><td><img src="/images/smallgoldmedal.jpg"></td><td><img src="/images/image_gallery.php?show='.$id.'" width="200" height="130"></td></tr>';
    echo '<tr><td>Date</td><td>'.$array[DAY].'</td></tr>
	      <tr><td>Owner</td><td>'.$array[USERNAME].'</td></tr>
	      <tr><td>Location</td><td><img src="/images/'.$flagico[$array[LOC_ID]].'" width="20" height="12">  '.$array[CITY].'</td></tr>
              <tr><td>Pet Name</td><td><span title="Sex: '.$array[PETSEX].'&#13;Age: '.$array[PETAGE].' months&#13;Breed: '.$array[BREED].'">'.$array[PETNAME].'</span></td></tr>
	      <tr><td>Species</td><td>'.$array[PETTYPE].'</td></tr>
              <tr><td>Top Speed (m/s)</td><td>'.$array[TOPSPEED].'</td></tr>';
    echo '</table>';

?>
</td></tr>
</table>
<table width=100%><tr><td></td><td></td><td></td><td></td>
<?php
	if ($back > 0) {
		 echo "<td align='right'><a href = 'winnersboard.php?back=0'>Latest Winners</a></td>";
	}
	$dur = $array[DAY];
	$back++;
	if ($back != 5) {
		echo "<td align='right'><a href = 'winnersboard.php?back={$back}&day={$dur}'>Previous Winners</a></td>";
	}
?>
</tr></table>
</body>
</html>
