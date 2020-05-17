<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('aatw_stop', false, false, false, false);
$startmsg = "Stop";
$msg = new AMQPMessage($startmsg);
$channel->basic_publish($msg, '', 'aatw_stop');
//echo " [x] Sent 'Hello World!'\n";
$channel->close();
$connection->close();
?>
<html>
<head>
<title>System Control</title>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>

<body>
<img src="wrr.gif" alt="world rodent racing">
<p>
<h2>Successfully sent stop message</h2>
<p>
<ul><li><a href='control.php'>OK</a></li></ul>
</body>
</html>
