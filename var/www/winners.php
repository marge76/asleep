<?php 

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	#prepare PJBS to connect to GaianDB
	require_once 'PJBS.php';
	#create an instance
	$drv = new PJBS();
	#connect to a JDBC data source with
	$drv->connect("jdbc:derby://localhost:6414/gaiandb", "gaiandb", "passw0rd");

        #prepare tweeting winners
	require_once 'twitter.class.php';
        #Consumer keys and access tokens, OAuth
        $consumerKey = 'JEDr7LZ73orALfyuLHi3TEmJu';
        $consumerSecret = 'HYrAHqrgTWpxjvUzjzsX8z9ETcXCwTU11Abqk6wcU4JLyaqGf7';
        $accessToken = '2877989129-vO2y4EQLveLbMVozScrir0Mgxy244EZpPZfQcUp';
        $accessTokenSecret = 'TGeBbtZt5KBkXlHd7D11t0cJXwUQ8Qg73iE36jh2dLSmS';

        try {
		$twitter = new Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
	} catch (TwitterException $e) {
		echo 'Error: ' . $e->getMessage();
	}

	$date = new DateTime();
	$dur = $date->modify('-1 day');
	$dur = date_format($dur, 'Y-m-d');
	$sql = "select * from new com.ibm.db2j.GaianQuery('select * from runlog where day = ''".$dur."'' order by topspeed desc') GQ";
	$res = $drv->exec($sql);
	$array = $drv->fetch_array($res);
	$maxtpspd = $array[TOPSPEED];
	$sql = "select * from new com.ibm.db2j.GaianQuery('insert into winners VALUES(''".$array[GUID]."'', ''".$array[PETNAME]."'',''".$dur."'',NULL,".$array[TOPSPEED].")') GQ";
	$res = $drv->exec($sql);

        $user_name = "root";
        $pass_word = "SQLPw0rd";
        $database = "aatw";
        $server = "127.0.0.1";

	$tpspd_memtwitt = "";
        $db_handle = mysql_connect($server, $user_name, $pass_word);
        $db_found = mysql_select_db($database, $db_handle);
        if ($db_found) {
                $SQL = "SELECT L1, memtwitt FROM login WHERE GUID='".$array[GUID]."'";
                $result = mysql_query($SQL);
                list($uname, $tpspd_memtwitt) = mysql_fetch_row($result);
                mysql_close($db_handle);
       }
        if ($tpspd_memtwitt == "") {
               $tpspd_memtwitt = $uname;
        }

	#Tweet Top Speed Winner	
	$twmsg = "Daily Top Speed Winner: {$array[PETNAME]} running at a top speed of {$maxtpspd}m/s. Congratulations, {$tpspd_memtwitt}"; 
	try {
		$tweet = $twitter->send($twmsg);
	} catch (TwitterException $e) {
                echo 'Error: ' . $e->getMessage();
        }
        
	$sql = "select * from new com.ibm.db2j.GaianQuery('select * from runlog where day = ''".$dur."'' order by distance desc') GQ";
	$res = $drv->exec($sql);
	$array = $drv->fetch_array($res);
	$maxdist = $array[DISTANCE];
	$sql = "select * from new com.ibm.db2j.GaianQuery('insert into winners VALUES(''".$array[GUID]."'', ''".$array[PETNAME]."'',''".$dur."'',".$array[DISTANCE].",NULL)','with_provenance') GQ";
	$res = $drv->exec($sql);

	$dist_memtwitt = "";
	$db_handle = mysql_connect($server, $user_name, $pass_word);
	$db_found = mysql_select_db($database, $db_handle);
	if ($db_found) {
	        $SQL = "SELECT L1, memtwitt FROM login WHERE GUID='".$array[GUID]."'";
	        $result = mysql_query($SQL);
	        list($uname, $dist_memtwitt) = mysql_fetch_row($result);
	        mysql_close($db_handle);
	}
        if ($dist_memtwitt == "") {
               $dist_memtwitt = $uname;
        }

	#Tweet distance winner
	$twmsg = "Daily Distance Winner: {$array[PETNAME]} running a distance of {$maxdist}m. Congratulations, {$dist_memtwitt}";
	try {
		$tweet = $twitter->send($twmsg); // you can add $imagePath as second a$
	} catch (TwitterException $e) {
                echo 'Error: ' . $e->getMessage();
        }
        
	$dur = $date->modify('-2 day');
        $dur = date_format($dur, 'Y-m-d');
        $sql = "select * from new com.ibm.db2j.GaianQuery('delete from winners where day = ''".$dur."'' ','with_provenance') GQ";
        $res = $drv->exec($sql);

}

?>

<html>
<head>
<title>Winners</title>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body>

<img src="wrr.gif" alt="world rodent racing">
<ul>
<li><a href="../index.html">Home</a></li>
<li id=selected>Winners</li>
<li><a href="mapcluster.php">Maps</a></li>
<li><a href="graph/graphs.php">Graphs</a></li>
<li><a href="securemenu.php">My Pets</a></p></li>
<li><a href="about.html">About</a></li>
</ul>
<br>
<iframe src="winnersboard.php" height=420 width=850 seamless scrolling=no frameborder=0></iframe>
<br>

<FORM NAME ="form1" METHOD ="POST" ACTION ="winners.php">
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Calculate Latest Winners" id=submit>
</form>

</body>
</html>
