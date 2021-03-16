<html>
<body>
<link rel="stylesheet" type="text/css" media="all" href="css/common.css" />
<div id="mainContainer">
<?php require('header.html'); ?>
<div id="pageContents">
<?php require('navigation.html'); ?>
<p><i>qTeller takes a little while to generate  figures. Don't panic if nothing happens for 30 seconds after you click a link</i></p>
<h3>Single Gene Expression</h3>
<form action="bar_chart.php" method="post">
<a href="br-example.png"><img src="br-example.png" width=300 /></a><br>
Gene Name: <input type="text" name='name' /> ZM*, Maize_refgen4<br>
<input type="submit" value="Submit" /><br>
<p><a href="advanced_barchart_options.php">Advanced options</a></p>
<p>Try entering the id for glossy1 (Zm00001d020557)</p>
<h3>Two Gene Scatterplot</h3>
</form>
<form action="scatter_plot.php" method="post">
<a href="sc-example.png"><img src="sc-example.png" width=300 /></a><br>
Gene1 Name: <input type="text" name='name1' /><br>
Gene2 Name: <input type="text" name='name2' /><br>
<input type="submit" value="Submit" /></form>
<p><a href="advanced_scatterplot_options.php">Advanced options</a></p>
<p>Stumped? Try comparing the expression of these two genes (Zm00001d020557) and (Zm00001d027281)<p>
</body>
</html>
