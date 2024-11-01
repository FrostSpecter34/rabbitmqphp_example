<?php
// Include the RabbitMQ library
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('172.25.182.53', 5672, 'adam', 'adam', 'DMZ_MAIN');
$channel = $connection->channel();
$queueName = 'TestQueue';$channel->queue_declare($queueName, false, true, false, false);

$callback = function($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    requestProcessor($msg->body);
    $msg->ack();
};
$channel->basic_qos(null, 1, null);
$channel->basic_consume($queueName, '', false, false, false, false, $callback);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

while($channel->is_consuming()) {
    $channel->wait();
}
$channel->close();
$connection->close();
?>