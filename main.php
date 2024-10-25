<?php
session_start();

// Check if the user is authenticated (you would implement this after a successful login)
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirect to login if not authenticated
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Protected Page</title>
</head>
<body>
    <h1>Welcome!</h1>
    <p>You are logged in.</p>
    <a href="logout.php">Logout</a>
</body>
</html>