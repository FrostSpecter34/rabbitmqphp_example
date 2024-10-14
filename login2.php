<?php
require_once __DIR__ . '/vendor/autoload.php'; //Rabbitmq library

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//Creds for login check
$validUsername = 'user';
$validPassword = 'password';

//retrieve post data from form
$username = $_POST['username'];
$password = $_POST['password'];

//auth check
if ($username === $validUsername && $password === $validPassword) {
    echo "Login successful!<br>";

    //establish rabbitmq connection
    $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
    $channel = $connection->channel();

    //declare rabbitmq queue
    $channel->queue_declare('login_queue', false, false, false, false);

    //create message
    $msg = new AMQPMessage("User $username logged in successfully.");

    //send message to the queue
    $channel->basic_publish($msg, '', 'login_queue');

    echo "Message sent to RabbitMQ: 'User $username logged in successfully.'";

    //close channel and connection
    $channel->close();
    $connection->close();
} else {
    echo "Login failed! Check your username and password.";
}
?>