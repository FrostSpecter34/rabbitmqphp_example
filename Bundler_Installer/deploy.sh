#!/bin/bash

# Opens up a window to select the bundle file.
BUNDLE_PATH=$(zenity --file-selection --title="Select Bundle File")

# Will check if the user selected a file, if not, it will exit.
if [ -z "$BUNDLE_PATH" ]; then
  echo "No bundle selected. Exiting."
  exit 1
fi

# Check if the selected file exists
if [[ ! -f "$BUNDLE_PATH" ]]; then
  echo "Error: That bundle file doesn't exist."
  exit 1
fi

# IP addresses, usernames, and passwords for each VM
# Mario PROD
VM1_USER="mdl35"
VM1_IP="172.25.232.217"
VM1_PASS="mdl35it490"

# Mario QA
VM2_USER="mdl35"
VM2_IP="172.25.126.228"
VM2_PASS="mdl35it490"


# JAMES PROD
VM3_USER="jsq2"
VM3_IP="172.25.49.129"
VM3_PASS="IT490passsword"


# JAMES QA 
VM4_USER="jsq2"
VM4_IP="172.25.38.17"
VM4_PASS="IT490passsword"

# RAHUL PROD
VM5_USER="rahuljinka1"
VM5_IP="172.25.166.29"
VM5_PASS="jinkarahul"


# RAHUL QA
VM6_USER="rahul-jinka"
VM6_IP="172.25.226.207"
VM6_PASS="rahuljinka"


# This function automates the SCP transfer with expect
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

# Copies and sends the bundle to each VM
echo "Copying to VM1..."
copy_with_password "$VM1_USER" "$VM1_IP" "$VM1_PASS"

echo "Copying to VM2..."
copy_with_password "$VM2_USER" "$VM2_IP" "$VM2_PASS"

echo "Copying to VM3..."
copy_with_password "$VM3_USER" "$VM3_IP" "$VM3_PASS"

echo "Copying to VM4..."
copy_with_password "$VM4_USER" "$VM4_IP" "$VM4_PASS"

echo "Copying to VM5..."
copy_with_password "$VM5_USER" "$VM5_IP" "$VM5_PASS"

echo "Copying to VM6..."
copy_with_password "$VM6_USER" "$VM6_IP" "$VM6_PASS"

echo "All copies complete."