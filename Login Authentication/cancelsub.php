<?php
session_start();
require_once 'testRabbitMQClient.php'; // Include your RabbitMQ client setup

// Check if the subscription ID is provided
if (isset($_GET['id'])) {
    $subscriptionId = $_GET['id'];

    // Prepare the message to send to the DMZ server
    $message = [
        'action' => 'cancel_subscription',
        'subscription_id' => $subscriptionId
    ];

    // Send the message to RabbitMQ and wait for the response from the DMZ server
    $response = sendRequestToRabbitMQ($message);

    // Check the response from the DMZ server
    if ($response['status'] == 'success') {
        $_SESSION['message'] = 'Subscription cancelled successfully.';
    } else {
        $_SESSION['message'] = 'Error: ' . $response['message'];
    }
}

// Redirect back to the main page
header('Location: main.php');
exit();
?>