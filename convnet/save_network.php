<?php
define("UPLOAD_DIR", "/home/smartinez/catfeeder/network/");

function is_ajax() {
      return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

if (is_ajax()) {
    echo "{ }";

    $file = fopen(UPLOAD_DIR . "network.json","w+");

    $content = trim(file_get_contents("php://input"));
    $jsonDecode = json_decode($content);

    fwrite($file, $jsonDecode);
    fclose($file);

} else {
?>
<!DOCTYPE html>
<html>
<body>
</body>
</html>
<?php
echo "Empty";
}

