<!--Not being used at the moment-->
<?php
require_once 'vendor/autoload.php'; // Include the RabbitMQ library (php-amqplib)

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = $data['password'];

// RabbitMQ connection
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Declare a queue for authentication
$channel->queue_declare('auth_queue', false, true, false, false, false, []);

// Create the message
$messageData = json_encode(['username' => $username, 'password' => $password]);
$msg = new AMQPMessage($messageData);
$channel->basic_publish($msg, '', 'auth_queue');

// Consume the response
$response = null;

// Callback function to process responses
$callback = function($msg) use (&$response) {
    $response = json_decode($msg->body, true);
};

// Set up consumer to listen for the response
$channel->basic_consume('auth_response_queue', '', false, true, false, false, $callback);

// Wait for a response for 5 seconds
$startTime = time();
while (!$response && (time() - $startTime) < 5) {
    $channel->wait();
}

// Close the connection
$channel->close();
$connection->close();

// Return the response as JSON
if ($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No response from server.']);
}
?>