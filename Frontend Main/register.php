<?php
session_start(); // Start the session

// Initialize message variable
$message = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted username and password
    $username = $_POST['username'] ?? '';
    $email = $_POST[email] ?? '';
    $password = $_POST['password'] ?? '';

    // Ensure username and password are not empty
    if (!empty($username) && !empty($password) && !empty($email)) {
        // Send login data to the PHP client
        $ch = curl_init('http://www.sample.com/testRabbitMQClient.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username' => $username, 'email' => $email, 'password' => $password]));
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
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;

                // Redirect to a protected page if login is successful
                header('Location: index.html'); 
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

<html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Project</title>
    <!-- Bootstrap CSS -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
     <link href="main2.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Register</h2>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>

          </form>
        <p class="mt-3">Already have an account? <a href="login.php">Login here</a>.</p>

    </div>
<!-- Bootstrap JS -->
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>


