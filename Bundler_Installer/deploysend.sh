#!/bin/bash

# Define the bundle path and change depending on bundle version
BUNDLE_PATH="/home/mdl35/bundles/bundle_20241210_115427_VER_1.0.zip"

# IP addresses and usernames for each VM
# Mario PROD
VM1_USER="hami"
VM1_IP="172.25.25.223"
VM1_PASS="Watermelon843%"

# Function to automate scp with expect
copy_with_password() {
  local USER=$1
  local IP=$2
  local PASS=$3

  expect <<EOF
  spawn scp "$BUNDLE_PATH" "$USER@$IP:/home/$USER/"
  expect "password:"
  send "$PASS\r"
  expect eof
EOF
}

# Copy the bundle to each VM
echo "Copying to VM1..."
copy_with_password "$VM1_USER" "$VM1_IP" "$VM1_PASS"