<?php
require_once('client_rmq_dmz.php');


$request = array();
$request['type'] = "test";

$returnedResponse = createRabbitMQClientDMZ($request);

echo $returnedResponse;
?>
