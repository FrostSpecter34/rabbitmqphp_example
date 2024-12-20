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
BUNDLE_DIR = os.path.join(HOME_DIR)
REPO_DIR = os.path.join(HOME_DIR, "rabbitmqphp_example")
TEMP_DIR = "/tmp/bundle"

def get_latest_bundle():
    """Update the latest bundle in the BUNDLE_DIR."""
    print("Checking for bundles in the directory...")
    bundles = [os.path.join(BUNDLE_DIR, f) for f in os.listdir(BUNDLE_DIR) if f.endswith('.zip')]
    if not bundles:
        print("No bundles found in the directory.")
        return None

    bundles.sort(key=os.path.getmtime, reverse=True)
    latest_bundle = bundles[0]
    print(f"Latest bundle identified: {latest_bundle}")
    return latest_bundle

def get_target_folder(bundle_name):
    """Map the bundle name to the corresponding folder in the repo."""
    base_name = bundle_name.split('_')[0].lower()

    # Define a mapping of base names to the exact folder names, can be updated with any new folders
    # Make sure to use the full, correct names of the folders for the bundles
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
    # Creates a temporary directory to extract the bundle
    if os.path.exists(TEMP_DIR):
        # Clean up the temporary directory
        shutil.rmtree(TEMP_DIR)
        print(f"Existing temporary directory {TEMP_DIR} removed.")

    # Creates a new temporary directory
    os.makedirs(TEMP_DIR)
    print(f"Temporary directory {TEMP_DIR} created.")

    # Extracts the .zip file to TEMP_DIR
    with zipfile.ZipFile(bundle_path, 'r') as zip_ref:
        zip_ref.extractall(TEMP_DIR)
        print(f"Extracted {bundle_path} to {TEMP_DIR}")

def install_files(bundle_name):
    """Install files from the extracted bundle to the target directory."""
    print(f"Installing files from bundle {bundle_name}...")
    target_folder = get_target_folder(os.path.basename(bundle_name))
    target_path = os.path.join(REPO_DIR, target_folder)

    if not os.path.exists(target_path):
        raise FileNotFoundError(f"Target directory {target_path} does not exist. Ensure the repo is set up correctly.")

    print(f"Installing files to: {target_path}")

    for root, dirs, files in os.walk(TEMP_DIR):
        for file in files:
            src_file = os.path.join(root, file)
            dest_file = os.path.join(target_path, os.path.relpath(src_file, TEMP_DIR))
            os.makedirs(os.path.dirname(dest_file), exist_ok=True)
            shutil.copy2(src_file, dest_file)
            print(f"Installed: {src_file} to {dest_file}")

def clean_up():
    """Removes the temporary files."""
    print("Cleaning up temporary files...")
    if os.path.exists(TEMP_DIR):
        shutil.rmtree(TEMP_DIR)
        print(f"Cleaned up temporary files in {TEMP_DIR}")

# Function for the installation process
def install_latest_bundle():
    """Installation process begins."""
    try:
        latest_bundle = get_latest_bundle()
        if latest_bundle:
            print(f"Starting installation for {latest_bundle}")
            extract_bundle(latest_bundle)
            install_files(latest_bundle)
            clean_up()
            print("Installation successful!")
        else:
            print("No new bundle found.")
    except Exception as e:
        print(f"Installation failed: {e}")
        sys.exit(1)

def main():
    last_installed_bundle = None

    # Continuously monitors the bundle directory (aka /home/<username>/) for new files
    while True:
        print("Waiting for a new bundle...")
        result = subprocess.run(['/usr/bin/inotifywait', '-e', 'create', '--format', '%f', BUNDLE_DIR],capture_output=True, text=True)
        if result.returncode == 0:
            new_file = result.stdout.strip()
            print(f"New file detected: {new_file}")
            sys.stdout.flush()

            # The delay is necessary for it to work properly
            time.sleep(2)
            print("Waiting for file write to complete...")

            # Service now checks for any of the latest bundle sent
            latest_bundle = get_latest_bundle()
            if latest_bundle and latest_bundle != last_installed_bundle:
                print(f"New bundle detected: {latest_bundle}")
                install_latest_bundle()
                last_installed_bundle = latest_bundle
        else:
            print(f"inotifywait failed with return code {result.returncode}")
if __name__ == "__main__":
    main()