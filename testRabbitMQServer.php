<?php
// Include necessary libraries and database functions
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/db_functions.php'; // Ensure your database functions are included

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Connect to RabbitMQ
$connection = new AMQPStreamConnection('172.25.205.131', 5672, 'test', 'test', 'DMZ_MAIN');
$channel = $connection->channel();
$channel->queue_declare('testQueue', false, true, false, false);

// Define a callback for processing incoming messages
$callback = function ($msg) {
    $data = json_decode($msg->body, true);
    $requestType = $data['request_type'] ?? ''; // Determine the type of request

    $response = [];

    switch ($requestType) {
        case 'login':
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            // Fetch the stored password for validation
            $storedPassword = userLogin($username); // Assuming userLogin retrieves the hashed password

            // Validate the credentials
            if ($storedPassword !== null && password_verify($password, $storedPassword)) {
                $response = [
                    'success' => true,
                    'message' => 'Login successful.'
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Invalid username or password.'
                ];
            }
            break;

        case 'register':
            $username = $data['username'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            registerUser($username, $email, $password); // Call the register function
            $response = ['success' => true, 'message' => 'User registered successfully.'];
            break;

        case 'add_subscription':
            $subscription = $data['subscription'] ?? [];
            $userId = $subscription['user_id'] ?? ''; // Assuming user ID is included in the subscription data

            addService($userId, $subscription); // Call the function to add the subscription
            $response = ['success' => true, 'message' => 'Subscription added successfully.'];
            break;

        case 'cancel_subscription':
            $subscriptionId = $data['id'] ?? '';
            // Call the cancelService function to handle cancellation
            $responseMsg = cancelService($subscriptionId);
            $response = ['success' => true, 'message' => $responseMsg];
            break;

        case 'fetch_subscriptions':
            $userId = $data['user_id'] ?? ''; // Assuming user ID is sent for fetching subscriptions
            $subscriptions = getUserSubscriptions($userId); // Retrieve subscriptions for the user
            $response = ['success' => true, 'subscriptions' => $subscriptions];
            break;

        default:
            $response = ['success' => false, 'message' => 'Invalid request type.'];
            break;
    }

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

// Start consuming messages from the testQueue
$channel->basic_consume('testQueue', '', false, false, false, false, $callback);

// Wait for messages
while ($channel->is_consuming()) {
    $channel->wait();
}

// Close the channel and connection
$channel->close();
$connection->close();
?>