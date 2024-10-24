<?php
// Include the RabbitMQ library
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Get the posted data
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// Prepare the message
$messageBody = json_encode(['username' => $username, 'password' => $password]);

// Connect to RabbitMQ
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('login_queue', false, false, false, false);

// Send the message
$msg = new AMQPMessage($messageBody);
$channel->basic_publish($msg, '', 'login_queue');

// Wait for the response
$callbackQueue = $channel->queue_declare("", false, false, true, false)[0];
$response = null;
$channel->basic_consume($callbackQueue, '', false, true, false, false, function ($msg) use (&$response) {
    $response = json_decode($msg->body, true);
});

// Wait until response is received
while (!$response) {
    $channel->wait();
}

// Close the channel and connection
$channel->close();
$connection->close();

// Send the response back to the client
header('Content-Type: application/json');
echo json_encode($response);
?>
