<!--Takes the place of index.html in /var/www/sample-->
<?php
// Initialize message variable
$message = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted username and password
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Send login data to the PHP client
    $ch = curl_init('htttp://www.sample.com/testRabbitMQClient.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username' => $username, 'password' => $password]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    // Execute the request
    $response = curl_exec($ch);
    curl_close($ch);

    // Handle the response
    if ($response) {
        $data = json_decode($response, true);
        $message = $data['message']; // Display message
        if ($data['success']) {
            // Redirect to a protected page if login is successful
            header('Location: main.php'); // Change this to your actual protected page
            exit;
        }
    } else {
        $message = 'Error communicating with RabbitMQ client.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Login</button>
    </form>

    <div id="message"><?php echo htmlspecialchars($message); ?></div>
</body>
</html>