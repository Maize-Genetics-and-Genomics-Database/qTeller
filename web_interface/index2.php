<html>
<body>
<html>
<body>
<link rel="stylesheet" type="text/css" media="all" href="/qteller2/css/common.css" />
<div id="mainContainer">
<?php require('header.html'); ?>
<div id="pageContents">
<?php require('navigation.html'); ?>
<p>Welcome to qTeller, the simple tool for digging up information on the genes hiding inside your favorite QTL or mutant mapping interval.</p>
<form action="one_gene.php" method="post">
Genome Version: <select name='version'>
<option value='2F'>B73_refgen2 (filtered gene set)</option>
<option value='2W'>B73_refgen2 (working gene set)</option>
<option value='1F'>B73_refgen1 (filtered gene set)</option>
<option value='1W'>B73_refgen1 (working gene set)</option>
</select><br>
Gene Name: <input type="text" name='name' /><br>
<input type="submit" value="Submit" />
