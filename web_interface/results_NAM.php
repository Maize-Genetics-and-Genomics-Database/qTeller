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
$myfiltered = $_POST['filtered'];
$mystart = trim($_POST['start']);
if (empty($mystart)) { $mystart=0; }
$mystop = trim($_POST['stop']);
if (empty($mystop)) { $mystop=0; }
$mystart = str_replace(',','',$mystart);
$mystop = str_replace(',','',$mystop);
$mychr = $_POST['chr'];
$myinclude = $_POST['info'];
$mycommand = "python interval_handling/make_spreadsheet_NAM.py --chr $mychr --start $mystart --stop $mystop --filtered $myfiltered";
$mycommand = $mycommand . " --included_vals " . implode(",", $myinclude);
exec($mycommand);
#echo $mycommand;
echo "<a href=\"tmp/$mychr.$mystart.$mystop.$myfiltered.html\">View results on your web browser</a><br>";
echo "<a href=\"tmp/$mychr.$mystart.$mystop.$myfiltered.csv\">Download Results as a .csv spreadsheet</a>";
?>
<br>
<br>
<br>
<div id="tool2Container">
<p><h3>Problems? Questions? Let us know through the Contact page!</h3></p>
</div>
</div>
</div>
</div>
</body>
</html>

