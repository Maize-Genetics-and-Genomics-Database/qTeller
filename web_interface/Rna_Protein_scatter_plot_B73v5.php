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

if (array_key_exists("name1",$_POST)) { $mygene1 = trim($_POST["name1"]); } else { $mygene1 = trim($_GET["name1"]);}
if (array_key_exists("name2",$_POST)) { $mygene2 = trim($_POST["name2"]); } else { $mygene2 = trim($_GET["name2"]);}
if (array_key_exists("protein",$_POST)) { $protein = trim($_POST["protein"]); } elseif (array_key_exists("protein",$_GET)) { $protein = trim($_GET["protein"]);}
if (array_key_exists("gene",$_POST)) { $gene = trim($_POST["gene"]); } elseif (array_key_exists("gene",$_GET)) { $gene = trim($_GET["gene"]);}
if (array_key_exists("info",$_POST)) { $myinfo = $_POST["info"]; } elseif (array_key_exists("info",$_GET)) { $myinfo = $_GET["info"]; } else { $myinfo = 'all';}
if (array_key_exists("xmax",$_POST)) { $xmax = $_POST["xmax"]; } elseif (array_key_exists("xmax",$_GET)) { $xmax = $_GET["xmax"]; } else { $xmax = 0; }
if (array_key_exists("ymax",$_POST)) { $ymax = $_POST["ymax"]; } elseif (array_key_exists("ymax",$_GET)) { $ymax = $_GET["ymax"]; } else { $ymax = 0; }
if (empty($ymax)) { $ymax = 0; }
if (empty($xmax)) { $xmax = 0; }
if (is_array($myinfo)  && (sizeof($myinfo) != 0)){ 
#print_r($myinfo);
$all_info = implode("|",$myinfo);
} elseif($myinfo != 'all'){
$all_info = $myinfo;
#echo $all_info;
} else {
$all_info = 'all';
#echo $all_info;
}

#echo "\"$all_info\"";

$expression = str_replace("|","_",$all_info);
$length = strlen($expression);
if (($length > 150) && ($length % 2 == 0) ) {
     $middle = $length/2;
     $num = 150;
     #echo $middle;
     $expression = substr($expression, $middle,$num );
}else if(($length > 150) && ($length % 2 != 0)) {
     $middle = ($length-1)/2;
     $num = 150;
     #echo $middle;
     $expression = substr($expression, $middle, $num);
}

