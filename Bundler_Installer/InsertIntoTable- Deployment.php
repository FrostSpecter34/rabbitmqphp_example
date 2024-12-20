<?php

require_once(db_deploy.php);

function Package($nameofBundle, $versionofNumber, $status_code) {
    	$conn = dbConnect();
    	$create = $conn->prepare("INSERT INTO Package ($nameofBundle, $versionofNumber, $status_code) VALUES (?, ?, ?)");
    	if (!$create) {
        	return array("success" => false, "message" => "Failed to prepare statement.");
    	}
    	$create->bind_param($nameofBundle, $versionofNumber, $status_code);
    	$create->execute();

    	if ($create>affected_rows > 0) {
        	$nameofBundle = $create->insert_bundlename
        	$versionofNumber = $create->insert_versionNumber;
        	$create = $conn->prepare("INSERT INTO Package ($nameofBundle, $versionofNumber, $status_code) VALUES (?,?, ?)");
        	$create->bind_param($nameofBundle, $versionofNumber, $status_code);
        	$create->execute();
        	$create->close();
        	$conn>close();
    	}	

    	$create->close();
    	$db->close();
}
