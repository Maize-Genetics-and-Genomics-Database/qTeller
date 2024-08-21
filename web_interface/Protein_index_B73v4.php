<?php
include_once("include.php");

class MyDB extends SQLite3
{
        function __construct()
        {
                $this->open('./gene_protein_qt4db');
        }
}

$db = new myDB();
$result = $db->query("SELECT stub_id,experiment_id,type,link,source_id from data_sets");
$function_array = array();
$exp_array = array();
$synteny_array = array();
$all_array = array();
$fun_array = array();
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        if ($row['type'] == 'Anno') {
                $function_array[] = "<input type=\"checkbox\" name=\"info[]\" value=\"" . $row['stub_id'] . 
"\" />" . $row['experiment_id'] . " <a href=\"" . $row['link'] . "\">(" . $row['source_id'] . ")</a>";
                $fun_array[] = $row['stub_id'];
                }
        if ($row['type'] == 'Expression') {

if (!(array_key_exists(" <a href=\"" . $row['link'] . "\">(" . $row['source_id'] . ")</a>",$exp_array))){
$exp_array[" <a href=\"" . $row['link'] . "\">(" . $row['source_id'] . ")</a>"] = array();
                }
$exp_array[" <a href=\"" . $row['link'] . "\">(" . $row['source_id'] . ")</a>"][$row['experiment_id']] = $row['stub_id'];
        $all_array[] = $row['stub_id'];
        }
}

$have_data = False;
$no_vals = True;
if (array_key_exists("name",$_GET)) { 
$mygene = $_GET["name"];
$have_data = True;
if (strlen($_GET['info']) != 0) { 
$no_vals = False;
$info = explode('|',$_GET['info']); }
else {
$no_vals = True;
$info = array();
}
}

?>

<html>
<body>
<link rel="stylesheet" type="text/css" media="all" href="css/common.css_Gene" />
<div id="mainContainer">
<?php require('header.html'); ?>
<div id="pageContents">
<?php require('welcome_navigation.html'); ?>
<div id="contentContainer">
<h2><font color="green">B73 v4 Expression and Abundance for Genes in an Interval</font></h2>
<p>Retrieve FPKM or NSAF information for all genes within specified genomic coordinates.</p>

<div id="subheaderContainer">
<p><h3><font color="white">Select genomic interval</font></h3></p>
        </div>
<p>To find the FPKM expression values or NSAF abundance values of genes within a genomic interval, select a chromosome, then enter the genome start and stop positions of your interval.</p>
<p>To select expression or abundance for <i>all</i> the genes in the genome, select "All Chromosomes" and leave start and end positions blank.</p>
<form action="Protein_results_B73v4.php" method="post">
<b><font color="green">Genome Version: <select name='version'>
<option value='2F'>B73_refgen4</option>
</select><br>
Chromosome: <select name='chr'>
<option value="chr1">Chromosome 1</option>
<option value="chr2">Chromosome 2</option>
<option value="chr3">Chromosome 3</option>
<option value="chr4">Chromosome 4</option>
<option value="chr5">Chromosome 5</option>
<option value="chr6">Chromosome 6</option>
<option value="chr7">Chromosome 7</option>
<option value="chr8">Chromosome 8</option>
<option value="chr9">Chromosome 9</option>
<option value="chr10">Chromosome 10</option>
<option value="all">All Chromosomes</option>
</select><br>
Genome Start Position (bp): <input type="text" name='start' /><br>
Genome End Position (bp): <input type="text" name="stop" /></font></b><br>


<!--<?php
//$all_vals = implode(",",$fun_array) . ',' .implode(",",$all_array);
$all_vals = implode(",",$all_array);
echo "<input type=\"checkbox\" name=\"info[]\" value=\"$all_vals\" /> <b>Just show me everything</b> <-- If you check this don't check any other boxes<br><br>";
?>--> 

<!--<b><font color="green">Functional Data:</font></b><br>
<?php
foreach ($function_array as $i => $aoption) {
        echo $aoption;
        echo "<br>";
        }
?>-->

<div id="subheaderContainer">
<p><h3><font color="white">Select expression or abundance data</font></h3></p>
        </div>
Links are to the publications in which different data sets were first published.<br></p>
<?php
$all_vals = implode(",",$all_array);
echo "<input type=\"checkbox\" name=\"info[]\" value=\"$all_vals\" /> <b>Show all expression or abundance data sources</b> <-- If you check this don't check any other boxes<br><br>";
?>
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit for mRNA Abundance! " name = "gene" onclick=" return confirmGetMessage()">
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit for Protein Abundance! " name = "protein" onclick=" return confirmGetMessage()">
<br>
<br>
<p><b>Or select which expression or abundance datasets you would like to analyze:</b><br>

<div id="tool2Container">

<?php
foreach ($exp_array as $paper => $data_sets) {
        if ($paper == '') continue;
        $class = translateClass($paper);
        echo "<br><b>$paper:</b><input type=\"radio\" name=\"chk_$class\" value=\"on\" onclick=\"check_all_boxes('$class', true)\" ><i>All on</i></input> <input type=\"radio\" name=\"chk_$class\" onclick=\"check_all_boxes('$class', false)\" value=\"off\" /><i>All off</i><br>";
        echo "<table><tr>";
        $rowcount = 2;
        
        foreach ($data_sets as $mytext => $mystub) {
                if ($rowcount % 4 == 2) echo "<tr>";
                echo "<td><input class=\"$class\" type=\"checkbox\" name=\"info[]\" value=\"$mystub\" />$mytext</td>";
                if ($rowcount++ % 4 == 1) echo "</tr>";
                }
        echo "</table></tr>";
        } 
?>
<br>
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit for mRNA Abundance! " name = "gene" onclick=" return confirmGetMessage()">
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit for Protein Abundance! " name = "protein" onclick=" return confirmGetMessage()">
<br>

</div>
</body>
</html>
