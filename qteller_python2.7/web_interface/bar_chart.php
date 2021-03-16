<?php
header("Pragma-directive: no-cache");
header("Cache-directive: no-cache");
header("Cache-control: no-cache");
header("Pragma: no-cache");
header("Expires: 0");
#error_reporting(32767);
if (array_key_exists("name",$_POST)) { $mygene = trim($_POST["name"]); } else { $mygene = trim($_GET["name"]);}
if (array_key_exists("info",$_POST)) { $myinfo = $_POST["info"]; } else { $myinfo = array($_GET["info"]);}
?>
<html>
<body>
<link rel="stylesheet" type="text/css" media="all" href="css/common.css" />
<div id="mainContainer">
<?php require('header.html'); ?>
<div id="pageContents">
<?php require('navigation.html'); ?>
<body>
<div id="datacontainer">
<?php
#echo exec("echo \$MPLCONFIGDIR");
putenv("MPLCONFIGDIR=/tmp/");
$all_info = implode("|",$myinfo);
echo exec("scl enable python27 'python image_handling/make_bar_chart.py --gene $mygene --exps \"$all_info\"'");
#echo "<p>$mygene</p>";
#echo "<p>$all_info</p>";
echo "<p><i>Click image to enlarge. Move your mouse over a dot to see details about that expression datapoint.</i></p>";
require("./tmp/$mygene-map.html");
echo "<br>";
echo "<a href=\"advanced_barchart_options.php?name=$mygene&info=$all_info\">Advanced Options</a> <-- Allows you to chose which datasets to display.<br>";
echo "Download this image as <a href=\"tmp/$mygene.png\">a high resolution PNG</a> or <a href=\"tmp/$mygene.svg\">a SVG</a> (best option editing in Inkscape/Illustrator)<br>";
echo "<a href=\"bar_chart.php?name=$mygene&info=$all_info\">Regenerate this particular analysis</a> <-- copy this link to let your boss, coworkers, or underlings see the exact same analysis without typing in gene names themselves.<br>";
?>
<a href="rna_data_sources.php">Papers These Data Come From</a><br>
</div>
</body>
</html>
