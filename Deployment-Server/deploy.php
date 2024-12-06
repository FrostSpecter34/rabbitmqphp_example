<?php

class Deployer
{
    private $DevServer;
    private $QAServer;
    private $ProdServer;

      function __deployTo()
    {
        
     $this->DevServer = new rabbitMQClient('DevServer');
     $this->QAServer = new rabbitMQClient('QAServer');
     $this->ProdServer = new rabbitMQClient('ProdServer');
    }


    function deployWhat(bundleName, versionNumber, package_contents){
        $zippedFiles = new bundle_files();
        OpenFiles = retrive bundle_files();
        $dbSession = $sql->start_session($dbHost, $dbUser, $dbPass);
        
    
    }
    
    

    
function rollbackTo(){
    if (versionNumber == packageTable(versionNumber))
        

    
}
