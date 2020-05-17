<?php

session_start();
if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
        header ("Location: /login/login3.php");
}
$guid = $_SESSION['guid'];
$db_host = 'localhost';
$db_user = 'root'; 
$db_pwd = 'SQLPw0rd';

$database = 'aatw';
$table = 'aatw_gallery';

if (!mysql_connect($db_host, $db_user, $db_pwd))
    die("Can't connect to database");

if (!mysql_select_db($database))
    die("Can't select database");
?>

<html><head>
<title>My Pictures</title>
<link rel="shortcut icon" href="../favicon.ico">
<link rel="stylesheet" type="text/css" href="../stylesheet.css">
</head>

<body>
<img src="../wrr.gif" alt="world rodent racing">
<ul>
<li id=selected>My Pictures</li>
<li><a href="../securemenu.php">Back</a></li>
</ul>
<p>

<h2>Pictures:</h2>

<?php
echo $guid;
$result = mysql_query("SELECT id, image_time, petname FROM {$table} WHERE GUID='{$guid}' ORDER BY id DESC");
if (mysql_num_rows($result) == 0) // table is empty
    echo '<ul><li>No images loaded</li></ul>';
else 
    echo '<ul><li>found</ul></li>';
?>

</form>
</body>
</html>
