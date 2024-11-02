<?php
session_start();
require_once 'testRabbitMQClient.php'; // Include your RabbitMQ client setup

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $subscription = [
        'website' => $_POST['website'],
        'card_type' => $_POST['card_type'],
        'card_number' => $_POST['card_number'],
        'paypal' => $_POST['paypal'],
        'price' => $_POST['price'],  
        'renewal_date' => $_POST['renewal_date'],  
        'cancellation_date' => $_POST['cancellation_date']  
    ];

    // Prepare the message to send to the DMZ server
    $message = [
        'action' => 'add_subscription',
        'subscription' => $subscription
    ];

    // Send the message to RabbitMQ and wait for the response from the DMZ server
    $response = sendRequestToRabbitMQ($message);

    // Check the response from the DMZ server
    if ($response['status'] == 'success') {
        $_SESSION['message'] = 'Subscription added successfully!';
    } else {
        $_SESSION['message'] = 'Error: ' . $response['message'];
    }

    // Redirect to main page
    header('Location: main.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" type="image/png" href="https://animaproject.s3.amazonaws.com/home/favicon.png" />
    <meta name="og:type" content="website" />
    <meta name="twitter:card" content="photo" />
    <link rel="stylesheet" type="text/css" href="css/homepage.css" />
</head>
<body>
<form action="addsub.php" method="POST">
    <div class="container-center-horizontal">
        <div class="frame-3">
            <h1 class="title">Add Subscriptions</h1>

            <div class="website">Website</div>
            <input name="website" type="text" class="website-input" placeholder="Type here" required />

            <div class="card">Card Type</div>
            <input name="card_type" type="text" class="card type-input" placeholder="Type here" required />

            <div class="card">Card Number</div>
            <input name="card_number" type="text" class="card number-input" placeholder="Type here" required />

            <div class="website">Price</div>
            <input name="price" type="text" class="price-input" placeholder="Enter price" required />

            <div class="website">Renewal Date</div>
            <input name="renewal_date" type="date" class="renewal-date-input" required />

            <div class="website">Cancellation Date</div>
            <input name="cancellation_date" type="date" class="cancellation-date-input" />

            <div class="website">or</div>

            <div class="website">PayPal</div>
            <input name="paypal" type="text" class="paypal-input" placeholder="Type here" />

            <br> 
            <button type="submit">Add Subscription</button>
            <a href="main.php" class="return-link">Return to account</a>
        </div>
    </div>
</form>
</body>
</html>