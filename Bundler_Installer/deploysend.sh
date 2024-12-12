#!/bin/bash

# Open a file/folder selection dialog using zenity
BUNDLE_PATH=$(zenity --file-selection --title="Select Bundle File")

# Check if the user selected a file (the return value will be empty if the user cancels)
if [ -z "$BUNDLE_PATH" ]; then
  echo "No bundle selected. Exiting."
  exit 1
fi

# Check if the selected file exists
if [[ ! -f "$BUNDLE_PATH" ]]; then
  echo "Error: The specified bundle file does not exist."
  exit 1
fi

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