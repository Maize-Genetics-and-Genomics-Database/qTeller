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
<link rel="stylesheet" type="text/css" media="all" href="css/common.css_Gene" />
<div id="mainContainer">
<?php require('header.html'); ?>
<div id="pageContents">
<?php require('welcome_navigation.html'); ?>
<body>
<div id="contentContainer">
<div id="tool2Container">
<?php
#echo exec("echo \$MPLCONFIGDIR");
putenv("MPLCONFIGDIR=/tmp/");
$all_info = implode("|",$myinfo);
$mygene = escapeshellcmd($mygene);
$all_info = escapeshellcmd($all_info);
echo exec("python image_handling/B73vsB104_chart_B73v4_B104v4.py --gene $mygene --exps \"$all_info\"");
#echo "<p>$mygene</p>";
#echo "<p>$all_info</p>";
echo "<p><i>Click image to enlarge. Move your mouse over a bar to see details about that expression datapoint.</i></p>";

$Nam1 = "B73 ".$mygene." GeneExpression";
$Nam2 = "B104 ".$mygene." GeneExpression";

require("./tmp/$Nam1-$Nam2-map.html");
echo "<br>";
echo "<a href=\"advanced_Scatterchart_options_B73v4_B104V4.php?name=$mygene&info=$all_info\">Advanced Options</a> <-- Allows you to chose which datasets to display.<br>";
echo "Download this image as <a href=\"tmp/$Nam1-$Nam2.png\">a high resolution PNG</a> or <a href=\"tmp/$Nam1-$Nam2.svg\">a SVG</a> (best option editing in Inkscape/Illustrator)<br>";
echo "<a href=\"Scatter_chart_B73v4_B104v4.php?name=$mygene&info=$all_info\">Regenerate this particular analysis</a> <-- copy this link to share this result.<br>";
?>
<br>
</div>
</body>
</html>
