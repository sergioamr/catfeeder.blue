<?php
define("UPLOAD_DIR", "/home/smartinez/catfeeder/gallery-images/");

if (!empty($_FILES["myFile"])) {
    $myFile = $_FILES["myFile"];

    if ($myFile["error"] !== UPLOAD_ERR_OK) {
        echo "<p>An error occurred.</p>";
        exit;
    }

    // ensure a safe filename
    $name = preg_replace("/[^A-Z0-9._-]/i", "_", $myFile["name"]);

    // don't overwrite an existing file
	echo $name."\n";
    $i = 0;
   $parts = pathinfo($name);
    if (file_exists(UPLOAD_DIR . $name)) {
       unlink(UPLOAD_DIR . $name);
    }

    // preserve file from temporary directory
    $success = move_uploaded_file($myFile["tmp_name"],
        UPLOAD_DIR . $name);
    if (!$success) {
        echo "<p>Unable to save file.</p>";
        exit;
    }

    if (!strcmp($name,"display.html")) {

	$out= file_get_contents(UPLOAD_DIR . $name);
	$has_matches = preg_match_all("/\[([0-9]+)\]/", $out, $matches_out);
	$value = "";
	if($has_matches) {
        	$mydate = date("Y-m-d H:i");

                $value = $mydate. ",".intval( $matches_out[1][0]). ",".intval($matches_out[1][1])."\n";
	        echo $value;
	        $my_file = 'data-1.csv';
	        $handle = fopen($my_file, 'a');
	        fwrite($handle, $value);
	        fclose($handle);

		$my_file_date = UPLOAD_DIR.date("Y_m_d").".txt";
		if (!is_file($my_file_date)) {
			$handle = fopen($my_file_date,"w");
			fwrite($handle,"date,weight,total\n");
		} else {
			$handle = fopen($my_file_date,"a");
		}

		fwrite($handle, $value);
		fclose($handle);
	}

    }


    if (!strcmp($name,"dispense.html")) {

        $out= file_get_contents(UPLOAD_DIR . $name);
        $has_matches = preg_match("/\[([0-9]+)\]/", $out, $matches_out);
        if($has_matches) {
                $mydate = date("Y-m-d H:i");
                $value = $mydate. ",".intval( $matches_out[1]). "\n";
                echo $value;
                $my_file = 'data-2.csv';
                $handle = fopen($my_file, 'a');
                fwrite($handle, $value);
                fclose($handle);
        }

    }

 // set proper permissions on the new file
    chmod(UPLOAD_DIR . $name, 0644);
} else {

        $out="07:54:07 Weight [13] Total [37]";
	echo $out."<br>";
        $has_matches = preg_match_all("/\[([0-9]+)\]/", $out, $matches_out);
        if($has_matches) {
                $mydate = date("Y-m-d H:i");
		var_dump($matches_out);
                $value = $mydate. ",".intval( $matches_out[1][0]). ",".intval($matches_out[1][1])."\n";
                echo $value;


                $my_file_date = UPLOAD_DIR.date("Y_m_d").".txt";
		echo $my_file_date;
                if (!is_file($my_file_date)) {
                        $handle = fopen($my_file_date,"w");
                        fwrite($handle,"date,weight,total\n");
                } else {
                        $handle = fopen($my_file_date,"a");
                }

                fwrite($handle, $value);
                fclose($handle);
}

}

