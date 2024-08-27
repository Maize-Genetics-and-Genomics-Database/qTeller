
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
$myversion = $_POST['version'];
$mystart = trim(escapeshellcmd($_POST['start']));
if (empty($mystart)) { $mystart=0; }
$mystop = trim(escapeshellcmd($_POST['stop']));
if (empty($mystop)) { $mystop=0; }
$mystart = str_replace(',','',$mystart);
$mystop = str_replace(',','',$mystop);
$mychr = escapeshellcmd($_POST['chr']);
$myinclude = escapeshellcmd(implode(",", $_POST['info']));

$mycommand = "python interval_handling/make_spreadsheet_B73v5.py --chr $mychr --start $mystart --stop $mystop";
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
echo "<a href=\"tmp/$mychr.$mystart.$mystop.html\">View results on your web browser</a><br>";
echo "<a href=\"tmp/$mychr.$mystart.$mystop.csv\">Download Results as a .csv spreadsheet</a>";
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