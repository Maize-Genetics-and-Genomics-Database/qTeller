<html>
<body>
<html>
<body>
<link rel="stylesheet" type="text/css" media="all" href="css/common.css" />
<div id="mainContainer">
<?php require('header.html'); ?>
<div id="pageContents">
<?php require('navigation.html'); ?>
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
$function_array = array();
$exp_array = array();
$synteny_array = array();
$all_array = array();
$fun_array = array();
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
	if ($row['type'] == 'Synteny') {
		$synteny_array[] = "<input type=\"checkbox\" name=\"info[]\" value=\"" . $row['stub_id'] . "\" /> <a href=\"" . $row['link'] . "\">" . $row['experiment_id'] . "</a><br>";
		$all_array[] = $row['stub_id'];
		}
	if ($row['type'] == 'Anno') {
		$function_array[] = "<input type=\"checkbox\" name=\"info[]\" value=\"" . $row['stub_id'] . "\" />" . $row['experiment_id'] . " <a href=\"" . $row['link'] . "\">(" . $row['source_id'] . ")</a>";
		$fun_array[] = $row['stub_id'];
		}
	if ($row['type'] == 'Expression') {
		$exp_array[] = "<input type=\"checkbox\" name=\"info[]\" value=\"" . $row['stub_id'] . "\" />" . $row['experiment_id'] . " <a href=\"" . $row['link'] . "\">(" . $row['source_id'] . ")</a>";
		$all_array[] = $row['stub_id'];
		}
	}
?>
<p>Welcome to qTeller, the simple tool for digging up information on the genes hiding inside your favorite QTL or mutant mapping interval.</p>
<p>Links are to the publications in which different data sets were first published.</p>
<form action="named_results.php" method="post">
<p>Paste gene IDs here. One gene per row.</p>
<TEXTAREA NAME=gene_names rows=25 cols=50 wrap=physical>
</TEXTAREA>
<br>
<?php
$all_vals = implode(",",$fun_array) . ',' .implode(",",$all_array);
echo "<input type=\"checkbox\" name=\"info[]\" value=\"$all_vals\" /> <b>Just show me everything</b> <-- If you check this don't check any other boxes<br><br>";
?> 

<b>Functional Data:</b><br>
<?php
foreach ($function_array as $i => $aoption) {
	echo $aoption;
	echo "<br>";
	}
?>
<b>Expression Data Sources:</b><br>
<?php
$rowcount = 2;
echo "<table><tr>";
foreach ($exp_array as $i => $aoption) {
	if ($rowcount % 3 == 2) echo "<tr>";
	echo "<td>" . $aoption . "</td>";
	if ($rowcount++ % 3 == 1) echo "</tr>";
	}
echo "</tr></table>";
?>
<br>
<b>Syntenic Orthologs:</b><br>
<?php
$rowcount = 0;
echo "<table><tr>";
foreach ($synteny_array as $i => $aoption) {
	if ($rowcount % 3 == 2) echo "<tr>";
	echo "<td>" . $aoption . "</td>";
	if ($rowcount++ % 3 == 1) echo "</tr>";
	}
echo "</tr></table>";
?>
<input type="checkbox" name="link" value="gevo" /> GEvo Link (compare all orthologs)
<br>
<input type="submit" value="Submit" />
</body>
</html>
