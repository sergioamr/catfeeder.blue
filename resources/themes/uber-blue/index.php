<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
//header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
$files = glob('./gallery-images/*.jpg');

usort($files, function($a, $b) {
    return strcmp($b, $a);
});

/*
array_multisort(
    array_map( 'filemtime', $files ),
    SORT_NUMERIC,
    SORT_DESC,
    $files
  );
*/

$prev = "";
$count = 0;

$pct = 0.07;

if (isset($_GET["classify"])) {
    $pct = 0.03;
    $classify = 1;
} else{
    $classify = 0;
}

if (isset($_GET["pct"]))
    $pct = floatval($_GET["pct"]);

foreach($files as $file) {
    if (!empty($prev)) {
        $output = "./diffs/diff_".hash("md5",$file);

        $exe = "compare ".$file." ".$prev." -compose src ".$output.".png";
        if (!file_exists($output.".png")) {
            exec($exe);
            $exe = "compare -metric RMSE ".$file." ".$prev." NULL: 2> " .$output.".txt";
            //echo "<hr>".$exe."<br>";
            exec($exe, $metric);
            $count++;
        }
    }

    $prev = $file;
    if ($count>32)
        break;
}

?>
    <title>Fraggle Rock</title>
    <link rel="shortcut icon" href="<?php echo THEMEPATH; ?>/images/favicon.png" />

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo THEMEPATH; ?>/rebase-min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo THEMEPATH; ?>/style.css" />
    <?php echo $gallery->getColorboxStyles(5); ?>

    <script type="text/javascript" src="//code.jquery.com/jquery-2.1.4.min.js"></script>
    <?php // echo $gallery->getColorboxScripts(); ?>

    <?php file_exists('googleAnalytics.inc') ? include('googleAnalytics.inc') : false; ?>

    <meta http-equiv="refresh" content="300" />
    <meta http-equiv="cache-control" content="max-age=300" />

    <link rel="stylesheet" href="/convnet_64/css/style.css">

<?php
//##################### NEURAL NETWORK ##########################
?>
<script src="/convnet_64/build/vis.js"></script>
<script src="/convnet_64/build/util.js"></script>

<script src="/convnet_64/src/convnet_init.js"></script>
<script src="/convnet_64/src/convnet_util.js"></script>
<script src="/convnet_64/src/convnet_vol.js"></script>
<script src="/convnet_64/src/convnet_vol_util.js"></script>
<script src="/convnet_64/src/convnet_layers_dotproducts.js"></script>
<script src="/convnet_64/src/convnet_layers_pool.js"></script>
<script src="/convnet_64/src/convnet_layers_input.js"></script>
<script src="/convnet_64/src/convnet_layers_loss.js"></script>
<script src="/convnet_64/src/convnet_layers_nonlinearities.js"></script>
<script src="/convnet_64/src/convnet_layers_dropout.js"></script>
<script src="/convnet_64/src/convnet_layers_normalization.js"></script>
<script src="/convnet_64/src/convnet_net.js"></script>
<script src="/convnet_64/src/convnet_trainers.js"></script>
<script src="/convnet_64/src/convnet_magicnet.js"></script>
<script src="/convnet_64/src/convnet_export.js"></script>

<script src="/convnet_64/js/image-helpers.js"></script>
<script src="/convnet_64/js/images-demo.js"></script>
<script src="/convnet_64/js/pica.js"></script>

<script src="/convnet_64/output/fr_labels.js"></script>
<script src="/convnet_64/output/fr_network.js"></script>

<script type="text/javascript">

var urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null) {
       return undefined;
    }
    return decodeURI(results[1]) || 0;
}

function open_modal() {

}

var image_channels = 3;

var testImage_NoVis = function(parent, img) {
    var x = convnetjs.img_to_vol(img);
    var out_p = net.forward(x);

    var preds =[]
    for(var k=0;k<out_p.w.length;k++) { preds.push({k:k,p:out_p.w[k]}); }
    preds.sort(function(a,b){return a.p<b.p ? 1:-1;});

    var probsdiv = document.createElement('div');
    var results = parent.find(".probsdiv");
    if (results.length > 0) {
        probsdiv = results[0];
    }

    console.log("Src " + parent[0].dataset.src);

    var t = '';
    for(var k=0;k<4;k++) {
        classy_name = classes_txt[preds[k].k];
        var col = 'rgb(247,247,247); color: gray';

        if (classy_name == "food")
            col = 'rgb(162,141,120); color: white';

        if (classy_name == "rock")
            col = 'rgb(210,210,210); color: black';

        if (classy_name == "fraggle")
            col = 'rgb(40,40,40); color: gray';

        if (preds[k].p > 0.25) {
            t += '<div class=\"pp\" style=\"padding:5px; width:' + (15 + Math.floor(preds[k].p/1*32)) + 'px; background-color:' + col + ';\">';

            <?php if ($classify) { ?>
                t += '<button class="btn_classifier" onclick="classify(\''+ classy_name.trim() +'\', \'' + parent[0].dataset.src + '\');">' + classy_name + '</button>';
            <?php } else { ?>
                t += classy_name;
            <?php } ?>

            t += '</div>';
        }
    }

    probsdiv.innerHTML = t;
    probsdiv.className = 'probsdiv';
    probsdiv.setAttribute ("class", "probsdiv");
    parent.append(probsdiv);
}

