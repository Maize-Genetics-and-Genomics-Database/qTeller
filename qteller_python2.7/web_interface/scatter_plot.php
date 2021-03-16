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
if (array_key_exists("name1",$_POST)) { $mygene1 = trim($_POST["name1"]); } else { $mygene1 = trim($_GET["name1"]);}
if (array_key_exists("name2",$_POST)) { $mygene2 = trim($_POST["name2"]); } else { $mygene2 = trim($_GET["name2"]);}
if (array_key_exists("info",$_POST)) { $myinfo = $_POST["info"]; } else { $myinfo = array($_GET["info"]);}
if (array_key_exists("xmax",$_POST)) { $xmax = $_POST["xmax"]; } elseif (array_key_exists("xmax",$_GET)) { $xmax = $_GET["xmax"]; } else { $xmax = 0; }
if (array_key_exists("ymax",$_POST)) { $ymax = $_POST["ymax"]; } elseif (array_key_exists("ymax",$_GET)) { $ymax = $_GET["ymax"]; } else { $ymax = 0; }
if (empty($ymax)) { $ymax = 0; }
if (empty($xmax)) { $xmax = 0; }
$all_info = implode("|",$myinfo);
#$mygene2 = $_POST['name2'];
#echo exec("echo \$MPLCONFIGDIR");
putenv("MPLCONFIGDIR=/tmp/");
#echo "<p>python image_handling/make_scatter.py $mygene1,$mygene2 </p>";
#echo "python image_handling/make_scatterplot.py --gene1 $mygene1 --gene2 $mygene2 --exps \"$all_info\" --xmax $xmax --ymax $ymax";
exec("scl enable python27 'python image_handling/make_scatterplot.py --gene1 $mygene1 --gene2 $mygene2 --exps \"$all_info\" --xmax $xmax --ymax $ymax'");
echo "<i>Move your mouse over a dot to see details about that expression datapoint.</i><br>";
require("./tmp/$mygene1-$mygene2-map.html");
echo "<br>";
echo "<form action=\"scatter_plot.php\" method=\"post\"><i>Zoom in by entering smaller maximum values</i><br>Maximum X-Axis Value: <input type=\"text\" name=\"xmax\"/> Maximum Y-Axis Value: <input type=\"text\" name=\"ymax\"/><br><input type=\"hidden\" name=\"name1\" value=\"$mygene1\"><input type=\"hidden\" name=\"name2\" value=\"$mygene2\"><input type=\"hidden\" name=\"info\" value=\"$all_info\"><br>To restore the default scale click \"Submit\" without entering any values. <input type=\"submit\" value=\"Submit\" /></form>";

echo "<a href=\"advanced_scatterplot_options.php?name1=$mygene1&name2=$mygene2&info=$all_info\">Advanced Options</a> <-- Allows you to chose which datasets to display.<br>";
echo "Download this image as <a href=\"tmp/$mygene1-$mygene2.png\">a high resolution PNG</a> or as <a href=\"tmp/$mygene1-$mygene2.svg\">a SVG</a> (best option editing in Inkscape/Illustrator)<br>";
echo "See the expression of <a href=\"bar_chart.php?name=$mygene1\">$mygene1</a> or <a href=\"bar_chart.php?name=$mygene2\">$mygene2</a> as a bar chart<br>";
echo "<a href=\"scatter_plot.php?name1=$mygene1&name2=$mygene2&xmax=$xmax&ymax=$ymax&info=$all_info\">Regenerate this particular analysis</a> <-- copy this link to let your boss, coworkers, or underlings see the exact same analysis without typing in gene names themselves.<br>";
echo "<a href=\"http://genomevolution.org/CoGe/GEvo.pl?accn1=$mygene1;accn2=$mygene2;prog=blastn;show_cns=1;autogo=1;skip_feat_overlap=0\" target=\"_blank\">Compare these genes in GEvo</a><br>";
?>
<a href="rna_data_sources.php">Papers These Data Come From</a><br>
</div>
</body>
</html>
