import os
import zipfile
from datetime import datetime

# Configuration
SOURCE_DIR = "/home/mdl35/rabbitmqphp_example"  # Directory containing files to bundle
OUTPUT_DIR = "/home/mdl35/bundles"       # Directory to save the .zip file
BUNDLE_NAME = f"bundle_{datetime.now().strftime('%Y%m%d_%H%M%S')}.zip"

def bundle_files():
    # Ensure output directory exists
    if not os.path.exists(OUTPUT_DIR):
        os.makedirs(OUTPUT_DIR)

    # Full path to the output .zip file
    bundle_path = os.path.join(OUTPUT_DIR, BUNDLE_NAME)

    # Create the .zip file
    with zipfile.ZipFile(bundle_path, 'w', zipfile.ZIP_DEFLATED) as bundle:
        # Walk through the source directory and add files
        for root, dirs, files in os.walk(SOURCE_DIR):
            for file in files:
                file_path = os.path.join(root, file)
                # Add file to the zip, keeping relative paths
                arcname = os.path.relpath(file_path, SOURCE_DIR)
                bundle.write(file_path, arcname)
                print(f"Added: {file_path} as {arcname}")

    print(f"Bundle created: {bundle_path}")

if __name__ == "__main__":
    bundle_files()