function resize(parent, src, visualize){
    var image = new Image();
    image.src = src;
    image.onload = function() {
        var canvas = document.createElement('canvas');
        canvas.width = image.width;
        canvas.height = image.height;
        var ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(image, 0, 0, image.width, image.height);

        var dst = document.createElement('canvas');
        dst.width = image_dimension;
        dst.height = image_dimension;

        window.pica.WW = false;
        window.pica.resizeCanvas(canvas, dst, {
            quality: 2,
            unsharpAmount: 500,
            unsharpThreshold: 100,
            transferable: false
        }, function (err) {  });
        window.pica.WW = true;

        var image_scaled = new Image();
        image_scaled.src = dst.toDataURL("image/png");
        image_scaled.onload = function() {
            if (!visualize)
                testImage_NoVis(parent, image_scaled);
            else
                testImage(image_scaled);

            trigger_next_classify();
        }
    }
}

function trigger_next_classify() {
    var ele = $(".not_classified")[0];
    if (ele === undefined) {
        console.log("Finished classifying");
        return;
    }

    ele.calculate_net = function() {
        var src = $(this)[0].src;
        var p = $(this).parent();
        resize(p, src, false);
        $(this).removeClass("not_classified");
        $(this).addClass("classified");
        return this; // This is needed so others can keep chaining off of this
    };
    ele.calculate_net();
}

function load_network_by_name(my_network_name) {
    console.log("------------ Loading network " + my_network_name + " --------------");
    $.getJSON( "/network/" + my_network_name + ".json", function( json ) {
        net = new convnetjs.Net();
        net.fromJSON(json);
        trigger_next_classify();
    });
}

function load_network() {
    var mynet = urlParam('network');

    var classified = $(".classified");
    classified.removeClass("classified");
    classified.addClass("not_classified");

    if (mynet !== undefined) {
        network_name = mynet;
        load_network_by_name(mynet);
    } else {
        load_network_by_name(network_name + "_final");
    }

/*
    $(".food_view").hover(
      function(){
            $(this).css("opacity",0.5);
            var src = $(this)[0].src;
            var p = $(this).parent();
            console.log("Calculate hover");
            resize(p, src, false);
       }, function(){
        $(this).css("opacity",1);
       }
    );
*/
    $(".food_view").click(
      function() {
            //alert("test");
            $('#myModal').modal({
                show: 'false'
            });

            var src = $(this)[0].src;
            var p = $(this).parent();
            resize(p, src, true);
       }
    );
}


</script>

<?php
//##################### NEURAL NETWORK ##########################
?>

</head>
<body onLoad="load_network()">
<!--
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
  Launch demo modal
</button>
-->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Network Visualization</h4>
      </div>
      <div class="modal-body">
      <div class="divsec">

        <div class="container_btn">
        <div id="testset_vis"></div>
		</div>

            <div id="visnet"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
<!--        <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>

<div id="galleryWrapper">
<script type="text/javascript">

function classify(my_type, filename) {
    console.log(" Test " + my_type + " " + filename);

    $.ajax({
        url: 'move.php?type=' + my_type + "&filename=" + filename,
        success: function(data) {
            $('#notification-bar').text('Ok');
        },
        error: function() {
            $('#notification-bar').text('An error occurred');
        },
        type: 'GET'
    });
}

function cycleImages(){
	$( ".cycler" ).each(function() {
		  var $active = $(this).find('.active');
		  var $next = ($active.next().length > 0) ? $active.next() : $(this).find('img:first');
		  $next.css('z-index',2);//move the next image up the pile
		  $active.fadeOut(1000,function(){//fade out the top image
			  $active.css('z-index',1).show().removeClass('active');//reset the z-index and unhide the image
			  $next.css('z-index',3).addClass('active');//make the next image the top one
		  });
	});
}

<?php include 'csv_multiline.js';?>

$(document).ready(function(){
    // run every 2s
	//setInterval('cycleImages()', 2000);
})</script>

<script src="http://d3js.org/d3.v4.min.js"></script>
<script src="http://code.jquery.com/jquery.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

