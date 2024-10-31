<?php
// add_subscription.php
session_start();

// Initialize subscriptions array if it doesn't exist
if (!isset($_SESSION['subscriptions'])) {
    $_SESSION['subscriptions'] = [];
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $subscription = [
        'website' => $_POST['website'],
        'card_type' => $_POST['card_type'],
        'card_number' => $_POST['card_number'],
        'paypal' => $_POST['paypal']
    ];

    // Add subscription to session
    $_SESSION['subscriptions'][] = $subscription;

    // Redirect to main page with a success message
    $_SESSION['message'] = "Subscription added successfully!";
    header("Location: main.php");
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
  <body style="margin: 0; background: #ffffff">
    <input type="hidden" id="anPageName" name="page" value="frame-6" />
    <div class="container-center-horizontal">
      <div class="frame-6 screen">
        <h1 class="title roboto-normal-black-64px">Add Subscriptions</h1>

        <div class="website roboto-normal-black-64px">Website</div>
        <input name="website" type="text" class="website-input" placeholder="Type here" />

        <br><br>
        
        <div class="card roboto-normal-black-64px">Card Type</div>
        <input name="card_type" type="text" class="card-type-input" placeholder="Type here" />
        
        <div class="card roboto-normal-black-64px">Card Number</div>
        <input name="card_number" type="text" class="card-number-input" placeholder="Type here" />

        <div class="website roboto-normal-black-64px">or</div>

        <div class="website roboto-normal-black-64px">PayPal</div>
        <input name="paypal" type="text" class="paypal-input" placeholder="Type here" />

        <br> 
        
        <a href="main.php" class="return-link">Return to account</a>
      </div>
    </div>
  </body>
</html>