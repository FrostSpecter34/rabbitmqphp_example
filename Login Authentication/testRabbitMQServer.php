<?php
// Include the RabbitMQ library
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Connect to RabbitMQ
$connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test', 'testHost');
$channel = $connection->channel();
$channel->queue_declare('testQueue', false, true, false, false);

    $callback = function ($msg) {
        $data = json_decode($msg->body, true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

    // Validate the credentials
    $isValid = ($username === 'yourusername' && $password === 'password'); // Example validation

        // Prepare the response
        $response = [
            'success' => $isValid,
            'message' => $isValid ? 'Login successful.' : 'Invalid username or password.',
        ];
    // Validate the credentials
    $isValid = password_verify($password, $storedPassword);

    $response = [
        'success' => $isValid,
        'message' => $isValid ? 'Login successful.' : 'Invalid username or password.',
    ];
        // Ensure the reply_to property is set
        if (!empty($msg->get('reply_to'))) {
            // Send the response back
            $responseMsg = new AMQPMessage(json_encode($response), ['correlation_id' => $msg->get('correlation_id')]);
            $msg->delivery_info['channel']->basic_publish($responseMsg, '', $msg->get('reply_to'));
            $msg->ack();
        } else {
            // Log or handle the missing reply_to property
            error_log('Missing reply_to property in the received message.');
        }
    };

    $channel->basic_consume('testQueue', '', false, false, false, false, $callback);

    // Wait for messages
    while ($channel->is_consuming()) {
        $channel->wait();
    }

    // Close the channel and connection
    $channel->close();
    $connection->close();
?>
