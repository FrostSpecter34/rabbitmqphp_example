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
</head>
<body>
    <header>
        <h1 class="sudo-squad-sub-manager">Subscription Manager</h1>
    </header>
    <nav>
        <ul>
            <li><a href="addsub.php">Add Subscription</a></li>
            <li><a href="notifications.php">Notifications</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <main>
        <div class="my-account">My Account</div>
        <div class="active-subscriptions">Subscriptions</div>
        
        <?php if ($message): ?>
            <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="GET" action="">
            <label for="sort_by">Sort by:</label>
            <select name="sort_by" id="sort_by" onchange="this.form.submit()">
                <option value="website" <?php if ($sort_by == 'website') echo 'selected'; ?>>Website</option>
                <option value="card_type" <?php if ($sort_by == 'card_type') echo 'selected'; ?>>Card Type</option>
                <option value="card_number" <?php if ($sort_by == 'card_number') echo 'selected'; ?>>Card Number</option>
                <option value="paypal" <?php if ($sort_by == 'paypal') echo 'selected'; ?>>PayPal</option>
            </select>
        </form>

        <div class="subscriptions-list">
            <?php if (!empty($subscriptions)): ?>
                <?php foreach ($subscriptions as $subscription): ?>
                    <div class="subscription-item">
                        <div><strong>Website:</strong> <?php echo htmlspecialchars($subscription['website']); ?></div>
                        <div><strong>Card Type:</strong> <?php echo htmlspecialchars($subscription['card_type']); ?></div>
                        <div><strong>Card Number:</strong> <?php echo htmlspecialchars($subscription['card_number']); ?></div>
                        <div><strong>PayPal:</strong> <?php echo htmlspecialchars($subscription['paypal']); ?></div>
                        <div class="subscription-details">
                            <select>
                                <option>Price: <?php echo isset($subscription['price']) ? htmlspecialchars($subscription['price']) : 'N/A'; ?></option>
                                <option>Renewal Date: <?php echo isset($subscription['renewal_date']) ? htmlspecialchars($subscription['renewal_date']) : 'N/A'; ?></option>
                                <option>Cancellation Date: <?php echo isset($subscription['cancellation_date']) ? htmlspecialchars($subscription['cancellation_date']) : 'N/A'; ?></option>
                            </select>
                        </div>
                        <a href="cancelsub.php?id=<?php echo htmlspecialchars($subscription['id']); ?>">Cancel Subscription</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No subscriptions found.</p>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 SUDO SQUAD</p>
    </footer>
</body>
</html>