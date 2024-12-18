requrire_once(testRabbitMQ.ini);
import os
import datetime

<?php

$connection = new AMQPStreamConnection('172.25.205.131', 5672, 'hami', 'hami', 'DMZ');
$channel = $connection->channel();
$channel->queue_declare('testQueue', false, true, false, false);
$testExchange = 'testExchange';
$fanout = 'fanout'; 
$channel->exchange_declare($testExchange, $fanout, false, true, false);

?>

filepath = '/var/log/mysql/error.log'
logSavedPath = '/etc/hami'

if os.path.exists(filepath):
    with open (filename) as file
   logfile = file.read();
    print(logfile);
else:
    return null;

if (is_dir(logSavedpath))
shutil.copy(filepath, logSavedpath);

def logSaved(logName, logContent)
return logSavedpath();

timestamp = datetime();
Content = [timestamp] logContent;

with open('filepath', 'Content') as file:
    file.write('logSavedpath')
print(Content);

<?php

$msg = new AMQPMessage('Events by' Content);
$channel->basic_publish($msg, $testExchange);


$channel->close();
$connection->close();
?>
