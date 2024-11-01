<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Connect to RabbitMQ
$connection = new AMQPStreamConnection('172.25.205.131', 5672, 'test', 'test', 'DMZ_MAIN');
$channel = $connection->channel();
$channel->queue_declare('testQueue', false, true, false, false);

// Declare a callback queue
list($callbackQueue, ,) = $channel->queue_declare("", false, false, true, false);

$response = null;
$corr_id = uniqid();

// Prepare the message based on the request
$data = json_decode(file_get_contents('php://input'), true);
$requestType = $data['request_type'] ?? ''; // 'login', 'add_subscription', 'cancel_subscription', 'fetch_subscriptions'

switch ($requestType) {
    case 'login':
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        // Prepare the message for login
        $messageBody = json_encode(['request_type' => 'login', 'username' => $username, 'password' => $password]);
        break;

    case 'add_subscription':
        // Prepare the message for adding a subscription
        $subscription = [
            'website' => $data['website'] ?? '',
            'card_type' => $data['card_type'] ?? '',
            'card_number' => $data['card_number'] ?? '',
            'paypal' => $data['paypal'] ?? '',
            'price' => $data['price'] ?? '',
            'renewal_date' => $data['renewal_date'] ?? '',
            'cancellation_date' => $data['cancellation_date'] ?? ''
        ];
        $messageBody = json_encode(['request_type' => 'add_subscription', 'subscription' => $subscription]);
        break;

    case 'cancel_subscription':
        $subscriptionId = $data['id'] ?? '';
        // Prepare the message for canceling a subscription
        $messageBody = json_encode(['request_type' => 'cancel_subscription', 'id' => $subscriptionId]);
        break;

    case 'fetch_subscriptions':
        // Prepare the message for fetching subscriptions
        $messageBody = json_encode(['request_type' => 'fetch_subscriptions']);
        break;

    default:
        // Handle invalid request
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid request type.']);
        exit;
}

// Handle the response
$channel->basic_consume($callbackQueue, '', false, true, false, false, function ($msg) use (&$response, $corr_id) {
    if ($msg->get('correlation_id') == $corr_id) {
        $response = $msg->body;
    }
});

// Send the message
$msg = new AMQPMessage($messageBody, ['correlation_id' => $corr_id, 'reply_to' => $callbackQueue]);
$channel->basic_publish($msg, '', 'testQueue');

// Wait for the response
while (!$response) {
    $channel->wait();
}

// Close the channel and connection
$channel->close();
$connection->close();

if (!$response) {
    error_log('No response from RabbitMQ server.');
}

// Send the response back to the client
header('Content-Type: application/json');
echo $response;

?>