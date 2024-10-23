<?php
require_once(__DIR__ . "/../vendor/autoload.php");

use PhpAmqpLib\Connection\AMQPStreamConnection;

function rabbitMQConnection() {
    $host = '127.0.0.1';  // Your RabbitMQ host (replace with actual IP if needed)
    $port = 5672;
    $user = 'mdl35';
    $pass = 'mdl35it490';

    try {
        $connection = new AMQPStreamConnection($host, $port, $user, $pass);
        echo "Connected to RabbitMQ successfully.".PHP_EOL;
        return $connection;
    } catch (Exception $e) {
        echo "Error connecting to RabbitMQ: ".$e->getMessage().PHP_EOL;
        exit(1);
    }
}
?>