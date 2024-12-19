function dbConnection() {
    
    $hostname = '127.0.0.1';
    $user = 'remote';
    $pass = 'Watermelon843%';
    $dbname = 'packageTable';

$conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
