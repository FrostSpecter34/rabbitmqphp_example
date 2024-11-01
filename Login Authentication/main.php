<?php
session_start();
//require_once 'testRabbitMQServer.php';

// Fetch subscriptions from the database via RabbitMQ
//$subscriptions = fetchSubscriptions();
//$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';
//usort($subscriptions, function($a, $b) use ($sort_by) {
//    return strcmp($a[$sort_by], $b[$sort_by]);
//});
// Initialize subscriptions array if it doesn't exist
if (!isset($_SESSION['subscriptions'])) {
  $_SESSION['subscriptions'] = [];
}

// Check for success message to display
$message = '';
if (isset($_SESSION['message'])) {
  $message = $_SESSION['message'];
  unset($_SESSION['message']);
}

// Sorting logic (if needed)
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';

// Sort subscriptions based on the selected criteria
usort($_SESSION['subscriptions'], function($a, $b) use ($sort_by) {
  switch ($sort_by) {
      case 'website':
          return strcmp($a['website'], $b['website']);
      case 'card_type':
          // Sort by card type (non-blank first, then alphabetical)
          if (empty($a['card_type']) && !empty($b['card_type'])) {
              return 1; // a is blank, b is not
          } elseif (!empty($a['card_type']) && empty($b['card_type'])) {
              return -1; // a is not blank, b is
          } else {
              return strcmp($a['card_type'], $b['card_type']); // alphabetical order
          }
      case 'card_number':
          // Sort by card number (non-blank first, then numerical order)
          if (empty($a['card_number']) && !empty($b['card_number'])) {
              return 1; // a is blank, b is not
          } elseif (!empty($a['card_number']) && empty($b['card_number'])) {
              return -1; // a is not blank, b is
          } else {
              return strcmp($a['card_number'], $b['card_number']); // numerical order
          }
      case 'paypal':
          // Sort by PayPal (yes first, no last)
          if ($a['paypal'] === 'yes' && $b['paypal'] !== 'yes') {
              return -1; // a is yes, b is not
          } elseif ($a['paypal'] !== 'yes' && $b['paypal'] === 'yes') {
              return 1; // a is not yes, b is
          } else {
              return 0; // both are the same
          }
  }
  return 0; // Fallback in case no criteria match
});
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
        <h1 class="sudo-squad-sub-manager">SUDO SQUAD SUB MANAGER</h1>
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
            <?php if (!empty($_SESSION['subscriptions'])): ?>
                <?php foreach ($_SESSION['subscriptions'] as $subscription): ?>
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