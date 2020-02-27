<html>
<body>
<?php


exec("convert ". $_GET['img'] ." -colors 4 -depth 4 -format \"%c\" histogram:info:", $a);

var_dump($a);


for ($i=0;$i<count($a);$i++) {
	$find = preg_match_all("/#(.*)/", $a[$i], $matches);
	//var_dump($matches);

	if ($find>0) {
		$colour = substr(trim($matches[1][0]),0,6);
		echo "<div style='width:64px; height:64px;background-color:".$colour.";'>";
		echo $colour;
		echo "</div>";

		//$name = trim($matches['1']).".jpg";
		//exec("convert -size 100x20 xc:$colour $name");
		echo '<br />'; 
		//echo "<img src=\"$name\"><br>";
	}
}

?>
</body>
</html>
