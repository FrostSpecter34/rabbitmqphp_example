<?php
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
        
        $hostname = '127.0.0.1';
        $username = 'adam';
        $password = 'adam';
        $sourceQueue = 'testqueue';
        $destinationQueue ='testqueue';
        

$connection = new AMQPStreamConnection('localhost', 5672, 'adam', 'adam', 'testHost');
$channel = $connection->channel();
//Declare source queue
$channel->queue_declare($sourceQueue, false, true, false, false, false, []);

$channel->queue_declare($destinationQueue, false, true, false, false, false, []);

//Publish message to database
$callback = function ($msg) use ($channel,$destinationQueue) {
    //Add msg processing here if needed
    $channel->basic_publish($msg, '', $destinationQueue);
};

//Consume incoming messages
$channel->basic_consume($sourceQueue,'', false, true, false, false, $callback);

//Keeps channel running
while($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();


?>
