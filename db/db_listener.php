<?php
    require_once __DIR__ . '/../vendor/autoload.php'; 
    require_once __DIR__ . '/../path.inc';
    require_once __DIR__ . '/../get_host_info.inc';
    require_once __DIR__ . '/../rabbitMQLib.inc';
    require_once __DIR__ . '/rmqc.php';
    require_once __DIR__ . '/db_connect.php';

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

try {
    $connection = new AMQPStreamConnection('172.25.205.131', 5672, 'mdl35', 'mdl35', 'testHost');
    $channel = $connection->channel();
} catch (Exception $e) {
    echo 'Connection failed: ',  $e->getMessage(), "\n";
    exit(1);
}
$queueName = 'TestQueue';
$channel->exchange_declare('logs', AMQPExchangeType::FANOUT, false, true, false);
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