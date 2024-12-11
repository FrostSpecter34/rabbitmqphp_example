#!/usr/bin/env python3
import os
import zipfile
import shutil
import sys

# Get the current user's home directory
HOME_DIR = os.path.expanduser("~")

# Configuration
BUNDLE_DIR = HOME_DIR  # Path to the .zip file to install
INSTALL_DIR = os.path.join(HOME_DIR, "installed_bundle")
TEMP_DIR = "/tmp/bundle"  # Temporary location to unzip the files

def get_latest_bundle():
    """Update the latest bundle in the BUNDLE_DIR."""
    bundles = [os.path.join(BUNDLE_DIR, f) for f in os.listdir(BUNDLE_DIR) if f.endswith('.zip')]
    if not bundles:
        raise FileNotFoundError("No bundle files found in the specified directory.")
    
    # Sort by modification time (newest first)
    bundles.sort(key=os.path.getmtime, reverse=True)
    latest_bundle = bundles[0]
    print(f"Latest bundle identified: {latest_bundle}")
    return latest_bundle

def extract_bundle(bundle_path):
    """Extracts the bundle to a temporary directory."""
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
def install_files():
    """Install files from the extracted bundle to the target directories."""
    if not os.path.exists(INSTALL_DIR):
        os.makedirs(INSTALL_DIR)
        print(f"Install directory {INSTALL_DIR} created.")
    else:
        print(f"Install directory {INSTALL_DIR} already exists.")

    for root, dirs, files in os.walk(TEMP_DIR):
        # Create directories in the target location
        for directory in dirs:
            dest_dir = os.path.join(INSTALL_DIR, os.path.relpath(os.path.join(root, directory), TEMP_DIR))
            os.makedirs(dest_dir, exist_ok=True)
            print(f"Created directory: {dest_dir}")

        # Copy files to the target location
        for file in files:
            src_file = os.path.join(root, file)
            dest_file = os.path.join(INSTALL_DIR, os.path.relpath(src_file, TEMP_DIR))
            shutil.copy2(src_file, dest_file)  # Copy file with metadata
            print(f"Installed: {src_file} to {dest_file}")


def clean_up():
    """Removes the temporary files."""
    if os.path.exists(TEMP_DIR):
        shutil.rmtree(TEMP_DIR)
        print(f"Cleaned up temporary files in {TEMP_DIR}")

def post_install():
    """Will restart services."""
    os.system("sudo systemctl restart apache2")  # Example: Restarting Apache2
    print("Post-installation tasks completed.")

def main():
    """Instalation proccess begins."""
    try:
        latest_bundle = get_latest_bundle()  # Find the latest bundle
        extract_bundle(latest_bundle)  # Extract it
        install_files()  # Install the files
        clean_up()  # Clean up temporary files
        post_install()  # Run any post-installation tasks
        print("Installation successful!")
    except Exception as e:
        print(f"Installation failed: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()