#!/usr/bin/env python3
import os
import zipfile
import shutil
import sys
import subprocess
import time

# Get the current user's home directory
HOME_DIR = os.path.expanduser("~")

# Configuration
BUNDLE_DIR = os.path.join(HOME_DIR)  # Path to the .zip file to install
REPO_DIR = os.path.join(HOME_DIR, "rabbitmqphp_example")  # Path to the cloned repo
TEMP_DIR = "/tmp/bundle"  # Temporary location to unzip the files

def get_latest_bundle():
    """Update the latest bundle in the BUNDLE_DIR."""
    print("Checking for bundles in the directory...")
    bundles = [os.path.join(BUNDLE_DIR, f) for f in os.listdir(BUNDLE_DIR) if f.endswith('.zip')]
    if not bundles:
        print("No bundles found in the directory.")
        return None

    # Sort by modification time (newest first)
    bundles.sort(key=os.path.getmtime, reverse=True)
    latest_bundle = bundles[0]
    print(f"Latest bundle identified: {latest_bundle}")
    return latest_bundle

def get_target_folder(bundle_name):
    """Map the bundle name to the corresponding folder in the repo."""
    # Extract the base name before the first underscore
    base_name = bundle_name.split('_')[0].lower()

    # Define a mapping of base names to the exact folder names
    folder_mapping = {
        'bundlerinstaller': 'Bundler_Installer',
        'db': 'db',
        'deploymentserver': 'Deployment-Server',
        'dmz': 'DMZ',
        'firewallrulesdirectory': 'Firewall Rules directory',
        'frontendmain': 'Frontend Main',
        'loginauthentication': 'Login Authentication',
        'rpc': 'rpc',
        'serverdocumentation': 'Server-Documentation',
        'vendor': 'vendor',
    }

    # Return the corresponding folder or raise an error if not found
    if base_name in folder_mapping:
        print(f"Target folder for {bundle_name} is {folder_mapping[base_name]}")
        return folder_mapping[base_name]
    else:
        raise ValueError(f"Unknown bundle type: {base_name}")

def validate_zip_file(bundle_path):
    """Ensure the provided file is a valid zip archive."""
    try:
        with zipfile.ZipFile(bundle_path, 'r') as zip_ref:
            bad_file = zip_ref.testzip()
            if bad_file:
                raise zipfile.BadZipFile(f"Corrupted file in archive: {bad_file}")
            print(f"{bundle_path} is a valid zip file.")
            return True
    except zipfile.BadZipFile as e:
        print(f"Invalid zip file: {e}")
        return False
    
def extract_bundle(bundle_path):
    """Extracts the bundle to a temporary directory."""
    if not validate_zip_file(bundle_path):
        raise ValueError(f"{bundle_path} is not a valid zip file.")

    print(f"Extracting bundle {bundle_path}...")
    # Create a temporary directory to extract the bundle
    if os.path.exists(TEMP_DIR):
        # Clean up the existing temporary directory
        shutil.rmtree(TEMP_DIR)
        print(f"Existing temporary directory {TEMP_DIR} removed.")

    # Create a new temporary directory
    os.makedirs(TEMP_DIR)
    print(f"Temporary directory {TEMP_DIR} created.")

    # Extract the .zip file to TEMP_DIR
    with zipfile.ZipFile(bundle_path, 'r') as zip_ref:
        zip_ref.extractall(TEMP_DIR)
        print(f"Extracted {bundle_path} to {TEMP_DIR}")

def install_files(bundle_name):
    """Install files from the extracted bundle to the target directory."""
    print(f"Installing files from bundle {bundle_name}...")
    target_folder = get_target_folder(os.path.basename(bundle_name))
    target_path = os.path.join(REPO_DIR, target_folder)  # Assuming repo is cloned in REPO_DIR

    if not os.path.exists(target_path):
        raise FileNotFoundError(f"Target directory {target_path} does not exist. Ensure the repo is set up correctly.")

    print(f"Installing files to: {target_path}")

    for root, dirs, files in os.walk(TEMP_DIR):
        # Copy files to the target location
        for file in files:
            src_file = os.path.join(root, file)
            dest_file = os.path.join(target_path, os.path.relpath(src_file, TEMP_DIR))
            os.makedirs(os.path.dirname(dest_file), exist_ok=True)
            shutil.copy2(src_file, dest_file)  # Copy file with metadata
            print(f"Installed: {src_file} to {dest_file}")

def clean_up():
    """Removes the temporary files."""
    print("Cleaning up temporary files...")
    if os.path.exists(TEMP_DIR):
        shutil.rmtree(TEMP_DIR)
        print(f"Cleaned up temporary files in {TEMP_DIR}")

def install_latest_bundle():
    """Installation process begins."""
    try:
        latest_bundle = get_latest_bundle()  # Find the latest bundle
        if latest_bundle:
            print(f"Starting installation for {latest_bundle}")
            extract_bundle(latest_bundle)  # Extract it
            install_files(latest_bundle)  # Install the files
            clean_up()  # Clean up temporary files
            print("Installation successful!")
        else:
            print("No new bundle found.")
    except Exception as e:
        print(f"Installation failed: {e}")
        sys.exit(1)

def main():
    last_installed_bundle = None

    while True:
        print("Waiting for a new bundle...")
        # Use inotifywait to wait for a new file in the BUNDLE_DIR
        result = subprocess.run(['inotifywait', '-e', 'create', '--format', '%f', BUNDLE_DIR], capture_output=True, text=True)
        if result.returncode == 0:
            new_file = result.stdout.strip()
            print(f"New file detected: {new_file}")

            # Introduce a delay to ensure the file is fully written
            time.sleep(2)  # Adjust delay as needed (2 seconds here)
            print("Waiting for file write to complete...")

            # Now check for the latest bundle and proceed
            latest_bundle = get_latest_bundle()
            if latest_bundle and latest_bundle != last_installed_bundle:
                print(f"New bundle detected: {latest_bundle}")
                install_latest_bundle()
                last_installed_bundle = latest_bundle
        else:
            print(f"inotifywait failed with return code {result.returncode}")
if __name__ == "__main__":
    main()