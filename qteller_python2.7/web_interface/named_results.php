<html>
<head>
<link rel="stylesheet" type="text/css" media="all" href="css/common.css" />
<div id="mainContainer">
<?php require('header.html'); ?>
<div id="pageContents">
<?php require('navigation.html'); ?>
<title>Results</title>
</head>
<body>
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
$myinclude = $_POST['info'];
$mycommand = "scl enable python27 'python interval_handling/make_spreadsheet_named.py --names " . $mynames . "'";
if ($myversion == '2F') {
	$mycommand = $mycommand . " --filtered ";
	}
if ($_POST['link'] == 'gevo') {
	$mycommand = $mycommand . " --link" ;
	}
$mycommand = $mycommand . " --included_vals " . implode(",", $myinclude);
exec($mycommand);
#echo $mycommand;
echo "<a href=\"tmp/custom.html\">View results in your browser</a><br>";
echo "<a href=\"tmp/custom.csv\">Download Results as a .csv spreadsheet</a>";
?>
</div>
</div>
</body>
</html>
