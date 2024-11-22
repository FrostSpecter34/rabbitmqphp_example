#!/usr/bin/env python3
import os
import zipfile
import shutil
import sys

# Configuration
BUNDLE_DIR = "/home/mdl35/bundles"  # Path to the .zip file to install
INSTALL_DIR = "/home/mdl35/tester"  # Where files will be installed
TEMP_DIR = "/tmp/bundle"  # Temporary location to unzip the files

def get_latest_bundle():
    """Identify the latest bundle in the BUNDLE_DIR."""
    bundles = [os.path.join(BUNDLE_DIR, f) for f in os.listdir(BUNDLE_DIR) if f.endswith('.zip')]
    if not bundles:
        raise FileNotFoundError("No bundle files found in the specified directory.")
    
    # Sort by modification time (newest first)
    bundles.sort(key=os.path.getmtime, reverse=True)
    latest_bundle = bundles[0]
    print(f"Latest bundle identified: {latest_bundle}")
    return latest_bundle

def extract_bundle(bundle_path):
    """Extract the bundle to a temporary directory."""
    # Create a temporary directory to extract the bundle
    if not os.path.exists(TEMP_DIR):
        os.makedirs(TEMP_DIR)

    # Extract the .zip file to TEMP_DIR
    with zipfile.ZipFile(bundle_path, 'r') as zip_ref:
        zip_ref.extractall(TEMP_DIR)
        print(f"Extracted {bundle_path} to {TEMP_DIR}")

def install_files():
    """Install files from the extracted bundle to the target directory."""
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
    """Remove the temporary extraction directory."""
    if os.path.exists(TEMP_DIR):
        shutil.rmtree(TEMP_DIR)
        print(f"Cleaned up temporary files in {TEMP_DIR}")

def post_install():
    """Perform any post-installation tasks, like restarting services."""
    os.system("sudo systemctl restart apache2")  # Example: Restarting Apache2
    print("Post-installation tasks completed.")

def main():
    """Run the installation process."""
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