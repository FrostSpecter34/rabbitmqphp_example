<?php
require_once(__DIR__ . "/../vendor/autoload.php");

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function rabbitMQConnection() {
    $host = '127.0.0.1';
    $port = 5672;
    $user = 'mdl35';
    $pass = 'mdl35it490';
    $vhost = 'testHost';

    try {
        $connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
        echo "Connected to RabbitMQ successfully.".PHP_EOL;
        return $connection;
    } catch (Exception $e) {
        echo "Error connecting to RabbitMQ: ".$e->getMessage().PHP_EOL;
        exit(1);
    }
}

try {
    $connection = new AMQPStreamConnection('localhost', 5672, 'mdl35', 'mdl35it490', 'testHost');  // Change credentials if needed
    echo "Connection to RabbitMQ established successfully!" . PHP_EOL;
} catch (Exception $e) {
    echo "Error connecting to RabbitMQ: " . $e->getMessage() . PHP_EOL;
}
?>