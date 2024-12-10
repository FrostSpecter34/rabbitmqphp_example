<?php
require_once('client_rmq_db.php');

$request = array();
$request['type'] = "test";

$returnedResponse = createRabbitMQClientDatabase($request);

echo $returnedResponse;
?>
