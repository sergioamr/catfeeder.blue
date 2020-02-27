<?php
define("UPLOAD_DIR", "/home/smartinez/catfeeder/gallery-images/");

if (!empty($_FILES["myFile"])) {
    $myFile = $_FILES["myFile"];

    if ($myFile["error"] !== UPLOAD_ERR_OK) {
        echo "<p>An error occurred.</p>";
        exit;
    }

    // ensure a safe filename
    $name = time()."_".preg_replace("/[^A-Z0-9._-]/i", "_", $myFile["name"]);

    // don't overwrite an existing file
	echo $name."\n";
    $i = 0;
    $parts = pathinfo($name);
    while (file_exists(UPLOAD_DIR . $name)) {
        $i++;

	if ($parts["extension"]=="csv" || $parts["extension"]=="jpg" || $parts["extension"]=="txt" || $parts["extension"]=="html") {
	        $name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
	} else {
		echo "Unable to process file";
		exit;	
		$name = $parts["filename"] . "-" . $i . "._" . $parts["extension"];
	} 
    }

    // preserve file from temporary directory
    $success = move_uploaded_file($myFile["tmp_name"],
        UPLOAD_DIR . $name);
    if (!$success) {
        echo "<p>Unable to save file.</p>";
        exit;
    }

    // set proper permissions on the new file
    chmod(UPLOAD_DIR . $name, 0666);
} else {
?>
<!DOCTYPE html>
<html>
<body>

<form action="upload.php" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="myFile" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>

</body>
</html>
<?php
echo "Empty";
}

