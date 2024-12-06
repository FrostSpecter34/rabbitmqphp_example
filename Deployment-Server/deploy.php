<?php

class Deployer
{
    private $DevServer;
    private $QAServer;
    private $ProdServer;

      function __DeployTo()
    {
     $this->DevServer = new rabbitMQClient('DevServer');
     $this->DevServer = new rabbitMQClient('QAServer');
     $this->DevServer = new rabbitMQClient('ProdServer');
    }
