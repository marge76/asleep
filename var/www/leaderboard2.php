<?php
$dur = #_GET('duration');
$dur_in = $dur;
$sort = #_GET('sort');

$date = new DateTime();

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$dur = $_POST['duration'];
	$dur_in = $dur;
	$sort = $_POST['sort'];
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
if ($sort == 2) {
        $sql = "select * from new com.ibm.db2j.GaianQuery('select * from runlog where day >= ''".$dur."'' order by topspeed desc','with_provenance') GQ";
} else {
#	$sql = "select * from new com.ibm.db2j.GaianQuery('select * from runlog where day >= ''".$dur."'' order by distance desc','with_provenance') GQ";
#}

#ranking query
#if ($sort == default) {
         $sql="select * from new com.ibm.db2j.GaianQuery
        ('select petname,distance,topspeed from runlog, 
       RANK (), OVER(Partition by petname ORDER by distance DESC)AS XRANK FROM
         ('select * from runlog where day >= ''".$dur."'' order by distance DESC')
	 As a 
         ORDER BY 4
GQ";,
}



$res = $drv->exec($sql);
$array = $drv->fetch_array($res);
?>
<html>
<head></head>
<body>

<FORM NAME ="sqlparam" METHOD ="POST" ACTION ="leaderboard2.php">

Duration:
<select name="duration">
  <option <?php if ($dur_in == 1) { print 'selected'; } ?> value="1">Day</option>
  <option <?php if ($dur_in == 2) { print 'selected'; } ?> value="2">Week</option>
  <option <?php if ($dur_in == 3) { print 'selected'; } ?> value="3">Month</option>
</select>
Sort by:
<select name="sort">
  <option <?php if ($sort == 1) { print 'selected'; } ?> value="1">Distance</option>
  <option <?php if ($sort == 2) { print 'selected'; } ?> value="2">Top speed</option>
</select>

<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Go">

</FORM>
<p>
<p>
<?php 
    echo '<table><th>GUID</th><th>Pet</th><th>Distance</th><th>Top Speed</th><th>Rank</th></tr>';
    $topnum = 25;
    $num = 0;
    while ($num != $topnum) {
	echo '<tr><td>'.$array[GUID].'</td><td>'.$array[PETNAME].'</td><td>'.$array[DISTANCE].'</td><td>'.$array[TOPSPEED].'</td><td>'.$array[xRANK].'</td></tr>';
	$num = $num + 1;
	$array = $drv->fetch_array($res);
	if ($array[GUID] == NULL) {
		$num = 25;
	}
    }
    echo '</table>';
?>

</body>
</html>
