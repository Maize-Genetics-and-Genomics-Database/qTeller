<html>
<head>
<title>Results</title>
</head>
<link rel="stylesheet" type="text/css" media="all" href="/qteller2/css/common.css" />
<div id="mainContainer">
<?php require('header.php'); ?>
<div id="pageContents">
<?php require('navigation.html'); ?>
<body>
<?php 
$user = "qteller2";
$password = "qtl2paper";
$database = "qteller2";
mysql_connect('localhost',$user,$password);
@mysql_select_db($database) or die("Unable to select database");
$myversion = $_POST['version'];
#$mystart = $_POST['start'];
#$mystop = $_POST['stop'];
#$mychr = $_POST['chr'];
$myname = $_POST['name'];
#echo $mychr;
#need to test to make sure these are integers
$query = "SELECT * FROM $myversion WHERE name='$myname'";
#echo "$query <br>";
$result = mysql_query($query);
$num=mysql_numrows($result);
if ( $num == 0 ) {
	$query = "SELECT * CLASSIC classic WHERE val='$myname'";
	$temp_result = mysql_query($query);
	$num=mysql_numrows($result);
	if ($num == 0) {
		echo "No gene named $myname found within the version of the maize genome specified ($myversion)";
		die;
		}
	else {
		$myname = mysql_result($temp_result,0,'name');
		}
	}
$i = 0;
#echo "number of rows: $num" ;
$header[] = "Gene Name";
$header[] = "Chromosome";
$header[] = "Start";
$header[] = "Stop";
while ( $i < 1 ) {
$mydata = array(mysql_result($result,$i,'name'),mysql_result($result,$i,"chr"),mysql_result($result,$i,"start"),mysql_result($result,$i,"stop"));
$i++;
}


$func_array = array(
	"maize_seq_anno" => "Maizesequence.org's automated functional annotation",
	"classic" => "Classical Maize Gene?",
	);
$ortholog_array = array(
	"brachy" => "Brachypodium",
	"fox" => "Foxtail Millet/Setaria",
	"rice" => "Rice",
	"sorghum" => "Sorghum",
	"maize" => "Maize (homeolog)",
	);
	
echo "<h2><b>$myname</b></h2>";

echo "<h3><b>Known Functional Data:</b></h3>";
echo "<p>";
foreach ($func_array as $mydb => $defline) {
	$query = "SELECT val FROM $mydb WHERE name='$myname'";
	$result = mysql_query($query);
	$num = mysql_numrows($result);
	if ($num > 0) {
		$this_val = mysql_result($result,0,"val");
		echo "<b>$defline</b>: $this_val ";
		}
	}
echo "</p>";

echo "<h3><b>Expression Data:</b></h3>";
echo "<p>";
foreach ($exp_array as $mydb => $defline) {
	$query = "SELECT val FROM $mydb WHERE name='$myname'";
	$result = mysql_query($query);
	$num = mysql_numrows($result);
	if ($num > 0) {
		$this_val = mysql_result($result,0,"val");
		echo "<b>$defline</b>: $this_val ";
		}
	}
echo "</p>";

echo "<h3><b>Ortholog Data:</b></h3>";
echo "<p>";
foreach ($ortholog_array as $mydb => $defline) {
	$query = "SELECT val FROM $mydb WHERE name='$myname'";
	$result = mysql_query($query);
	$num = mysql_numrows($result);
	if ($num > 0) {
		$this_val = mysql_result($result,0,"val");
		echo "<b>$defline</b>: $this_val ";
		}
	}
$query = "SELECT * FROM gevo WHERE name='$myname'";
$result = mysql_query($query);
$num = mysql_numrows($result);
if ($num > 0) {
	$this_val = mysql_result($result,0,"val");
	echo "<br><a href=\"$this_val\">Compare Orthologs with GEvo</a>";
	}
echo "</p>";


mysql_close();
?>
</div>
</div>
</body>
</html>
