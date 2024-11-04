<?php
    require_once __DIR__ . '/../path.inc';
    require_once __DIR__ . '/../get_host_info.inc';
    require_once __DIR__ . '/../rabbitMQLib.inc';

    //  creates rabbitMq client instance for DMZ server
    function createClientForDmz($request){
        $client = new rabbitMQClient("../rabbitmqphp_example/rabbitMQ_dmz.ini", "testServer");
       
        if(isset($argv[1])){
            $msg = $argv[1];
        }
        else{
            $msg = "client";
        }
        $response = $client->send_request($request);
        return $response;
    }
?>