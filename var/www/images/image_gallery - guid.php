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

// This function makes usage of
// $_GET, $_POST, etc... variables
// completly safe in SQL queries
function sql_safe($s)
{
    if (get_magic_quotes_gpc())
        $s = stripslashes($s);

    return mysql_real_escape_string($s);
}

// If user pressed submit in one of the forms
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // cleaning title field
    $petname = trim(sql_safe($_POST['petname']));

    if ($petname == '') // if title is not set
        $petname = "(not set)";// use (empty title) string


        if (isset($_FILES['photo']))
        {
            @list(, , $imtype, ) = getimagesize($_FILES['photo']['tmp_name']);
            // Get image type.
            // We use @ to omit errors

            if ($imtype == 3) // cheking image type
                $ext="png";   // to use it later in HTTP headers
            elseif ($imtype == 2)
                $ext="jpeg";
            elseif ($imtype == 1)
                $ext="gif";
            else
                $msg = 'Error: unknown file format';

            if (!isset($msg)) // If there was no error
            {
                $data = file_get_contents($_FILES['photo']['tmp_name']);
                $data = mysql_real_escape_string($data);
                // Preparing data to be used in MySQL query

                mysql_query("INSERT INTO {$table}
                                SET GUID='$guid', petname='$petname', ext='$ext',
                                    data='$data'");

                $msg = 'Success: image uploaded';
            }
        }

        if (isset($_POST['del'])) // If used selected some photo to delete
        {                         // in 'uploaded images form';
            $id = intval($_POST['del']);
            mysql_query("DELETE FROM {$table} WHERE id=$id");
            $msg = 'Photo deleted';
        }
}
elseif (isset($_GET['show']))
{
    $id = intval($_GET['show']);

    $result = mysql_query("SELECT ext, UNIX_TIMESTAMP(image_time), data
                             FROM {$table}
                            WHERE id=$id LIMIT 1");

    if (mysql_num_rows($result) == 0)
        die('no image');

    list($ext, $image_time, $data) = mysql_fetch_row($result);

    $send_304 = false;
    if (php_sapi_name() == 'apache') {
        // if our web server is apache
        // we get check HTTP
        // If-Modified-Since header
        // and do not send image
        // if there is a cached version

        $ar = apache_request_headers();
        if (isset($ar['If-Modified-Since']) && // If-Modified-Since should exists
            ($ar['If-Modified-Since'] != '') && // not empty
            (strtotime($ar['If-Modified-Since']) >= $image_time)) // and grater than
            $send_304 = true;                                     // image_time
    }


    if ($send_304)
    {
        // Sending 304 response to browser
        // "Browser, your cached version of image is OK
        // we're not sending anything new to you"
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $ts).' GMT', true, 304);

        exit(); // bye-bye
    }

    // outputing Last-Modified header
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', $image_time).' GMT',
            true, 200);

    // Set expiration time +1 year
    // We do not have any photo re-uploading
    // so, browser may cache this photo for quite a long time
    header('Expires: '.gmdate('D, d M Y H:i:s',  $image_time + 86400*365).' GMT',
            true, 200);

    // outputing HTTP headers
    header('Content-Length: '.strlen($data));
    header("Content-type: image/{$ext}");

    // outputing image
    echo $data;
    exit();
}
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

<?php
if (isset($msg)) // this is special section for
                 // outputing message
{
?>
<p style="font-weight: bold;"><?=$msg?>
<br>
<a href="<?=$PHP_SELF?>">reload page</a>
</p>
<?php
}
?>
<h2>Pictures:</h2>
<form action="<?=$PHP_SELF?>" method="post">
<!-- This form is used for image deletion -->

<?php
$result = mysql_query("SELECT id, image_time, petname FROM {$table} WHERE GUID='{$guid}' ORDER BY id DESC");
if (mysql_num_rows($result) == 0) // table is empty
    echo '<ul><li>No images loaded</li></ul>';
else
{
    echo '<ul>';
    while(list($id, $image_time, $petname) = mysql_fetch_row($result))
    {
        // outputing list
        echo "<li><input type='radio' name='del' value='{$id}'>";
        echo "<a href='{$PHP_SELF}?show={$id}'>{$petname}</a> &ndash; ";
        echo "<small>{$image_time}</small><li>";
    }

    echo '</ul>';
    <br><br>';

    echo '<input type="submit" value="Delete selected">';
}
?>

</form>
<h2>Upload new image:</h2>
<form action="<?=$PHP_SELF?>" method="POST" enctype="multipart/form-data">
<label for="petname">Pet name:</label><br>
<input type="text" name="petname" id="petname" size="64"><br><br>

<label for="photo">Photo:</label><br>
<input type="file" name="photo" id="photo"><br><br>

<input type="submit" value="upload">
</form>

</body>
</html>