<h1><?php include 'gallery-images/display.html';?><i class="pull-right" id="notification-bar"></i></h1>
<h4 style="display:none" id="food_today"></h4>

    <?php if($gallery->getSystemMessages()): ?>
        <ul id="systemMessages">
            <?php foreach($gallery->getSystemMessages() as $message): ?>
                <li class="<?php echo $message['type']; ?>">
                    <?php echo $message['text']; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div id="galleryListWrapper">
        <?php
		 $oldday = 0;
		 if (!empty($galleryArray) && $galleryArray['stats']['total_images'] > 0): ?>
            <ul id="galleryList" class="clearfix"  style="color: white;">
            <?php

		$file_count = 0;
		foreach ($galleryArray['images'] as $image):

            $file = "./".$image['file_path'];
            $output = "./diffs/diff_".hash("md5",$file);
            $display = true;
            $value = 0;

            if (file_exists($output.".txt")) {
                $out = file_get_contents($output.".txt");
                $has_matches = preg_match("/\(([0-9]*\.[0-9]*)\)/", $out, $matches_out);
                //echo $matches_out[1]." ";

                $value = floatval($matches_out[1]);

                if (floatval( $matches_out[1] <= $pct)) {
                    $display=false;
                    $file_count++;
                    if ($file_count>25) {
                        unlink($file);
                        unlink($output.".txt");
                    }
                }
    		}

		    if ($display == true) {
                $day = date("d", filemtime($image['file_path']));
                if (strcmp($day,$oldday)) {
                    $file_date = date("Y_m_d", filemtime($image['file_path']));

                    ?></ul>
                    <?php
                    echo "<br><div  style='color: grey; float:left;'>".date ("l d", filemtime($image['file_path']));
                    ?></div>&nbsp;<div id='total_<?php echo $file_date;?>' style='color: grey;float:right;'> Total!</div>
                    <div class="line"></div>

                    <?php
                    $file_name = "gallery-images/".$file_date.".txt";
                    $div_name = "graph_".hash("md5",$file_name);
                    ?>

                    <div width="960" height="500" id="<?php echo $div_name; ?>" style="color: white;"></div>
                    <script>create_graph("#<?php echo $div_name; ?>", "<?php echo $file_name; ?>", "#total_<?php echo $file_date; ?>");</script>
                    <div class="line"></div>

                    <ul id="galleryList" class="clearfix"  style="color: white;"><?php
                }
                $oldday = $day;

                ?><li class="image_thumb"><?php
                   echo date ("[H:i]", filemtime($image['file_path']));
                   $pos = strpos($image['file_path'], "manual");
                   if ($pos === false) {

                   } else {
                        echo " Manual";
                   }

                ?>
				<div class="container_btn" data-src="<?php echo $image['file_path'];?>">
					<img src="<?php echo $image['thumb_path']; ?>" alt="<?php echo $image['file_title']." ".$value; ?>" width="100" height="100" class="food_view not_classified"/>

<?php
if ($classify == 1) {
    if (!file_exists( $image['file_path'].".cls")) {
    ?>
					<button class="btn_fraggle" onclick="classify('fraggle', '<?php echo $image['file_path'];?>');">Fraggle</button>
					<button class="btn_rock" onclick="classify('rock', '<?php echo $image['file_path'];?>');">Rock</button>
					<button class="btn_empty"  onclick="classify('empty', '<?php echo $image['file_path'];?>');">Empty</button>
                    <button class="btn_random"  onclick="classify('random', '<?php echo $image['file_path'];?>');">Random</button>
					<button class="btn_food"  onclick="classify('food', '<?php echo $image['file_path'];?>');">Food</button>
    <?php
    }
} ?>
				</div>

				</li>

                <?php
            }
    	endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>

    <div class="line"></div>
    <div id="galleryFooter" class="clearfix">

        <?php if ($galleryArray['stats']['total_pages'] > 1): ?>
        <ul id="galleryPagination">

            <?php foreach ($galleryArray['paginator'] as $item): ?>
                <li class="<?php echo $item['class']; ?>">
                    <?php if (!empty($item['href'])): ?>
                        <a href="<?php echo $item['href']; ?>"><?php echo $item['text']; ?></a>
                    <?php else: ?><?php echo $item['text']; ?><?php endif; ?>
                </li>
            <?php
			endforeach; ?>

        </ul>
        <?php endif; ?>

    </div>
<!--
	<div class="container">
		 <div style="float: left; width: 220px;">

<video width="200" height="163" autoplay>
  <source src="video.mp4" type="video/mp4">
Your browser does not support the video tag.
</video>

		<br style="clear: left;" />
		</div>
		 <div style="float: left; width: 220px;">
		  <div id="area1"></div>
            <div class="cycler">
            <?php
            $active = "class='active'";
            $count = 0;
            foreach ($galleryArray['images'] as $image):
                if ($count<50) {
                    $myimage = $image['file_path'];
                    if ((strpos($myimage, 'detected_B.jpg') !== false)) {
                        ?><img src="<?php echo $myimage ?>"<?php echo $active; ?> width=200 height=163/><?php
                    $active = "";
                    $count++;
                    }
                }
            endforeach;
            ?>
            </div>
            <br style="clear: left;" />
		</div>

		<div style="float: left; width: 220px;">
            <div class="cycler">
            <?php
            $active = "class='active'";
            $count = 0;
            foreach ($galleryArray['images'] as $image):
                if ($count<50) {
                    $myimage = $image['file_path'];
                    if ((strpos($myimage, 'detected_A.jpg') !== false)) {
                        ?><img src="<?php echo $myimage ?>"<?php echo $active; ?> width=200 height=163/><?php
                        $active = "";
                        $count++;
                    }
                }
            endforeach; ?>
            </div>
            <br style="clear: left;" />
		</div>
	</div>
-->

</div>


</body>
</html>
