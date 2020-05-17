<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('aatw_pyth', false, false, false, false);
echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
$callback = function($msg) {
echo " [x] Received ", $msg->body, "\n";
};
$channel->basic_consume('aatw_pyth', '', false, true, false, false, $callback);
while(count($channel->callbacks)) {
$channel->wait();
}
$channel->close();
$connection->close();
?>
