#!/bin/bash

# Define the bundle path and change depending on bundle version
BUNDLE_PATH="/home/mdl35/bundles/bundle_20241210_115427_VER_1.0.zip"

# IP addresses and usernames for each VM
# Mario PROD
VM1_USER="mdl35"
VM1_IP="172.25.232.217"

# Mario QA
VM2_USER="mdl35"
VM2_IP="172.25.126.228"

# JAMES PROD
VM3_USER="jsq2"
VM3_IP="172.25.49.129"

# JAMES QA 
VM4_USER="jsq2"
VM4_IP="172.25.38.17"

# RAHUL PROD
VM5_USER="rahuljinka1"
VM5_IP="172.25.166.29"

# RAHUL QA
VM6_USER="rahul-jinka"
VM6_IP="172.25.226.207"


# Copy the bundle to each VM
echo "Copying to VM1..."
scp "$BUNDLE_PATH" "$VM1_USER@$VM1_IP:/home/$VM1_USER/"

echo "Copying to VM2..."
scp "$BUNDLE_PATH" "$VM2_USER@$VM2_IP:/home/$VM2_USER/"

echo "Copying to VM3..."
scp "$BUNDLE_PATH" "$VM3_USER@$VM3_IP:/home/$VM3_USER/"

echo "Copying to VM4..."
scp "$BUNDLE_PATH" "$VM4_USER@$VM4_IP:/home/$VM4_USER/"

echo "Copying to VM5..."
scp "$BUNDLE_PATH" "$VM5_USER@$VM5_IP:/home/$VM5_USER/"

echo "Copying to VM6..."
scp "$BUNDLE_PATH" "$VM6_USER@$VM5_IP:/home/$VM6_USER/"

echo "All copies complete."