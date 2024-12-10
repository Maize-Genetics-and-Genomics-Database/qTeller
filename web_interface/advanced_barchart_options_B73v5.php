

<?php
include_once("include.php");
class MyDB extends SQLite3
{
	function __construct()
	{
		$this->open('qt5db');
	}
}
$db = new myDB();
$result = $db->query("SELECT stub_id,experiment_id,type,link,source_id from data_sets");
$exp_array = array();
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
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
<h2><font color="green">Single-Gene Expression Options</font></h2>
<p><b><font color="red">NOTE</font></b>:<b> B73 v5 Visualize Expression </b><i> accepts only v5 gene IDs (Zm00001eb), not classical or GRMZM IDs. To convert your GRMZM IDs to v5, go</i><b> <a href="https://www.maizegdb.org/gene_center/gene#translate">HERE</a>.</b></p>
<p><b><font color="red">NOTE</font>: The Johnston 2014 [Scanlon Lab] biological replicates have not been averaged.</b></p>
<form action="bar_chart_B73v5.php" method="post">
<b><font color="green">Gene Name: <input id = "geneName" type="text" name='name' <?php if ($have_data) {echo "value=\"$mygene\"";} ?> /></font></b><br>
<p>Try entering the id for <i>lg1</i> <i id='gene4' onclick="copyToClipboard('gene4','geneName')">Zm00001eb000060</i></p>
<p>qTeller takes a little while to generate  figures. Don't panic it nothing happens for 30 seconds after you click Submit.</p>
<div id="subheaderContainer">
<p><h3><font color="white">Select expression data</font></h3></p>
        </div>
        <p><b>Select which expression datasets you would like to analyze:</b><br>
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
                echo "<td><input class=\"$class\" type=\"checkbox\" name=\"info[]\" value=\"$mystub\"";
                if ($have_data && in_array($mystub,$info)) { echo "checked=\"yes\"";}
                echo " />$mytext</td>";
                if ($rowcount++ % 4 == 1) echo "</tr>";
                }
	echo "</table></tr>";
	} 
?>
<br>
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit! " name = "gene">
</div>
</body>
</html>
