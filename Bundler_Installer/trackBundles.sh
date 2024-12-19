#!/bin/bash

WATCH_DIR="/home/hami"

script = "/home/hami/Documents/GitHub/rabbitmqphp_example/Bundler_Installer/Bundler_Installer/InsertIntoTable- Deployment.php"

if [ ! -d "$WATCH_DIR" ]; then
    echo "Directory $WATCH_DIR does not exist. Exiting."
    exit 1
fi

inotifywait -m -e create "$WATCH_DIR" --format '%f' | while read FILE
do
    echo "New file detected: $FILE"
    php "$PHP_SCRIPT" "$FILE"
done



