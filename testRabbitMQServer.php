<?php
require_once 'vendor/autoload.php'; // Include the RabbitMQ library (php-amqplib)

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// RabbitMQ connection
$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'test', 'test', 'testHost');
$channel = $connection->channel();

// Declare the authentication request queue
$channel->queue_declare('auth_queue', false, true, false, false, false, []);

// Declare the response queue
$channel->queue_declare('auth_response_queue', false, true, false, false, false, []);

// Callback function to handle incoming messages
$callback = function($msg) use ($channel) {
    $data = json_decode($msg->body, true);
    $username = $data['username'];
    $password = $data['password'];

    // For demonstration, we'll use a hardcoded user
    $storedHashedPassword = password_hash('password', PASSWORD_BCRYPT); // Hashed password for "password"

    // Simple authentication check
    if ($username === 'yourusername' && password_verify($password, $storedHashedPassword)) {
        // Authentication successful
        $response = ['success' => true, 'message' => 'Login successful.'];
    } else {
        // Authentication failed
        $response = ['success' => false, 'message' => 'Invalid username or password.'];
    }

    // Send response back to the response queue
    $responseMsg = new AMQPMessage(json_encode($response));
    $channel->basic_publish($responseMsg, '', 'auth_response_queue');
};

// Start consuming messages
$channel->basic_consume('auth_queue', '', false, true, false, false, $callback);

// Wait for messages
while ($channel->is_consuming()) {
    $channel->wait();
}

// Close the connection (will not reach here in normal use)
$channel->close();
$connection->close();
?>
