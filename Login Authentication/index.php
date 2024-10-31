<?php
session_start(); // Start the session

// Initialize message variable
$message = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted username and password
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Ensure username and password are not empty
    if (!empty($username) && !empty($password)) {
        // Send login data to the PHP client
        $ch = curl_init('http://www.sample.com/testRabbitMQClient.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username' => $username, 'password' => $password]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        // Execute the request
        $response = curl_exec($ch);
        // After curl_exec in index.php
        if ($response === false) {
            error_log('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);

        // Handle the response
        if ($response) {
            $data = json_decode($response, true);
            $message = $data['message'] ?? 'Invalid response from server.'; // Display message

            if (!empty($data['success']) && $data['success'] === true) {
                // Set session variables
                $_SESSION['username'] = $username;
                $_SESSION['loggedin'] = true;

                // Redirect to a protected page if login is successful
                header('Location: http://www.sample.com/main.php'); 
                exit;
            }
        } else {
            $message = 'Error communicating with RabbitMQ client.';
        }
    } else {
        $message = 'Username and password cannot be empty.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="/css/homepage.css" />
</head>
<body>
    <div class="container-center-horizontal">
        <div class="frame-3 screen">
            <header>
                <h1 class="sudo-squad-sub-manager">Login</h1>
            </header>
            <main>
                <form method="POST" action="">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    <br><br>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <br><br>
                    <button type="submit">Login</button>
                </form>

                <div id="message"><?php echo htmlspecialchars($message); ?></div>
            </main>
        </div>
    </div>
</body>
</html>