$genome_one = "rna";
$genome_two = "protein";
#$mygene2 = $_POST['name2'];
#echo exec("echo \$MPLCONFIGDIR");
putenv("MPLCONFIGDIR=/tmp/");
#echo "<p>python image_handling/make_scatter.py $mygene1,$mygene2 </p>";
#echo "python image_handling/make_scatterplot_B73v4_Pro.py --gene1 $mygene1 --gene2 $mygene2 --exps \"$all_info\" --xmax $xmax --ymax $ymax";
if ((isset($_POST["gene"]) or isset($_GET["gene"])) && ($all_info != 'all')) {
exec("python image_handling/make_scatterplot_gene_B73v5.py --gene1 $mygene1 --gene2 $mygene2 --exps \"$all_info\" --xmax $xmax --ymax $ymax");
}else if((isset($_POST["gene"]) or isset($_GET["gene"])) && ($all_info == 'all')){
exec("python image_handling/make_scatterplot_gene_B73v5.py --gene1 $mygene1 --gene2 $mygene2 --xmax $xmax --ymax $ymax");
}else if((isset($_POST["protein"]) or isset($_GET["protein"])) && ($all_info == 'all')){
exec("python image_handling/make_scatterplot_B73v5_Pro.py --gene1 $mygene1 --gene2 $mygene2 --xmax $xmax --ymax $ymax");
}else if((isset($_POST["protein"]) or isset($_GET["protein"])) && ($all_info != 'all')){
exec("python image_handling/make_scatterplot_B73v5_Pro.py --gene1 $mygene1 --gene2 $mygene2 --exps \"$all_info\" --xmax $xmax --ymax $ymax");
}
echo "<i>Move your mouse over a dot to see details about that expression and abundance datapoint.</i><br>";
echo "<br>";
if (isset($_POST["gene"]) or isset($_GET["gene"])){
require("./tmp/$mygene1-$mygene2-$expression-$genome_one-map.html");
echo "<br>";
echo "<form action=\"Rna_Protein_scatter_plot_B73v5.php\" method=\"post\"><i>Zoom in by entering smaller maximum values</i><br>Maximum X-Axis Value: <input type=\"text\" name=\"xmax\"/> Maximum Y-Axis Value: <input type=\"text\" name=\"ymax\"/><br><input type=\"hidden\" name=\"name1\" value=\"$mygene1\"><input type=\"hidden\" name=\"name2\" value=\"$mygene2\"><input type=\"hidden\" name=\"info\" value=\"$all_info\"><br>To restore the default scale click \"Submit\" without entering any values. <input type=\"submit\" value=\"Submit\" /></form>";

echo "<a href=\"Protein_advanced_scatterplot_options_B73v5.php?name1=$mygene1&name2=$mygene2&info=$all_info\">Advanced Options</a> <-- Allows you to chose which datasets to display.<br>";
echo "Download this image as <a href=\"tmp/$mygene1-$mygene2-$expression-$genome_one.png.png\">a high resolution PNG</a> or as <a href=\"tmp/$mygene1-$mygene2-$expression-$genome_one.png.svg\">a SVG</a> (best option editing in Inkscape/Illustrator)<br>";
echo "See the expression or abundance of <a href=\"Rna_Protein_bar_chart_B73v5.php?name=$mygene1&info=$all_info&gene=$gene\">$mygene1</a> or <a href=\"Rna_Protein_bar_chart_B73v5.php?name=$mygene2&info=$all_info&gene=$gene\">$mygene2</a> as a bar chart<br>";
echo "<a href=\"Rna_Protein_scatter_plot_B73v5.php?name1=$mygene1&name2=$mygene2&xmax=$xmax&ymax=$ymax&info=$all_info&gene=$gene\">Regenerate this particular analysis</a> <-- copy this link to share this analysis.<br>";
echo "<a href=\"http://genomevolution.org/CoGe/GEvo.pl?accn1=$mygene1;accn2=$mygene2;prog=blastn;show_cns=1;autogo=1;skip_feat_overlap=0\" target=\"_blank\">Compare these genes in GEvo</a><br>";
}else if(isset($_POST["protein"]) or isset($_GET["protein"])){
require("./tmp/$mygene1-$mygene2-$expression-$genome_two-map.html");
echo "<br>";
echo "<form action=\"Rna_Protein_scatter_plot_B73v5.php\" method=\"post\"><i>Zoom in by entering smaller maximum values</i><br>Maximum X-Axis Value: <input type=\"text\" name=\"xmax\"/> Maximum Y-Axis Value: <input type=\"text\" name=\"ymax\"/><br><input type=\"hidden\" name=\"name1\" value=\"$mygene1\"><input type=\"hidden\" name=\"name2\" value=\"$mygene2\"><input type=\"hidden\" name=\"info\" value=\"$all_info\"><br>To restore the default scale click \"Submit\" without entering any values. <input type=\"submit\" value=\"Submit\" /></form>";

echo "<a href=\"Protein_advanced_scatterplot_options_B73v5.php?name1=$mygene1&name2=$mygene2&info=$all_info\">Advanced Options</a> <-- Allows you to chose which datasets to display.<br>";
echo "Download this image as <a href=\"tmp/$mygene1-$mygene2-$expression-$genome_two.png\">a high resolution PNG</a> or as <a href=\"tmp/$mygene1-$mygene2-$expression-$genome_two.png.svg\">a SVG</a> (best option editing in Inkscape/Illustrator)<br>";
echo "See the expression or abundance of <a href=\"Rna_Protein_bar_chart_B73v5.php?name=$mygene1&info=$all_info&protein=$protein\">$mygene1</a> or <a href=\"Rna_Protein_bar_chart_B73v5.php?name=$mygene2&info=$all_info&protein=$protein\">$mygene2</a> as a bar chart<br>";
echo "<a href=\"Rna_Protein_scatter_plot_B73v5.php?name1=$mygene1&name2=$mygene2&xmax=$xmax&ymax=$ymax&info=$all_info&protein=$protein\">Regenerate this particular analysis</a> <-- copy this link to share this analysis.<br>";
echo "<a href=\"http://genomevolution.org/CoGe/GEvo.pl?accn1=$mygene1;accn2=$mygene2;prog=blastn;show_cns=1;autogo=1;skip_feat_overlap=0\" target=\"_blank\">Compare these genes in GEvo</a><br>";

}
?>
</div>
</body>
</html>
