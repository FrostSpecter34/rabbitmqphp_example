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
                header('Location: http://www.sample.com/homepage.php'); 
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
    <body>
    <!-- Login Form -->
    <div class="container mt-5">
        <h2 class="mb-4">Login</h2>
 <form action="" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p class="mt-3">Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
    <!-- Bootstrap JS -->
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
