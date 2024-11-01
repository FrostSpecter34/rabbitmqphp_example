<?php
require_once(__DIR__ . "/../vendor/autoload.php");
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');
require_once('db_connect.php');
require_once('db_functions.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;

use Database\{Users, Subs, Services, PaymentInfo};
use RabbitMQ\RabbitMQServer;

//Code for the request processor
function requestProcessor($messageBody)
{
    $data = json_decode($messageBody, true);
    if(isset($data['action'])){
        switch ($data['action']){
            case 'register':
                registerUser($data['username'], $data['email'], $data['password']);
                break;
            case 'login':
                userLogin($data['username'], $data['email'], $data['password']);
                break;
            case 'addService':
                addService($data['user_id'], $data['service_Details']);
                break;
            case 'paymentUpdate':
                updatePayment($data['user_id'], $data['payment_info']);
                break;
            default:
                echo "Unknown action: " . $data['action'] . "\n";
            } 
        }
    else{
        echo "Invalid message format\n";
    }
}

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'mdl35', 'mdl35', 'testHost');
$channel = $connection->channel();

$queueName = 'TestQueue';
$channel->queue_declare($queueName, false, true, false, false);

$callback = function($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    requestProcessor($msg->body);
    $msg->ack();
};
$channel->basic_qos(null, 1, null);
$channel->basic_consume($queueName, '', false, false, false, false, $callback);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

while($channel->is_consuming()) {
    $channel->wait();
}
$channel->close();
$connection->close();
?>