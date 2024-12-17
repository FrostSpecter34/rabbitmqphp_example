<?php

// Establishes connection to MySQL database
function dbConnection() {
    $hostname = '0.0.0.0';
    $user = 'remote';
    $pass = 'password';
    $dbname = 'Sub_Service';

    $connection = mysqli_connect($hostname, $user, $pass, $dbname);

    if (!$connection) {
        echo "Error connecting to database: " . mysqli_connect_error() . PHP_EOL;
        exit(1);
    }
    else{
        echo "Connection established to database" . PHP_EOL;
        return $connection;
    }
}
dbConnection();
?>