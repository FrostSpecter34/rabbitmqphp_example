<?php
session_start();

// Check if the subscription ID is provided
if (isset($_GET['id'])) {
    $subscriptionId = $_GET['id'];

    // Check if subscriptions exist in the session
    if (isset($_SESSION['subscriptions'])) {
        // Loop through the subscriptions to find the one to remove
        foreach ($_SESSION['subscriptions'] as $index => $subscription) {
            if ($subscription['id'] == $subscriptionId) {
                // Remove the subscription from the session
                unset($_SESSION['subscriptions'][$index]);

                // Re-index the array
                $_SESSION['subscriptions'] = array_values($_SESSION['subscriptions']);

                // Set a success message
                $_SESSION['message'] = 'Subscription cancelled successfully.';
                break;
            }
        }
    }
}

// Redirect back to the main page
header('Location: main.php');
exit();
?>
