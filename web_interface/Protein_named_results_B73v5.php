
<html>
<head>
	<body>
<link rel="stylesheet" type="text/css" media="all" href="css/common.css_Gene" />
<div id="mainContainer">
<?php require('header.html'); ?>
<div id="pageContents">
<?php require('welcome_navigation.html'); ?>

<div id="contentContainer">
</head>
<div id="subheaderContainer">
<p><h2><font color="white">Results</font></h2></p>
  </div>
<?php

#echo $_POST;
$mynames = str_replace("\n","dummy",$_POST['gene_names']);
$mynames = preg_replace('/\s+/','',$mynames);
$myversion = $_POST['version'];
$mystart = trim($_POST['start']);
if (empty($mystart)) { $mystart=0; }
$mystop = trim($_POST['stop']);
if (empty($mystop)) { $mystop=0; }
$mystart = str_replace(',','',$mystart);
$mystop = str_replace(',','',$mystop);
$mychr = $_POST['chr'];
$myinclude = escapeshellcmd(implode(",", $_POST['info']));
$mynames = escapeshellcmd($mynames);
$myinclude = escapeshellcmd($myinclude);

if (isset($_POST["gene"])) {
$mycommand = "python interval_handling/make_spreadsheet_named_gene_B73v5.py --names $mynames";
}else if(isset($_POST["protein"])){
$mycommand = "python interval_handling/make_spreadsheet_named_B73v5_Pro.py --names $mynames";
}
if ($myversion == '2F') {
	$mycommand = $mycommand . " --filtered ";
	}
/*if ($_POST['link'] == 'gevo') {
	$mycommand = $mycommand . " --link" ;
	}
*/
$mycommand = $mycommand . " --included_vals " . $myinclude;
exec($mycommand);
#echo $mycommand;
echo "<a href=\"tmp/custom.html\">View results on your web browser</a><br>";
echo "<a href=\"tmp/custom.csv\">Download Results as a .csv spreadsheet</a>";
?>
<br>
<br>
<br>
<br>
<div id="tool2Container">
<p><h3>Problems? Questions? Let us know through the Contact page!</h3></p>
</div>
</div>
<br>
<br>
</div>
</div>
</div>
</body>
</html>