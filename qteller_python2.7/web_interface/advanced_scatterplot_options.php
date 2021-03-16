<?php
class MyDB extends SQLite3
{
	function __construct()
	{
		$this->open('qt4db');
	}
}
$db = new myDB();
$result = $db->query("SELECT stub_id,experiment_id,type,link,source_id from data_sets");
$exp_array = array();
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
	if ($row['type'] == 'Expression') {

if (!(array_key_exists($row['source_id'],$exp_array))){
$exp_array[$row['source_id']] = array();
		}
$exp_array[$row['source_id']][$row['experiment_id']] = $row['stub_id'];
	}
}
$have_data = False;
$no_vals = True;
if (array_key_exists("name1",$_GET)) { 
$mygene1 = $_GET["name1"];
$mygene2 = $_GET["name2"];
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
<link rel="stylesheet" type="text/css" media="all" href="css/common.css" />
<div id="mainContainer">
<?php require('header.html'); ?>
<div id="pageContents">
<?php require('navigation.html'); ?>
<p><i>qTeller takes a little while to generate  figures. Don't panic it nothing happens for 30 seconds after you click a link</i></p>
<h3>Compare Expression of Genes</h3>
<form action="scatter_plot.php" method="post">
Gene1 Name: <input type="text" name='name1' <?php if ($have_data) {echo "value=\"$mygene1\"";} ?> /><br>
Gene2 Name: <input type="text" name='name2' <?php if ($have_data) {echo "value=\"$mygene2\"";} ?> /><br>
GRM* or AC*, this portion of qTeller is not classical maize gene aware<br>

<?php
echo $have_vals;
foreach ($exp_array as $paper => $data_sets) {
	if ($paper == '') continue;
	echo "<br><b>$paper:</b><br>";
	$all_vals = implode("|",$data_sets);
	echo "<input type=\"checkbox\" name=\"info[]\" value=\"$all_vals\"";
	if ($no_vals) { echo "checked=\"yes\"";}
	echo "/>Show all data from this paper<br>";
	echo "<table><tr>";
	$rowcount = 2;
	foreach ($data_sets as $mytext => $mystub) {
		if ($rowcount % 6 == 2) echo "<tr>";
		echo "<td><input type=\"checkbox\" name=\"info[]\" value=\"$mystub\"";
		if ($have_data && in_array($mystub,$info)) { echo "checked=\"yes\"";}
		echo " />$mytext</td>";
		if ($rowcount++ % 6 == 1) echo "</tr>";
		}
	echo "</table></tr>";
	} 
?>
<input type="submit" value="Submit" /><br>
<p>Try entering the id for glossy1 (GRMZM2G114642)</p>
<p>For AC* style genes, use the name that ends in FG### and not FGT###. Yes it's confusing terminology. Nobody consulted us when maize genes were being named.</p>
</body>
</html>
