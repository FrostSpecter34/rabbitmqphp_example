import os
import zipfile
from datetime import datetime
import subprocess

# Allows the user to select files and directories to include in the bundle
def choose_files_to_include(directory):
    files_and_folders = []
    
    # List files and folders in the given directory
    for root, dirs, files in os.walk(directory):
        for name in files:
            files_and_folders.append(os.path.join(root, name))
        # Optionally, add directories to allow choosing specific directories as well
        for name in dirs:
            files_and_folders.append(os.path.join(root, name))
    
    # Displays the files and folders
    print("\nAvailable files and directories to include in the bundle:")
    for i, item in enumerate(files_and_folders):
        print(f"{i+1}. {item}")
    
    # Let the user select files/folders to include in the bundle via numbers, separated by commas
    # This is to allow the user to skip any unchanged or unnecessary files for the bundle
    selected_items = []
    while True:
        try:
            selection = input("\nEnter the numbers of the items to include, separated by commas (or 'done' to finish): ").strip()
            if selection.lower() == 'done':
                break
            selected_indices = [int(x.strip()) - 1 for x in selection.split(',')]
            for index in selected_indices:
                if 0 <= index < len(files_and_folders):
                    selected_items.append(files_and_folders[index])
                else:
                    print(f"Invalid selection: {index + 1}")
        except ValueError:
            print("Invalid input. Please enter numbers separated by commas.")
    
    return selected_items

def zenity_select_directory(title):
    result = subprocess.run(['zenity', '--file-selection', '--directory', '--title', title], stdout=subprocess.PIPE)
    directory = result.stdout.decode('utf-8').strip()
    return directory

def create_bundle():
    # Opens a window to select the output directory
    output_directory = zenity_select_directory("Select Output Directory")
    if not output_directory:
        print("No output directory selected. Exiting.")
        return

    # Opens a window to select the source directory
    source_directory = zenity_select_directory("Select Source Directory")
    if not source_directory:
        print("No source directory selected. Exiting.")
        return

    # Gets user input for the bundle name
    bundle_name_input = input("Enter the name for the bundle: ").strip()
    if not bundle_name_input:
        print("Bundle name cannot be empty. Exiting.")
        return

    # Gets user input for the version number
    version = input("Enter the version number for this bundle (default is 1.0.0): ").strip() or "1.0.0"
    timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
    bundle_name = f"{bundle_name_input}_{timestamp}_v{version}.zip"
    bundle_path = os.path.join(output_directory, bundle_name)
    
    # Ensures that the output directory exists
    if not os.path.exists(output_directory):
        os.makedirs(output_directory)

    files_to_include = choose_files_to_include(source_directory)

    # Creates the bundle as a .zip file
    with zipfile.ZipFile(bundle_path, 'w', zipfile.ZIP_DEFLATED) as bundle:
        for file_path in files_to_include:
            if os.path.exists(file_path):
                arcname = os.path.relpath(file_path, source_directory)
                bundle.write(file_path, arcname)
                print(f"Added: {file_path} as {arcname}")
            else:
                print(f"File not found: {file_path}")

    print(f"Bundle created at: {bundle_path}")

if __name__ == "__main__":
    create_bundle()