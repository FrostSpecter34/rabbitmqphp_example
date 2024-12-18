<?php
session_start();
require_once 'testRabbitMQClient.php'; // Include your RabbitMQ client setup

// Check for success message to display
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Define the sort criteria based on GET parameters
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'website';
$order_by = 'website'; // Default sort column
$order_criteria = 'ASC';

switch ($sort_by) {
    case 'website':
        $order_by = 'website';
        $order_criteria = 'ASC';
        break;
    case 'card_type':
        $order_by = 'card_type';
        $order_criteria = 'ASC';
        break;
    case 'card_number':
        $order_by = 'card_number';
        $order_criteria = 'ASC';
        break;
    case 'paypal':
        $order_by = 'paypal DESC';
        break;
}

// Prepare the message to send to the DMZ server
$message = [
    'action' => 'fetch_subscriptions',
    'sort_by' => $sort_by,
    'order' => $order_by . ' ' . $order_criteria
];

// Send the message to RabbitMQ and wait for the response from the DMZ server
$response = sendRequestToRabbitMQ($message);

// Check if the response contains subscriptions
$subscriptions = $response['subscriptions'] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="/css/homepage.css" />
 <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <title>Subscription Manager</title>
</head>
<body>
    <h1>Subscription Manager</h1>

    <div class="sort-menu">
        <form method="GET" action="">
            <label for="sort_by">Sort by:</label>
            <select name="sort_by" id="sort_by" onchange="this.form.submit()">
                <option value="website" <?php if ($sort_by == 'website') echo 'selected'; ?>>Website</option>
                <option value="card_type" <?php if ($sort_by == 'card_type') echo 'selected'; ?>>Card Type</option>
                <option value="card_number" <?php if ($sort_by == 'card_number') echo 'selected'; ?>>Card Number</option>
                <option value="paypal" <?php if ($sort_by == 'paypal') echo 'selected'; ?>>PayPal</option>
            </select>
        </form>
    </div>

	<a href="addsub.php">Add Subscription</a></li>
    <a href="logout.php">Logout</a></li>

    <div class="active-subscriptions">Active Subscriptions</div>
    <div class="subscription-list">
        <?php if (!empty($subscriptions)): ?>
            <?php foreach ($subscriptions as $subscription): ?>
                <div class="subscription-item">
                    <span><?php echo htmlspecialchars($subscription['website']); ?> - <?php echo htmlspecialchars($subscription['card_type']); ?> - <?php echo htmlspecialchars($subscription['card_number']); ?> - <?php echo htmlspecialchars($subscription['paypal']); ?></span>
                    <a href="cancelsub.php?id=<?php echo htmlspecialchars($subscription['id']); ?>" class="delete-button">Cancel Subscription</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No subscriptions found.</p>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById("sort_by").addEventListener("change", function() {
            const selectedValue = this.value;
            subscriptions.sort((a, b) => {
                if (selectedValue === "website") {
                    return a.website.localeCompare(b.website);
                } else if (selectedValue === "card_type") {
                    return a.card_type.localeCompare(b.card_type);
                } else if (selectedValue === "card_number") {
                    return a.card_number.localeCompare(b.card_number);
                } else if (selectedValue === "paypal") {
                    return a.paypal.localeCompare(b.paypal);
                }
            });
            displaySubscriptions();
        });

        function displaySubscriptions() {
            const subscriptionList = document.querySelector(".subscription-list");
            subscriptionList.innerHTML = "";

            subscriptions.forEach((subscription, index) => {
                const item = document.createElement("div");
                item.className = "subscription-item";

                const text = document.createElement("span");
                text.textContent = `${subscription.website} - ${subscription.card_type} - ${subscription.card_number} - ${subscription.paypal}`;

                const deleteButton = document.createElement("a");
                deleteButton.className = "delete-button";
                deleteButton.textContent = "Cancel Subscription";
                deleteButton.href = `cancelsub.php?id=${subscription.id}`;

                item.appendChild(text);
                item.appendChild(deleteButton);
                subscriptionList.appendChild(item);
            });
        }

        displaySubscriptions();
    </script>
	   <script src="/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
