#!/bin/bash

# Define the bundle path and change depending on bundle version
BUNDLE_PATH="/home/mdl35/bundles/bundle_20241210_115427_VER_1.0.zip"

# IP addresses and usernames for each VM
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
VM5_PASS="rahuljinka"


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