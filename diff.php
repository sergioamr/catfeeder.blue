<html>
<body>
Test
<?php

$files = glob('./gallery-images/*.jpg');
usort($files, function($a, $b) {
    return filemtime($a) < filemtime($b);
});

usort($files, create_function('$a,$b', 'return filemtime($a)<filemtime($b);'));

$prev = "";
$count = 0;

$pct = 0.01;
$count = 0;

if (isset($_GET["pct"])) 
	$pct = floatval($_GET["pct"]);

foreach($files as $file) {
	if (!empty($prev)) {
		$output = "./diffs/diff_".hash("md5",$file);	
		
		$exe = "compare ".$file." ".$prev." -compose src ".$output.".png";
		// echo $exe . "<br>";
		if (!file_exists($output.".png")) {
			exec($exe);
			$exe = "compare -metric RMSE ".$file." ".$prev." NULL: 2> " .$output.".txt";
			echo "<hr>".$exe."<br>";	
			exec($exe, $metric);			
		}
		
		echo "<p>";
		$out = file_get_contents($output.".txt"); 
		$has_matches = preg_match("/\(([0-9]*\.[0-9]*)\)/", $out, $matches_out);
		//echo $matches_out[1]."<br>";
		echo $matches_out[1]."<br>";

		if (floatval( $matches_out[1] > $pct)) {
			//echo "<p>".$out."</p>";
			?><img src="<?= $file ?>" width='128' ><?php
		        ?><img src="<?= $output ?>.png" width='128'><br><?php 	
		}
		echo "</p>";
		
		$a = array();		
		if ($count<100) {
			echo "<div style='width:1024px;height:100px'>";
			$exe = "convert ". $file ." -colors 6 -depth 4 -format \"%c\" histogram:info:";
			//echo "<hr>".$exe."<br>";
			exec($exe, $a);
			//echo "<p>";
			//var_dump($a);
			//echo "</p>";

			$colors_array = array();
			$values_array = array();
			for ($i=0;$i<count($a);$i++) {
				$find = preg_match_all("/([0-9]+):.*#(.*)\s/", $a[$i], $matches);				
				//$find = preg_match_all("/#(.*)/", $a[$i], $matches);				
				//var_dump($matches);

				if ($find>0) {
					$number = substr(trim($matches[1][0]),0,6);
					array_push($values_array, $number);
					
					$colour = substr(trim($matches[2][0]),0,6);
					array_push($colors_array, $colour);					
										
					//echo "<div style='width:120px;float:left;height:64px;background-color:".$colour.";'>";
					//echo $number. "<br>";
					//echo "#".$colour. "<br>";
					//echo "</div>";
				}
			}
			echo "<br>";
			
			array_multisort($values_array, SORT_DESC, $colors_array);
			//print_r($colors_array);
			for ($i=0;$i<count($colors_array);$i++) {
				$colour = $colors_array[$i];
				echo "<div style='width:120px;float:left;height:64px;background-color:".$colour.";'>";
				echo $values_array[$i]. "<br>";
				echo "#".$colour. "<br>";
				echo "</div>";
			}
			
			echo "<br>";
			$count++;
			echo "</div>";
		}		
		echo "<hr>";
	}
	$prev = $file; 
	$count++;
	if ($count>64)
		break;
}

?>
</body>
</html>
