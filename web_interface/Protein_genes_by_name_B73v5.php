<?php
include_once("include.php");
class MyDB extends SQLite3
{
        function __construct()
        {
                $this->open('gene_protein_qt5db');
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
<head>
<script type="text/javascript">

function copyToClipboard(elementId,TextId) {

  // Create a "hidden" input
  var aux = document.createElement("input");

  // Assign it the value of the specified element
  aux.setAttribute("value", document.getElementById(elementId).innerHTML);

  // Append it to the body
  document.body.appendChild(aux);

  // Highlight its content
  aux.select();

  // Copy the highlighted text
  document.execCommand("copy");

  // Remove it from the body
  document.body.removeChild(aux);

  let textarea = document.getElementById(TextId);
  textarea.focus();
  textarea.value += document.getElementById(elementId).innerHTML + "\n";
}

</script>
</head>
<body>
<link rel="stylesheet" type="text/css" media="all" href="css/common.css_Gene" />
<div id="mainContainer">
<?php require('header.html'); ?>
<div id="pageContents">
<?php require('welcome_navigation.html'); ?>
<div id="contentContainer">
<h2><font color="green">B73 v5 Expression and Abundance for Genes by Name</font></h2>
<p>Retrieve FPKM expression data or NSAF abundance data for a user-provided list of genes.</p>
<p><i><b><font color="red">NOTE</font></b>:</i><b> B73 v5 Genes By Name</b><i> accepts only v5 gene IDs (Zm00001eb), not classical or GRMZM IDs. To convert your GRMZM IDs to v5, go</i><b> <a href="https://www.maizegdb.org/gene_center/gene#translate">HERE</a>.</b></p>
<div id="subheaderContainer">
<p><h3><font color="white">Paste gene IDs</font></h3></p>
        </div>
<form action="Protein_named_results_B73v5.php" method="post">
<p><b><font color="green">Paste gene IDs in the box below. One gene per row.</font></b> <i>Try entering the ID for <i id='gene4' onclick="copyToClipboard('gene4','geneName')" style="cursor: pointer;color:blue;">Zm00001eb000020</i></i></p>
<TEXTAREA id = 'geneName' NAME=gene_names rows=25 cols=50 wrap=physical>
</TEXTAREA>
<br>
<!--<?php
//$all_vals = implode(",",$fun_array) . ',' .implode(",",$all_array);
$all_vals = implode(",",$fun_array);
echo "<input type=\"checkbox\" name=\"info[]\" value=\"$all_vals\" /> <b>Just show me everything</b> <-- If you check this don't check any other boxes<br><br>";
?> 
<br>
<b>Functional Data:</b><br>
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
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit for mRNA Abundance! " name = "gene">
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit for Protein Abundance! " name = "protein">
<br>
<br>
<p><b>Or select which expression or abundance datasets you would like to analyze:</b><br>

<div id="tool2Container">
<?php
foreach ($exp_array as $paper => $data_sets) {
        if ($paper == '') continue;
        $class = translateClass($paper);
        echo "<br><b>$paper:</b><input type=\"radio\" name=\"chk_$class\" value=\"on\" onclick=\"check_all_boxes('$class', true)\" /><i>All on</i> <input type=\"radio\" name=\"chk_$class\" onclick=\"check_all_boxes('$class', false)\" value=\"off\" /><i>All off</i><br>";
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
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit for mRNA Abundance! " name = "gene">
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit for Protein Abundance! " name = "protein">
<div>
</body>
</html>
