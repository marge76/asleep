<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$guid = $_GET['guid'];
$petname = $_GET['petname'];

$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('aatw_pic', false, false, false, false);
$startmsg = $guid.",".$petname;
$msg = new AMQPMessage($startmsg);
$channel->basic_publish($msg, '', 'aatw_pic');
//echo " [x] Sent 'Hello World!'\n";
$channel->close();
$connection->close();
?>
<html>
<head>
<title>My Pictures</title>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>

<body>
<img src="wrr.gif" alt="world rodent racing">
<p>
<h2>Successfully taken photo</h2>
<p>
<ul><li><a href='images/image_gallery.php'>OK</a></li></ul>
</body>
</html>
