<?php
session_start();
//require_once 'testRabbitMQServer.php';

// Fetch subscriptions from the database via RabbitMQ
//$subscriptions = fetchSubscriptions();
//$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';
//usort($subscriptions, function($a, $b) use ($sort_by) {
//    return strcmp($a[$sort_by], $b[$sort_by]);
//});
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="css/Homepage.css" />
</head>
<body style="margin: 0; background: #ffffff">
    <div class="container-center-horizontal">
        <div class="frame-3 screen">
            <div class="flex-col roboto-normal-black-64px">
                <h1 class="sudo-squad-sub-manager">SUDO SQUAD SUB MANAGER</h1>
                <div class="my-account">My Account</div>
                <div class="active-subscriptions">Subscriptions</div>

                <form method="GET" action="">
                    <label for="sort_by">Sort by:</label>
                    <select name="sort_by" id="sort_by" onchange="this.form.submit()">
                        <option value="name" <?php if ($sort_by == 'name') echo 'selected'; ?>>Name</option>
                        <option value="price" <?php if ($sort_by == 'price') echo 'selected'; ?>>Price</option>
                    </select>
                </form>

                <div class="subscriptions-list">
                    <?php foreach ($subscriptions as $subscription): ?>
                        <div class="subscription-item">
                            <div><?php echo $subscription['name']; ?> (<?php echo $subscription['status']; ?>)</div>
                            <div class="subscription-details">
                                <select>
                                    <option>Price: <?php echo $subscription['price']; ?></option>
                                    <option>Renewal Date: <?php echo $subscription['renewal_date']; ?></option>
                                    <option>Cancellation Date: <?php echo $subscription['cancellation_date']; ?></option>
                                </select>
                            </div>
                            <a href="cancel_subscription.php?id=<?php echo $subscription['id']; ?>">Cancel Subscription</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="addsub.php">Add subscription</a>
            <a href="notifications.php">Notifications</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>