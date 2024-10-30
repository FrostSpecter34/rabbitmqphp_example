<?php
require_once(__DIR__ . "/db_connect.php");

// Registers a new user
function registerUser($username, $email, $password) {
    $connection = dbConnection();

    $stmt = $connection->prepare('INSERT INTO Users (username, email, password) VALUES (?, ?, ?)');
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Hash the password

    if ($stmt) {
        $stmt->bind_param('sss', $username, $email, $hashedPassword);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "User successfully registered: $username\n";
        } else {
            echo "Error: Unable to register user\n";
        }

        $stmt->close();
    } else {
        echo "Error preparing SQL statement: " . $connection->error . PHP_EOL;
    }

    $connection->close();
}

//Fetch stored User
function getUserCredentials($username) {
    $connection = dbConnection();
    $stmt = $connection->prepare('SELECT password FROM Users WHERE username = ?');

    if ($stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($storedPassword);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Error preparing SQL statement: " . $connection->error . PHP_EOL;
        return null;
    }

    $connection->close();
    return $storedPassword;
}


// Adds to the Services table
function addService($userId, $serviceDetails) {
    $connection = dbConnection();

    $stmt = $connection->prepare('INSERT INTO Services (user_id, plan, start_date) VALUES (?, ?, ?)');
    
    if ($stmt) {
        $stmt->bind_param('iss', $userId, $serviceDetails['plan'], $serviceDetails['start_date']);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Service added for user: $userId\n";
        } else {
            echo "Error: Unable to add service\n";
        }

        $stmt->close();
    } else {
        echo "Error preparing SQL statement: " . $connection->error . PHP_EOL;
    }

    $connection->close();
}

// Updates payment information
function updatePayment($userId, $paymentInfo) {
    $connection = dbConnection();

    $stmt = $connection->prepare('UPDATE Users SET payment_info = ? WHERE id = ?');

    if ($stmt) {
        $paymentInfoJson = json_encode($paymentInfo); // Convert payment info to JSON
        $stmt->bind_param('si', $paymentInfoJson, $userId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Payment info updated for user: $userId\n";
        } else {
            echo "Error: Unable to update payment info\n";
        }

        $stmt->close();
    } else {
        echo "Error preparing SQL statement: " . $connection->error . PHP_EOL;
    }

    $connection->close();
}
?>