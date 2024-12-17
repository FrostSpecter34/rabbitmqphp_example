#!/bin/bash

# Open a file/folder selection window to select the bundle file to send
BUNDLE_PATH=$(zenity --file-selection --title="Select Bundle File")

# Will check if the user selected a file, if not, it will exit.
if [ -z "$BUNDLE_PATH" ]; then
  echo "No bundle selected. Exiting."
  exit 1
fi

# Checks if the selected file exists
if [[ ! -f "$BUNDLE_PATH" ]]; then
  echo "Error: The specified bundle file does not exist."
  exit 1
fi

# IP addresses and usernames for the deployment VM
# Mario PROD
VM1_USER="hami"
VM1_IP="172.25.25.223"
VM1_PASS="Watermelon843%"

# Automates the SCP transfer with expect
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

# Copies and sends the bundle to the deployment VM
echo "Copying to VM1..."
copy_with_password "$VM1_USER" "$VM1_IP" "$VM1_PASS"