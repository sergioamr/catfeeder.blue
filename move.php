<?php
if (!isset($_GET["type"])) {
    echo "no type";
    die();
}

if (!isset($_GET["filename"])) {
    echo "no name";
    die();
}

$parts = explode("/", $_GET["filename"]);

$destination = "classifier/" . $_GET["type"] . "/" . $parts[1];

if (copy($_GET["filename"], $destination)) {
    echo "ok";
    touch ($_GET["filename"]. ".cls");
    file_put_contents($_GET["filename"]. ".cls", $_GET["type"]);
}
//echo $_GET["filename"] . " " . $destination;
?>