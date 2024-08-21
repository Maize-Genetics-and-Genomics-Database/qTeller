
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
<h2><font color="green">B73 v4 Visualize Expression or Abundance</font></h2>
<p>View all dataset FPKM expression or NSAF abundance graphs for a query gene, or compare expression graphs between two genes. (To select only specific datasets, go to Advanced Options below.)</p>
<p>qTeller takes a little while to generate  figures. Don't panic if nothing happens for 30 seconds after you click Submit.</p>
<p><i><b><font color="red">NOTE</font></b>:</i><b> B73 v4 Visualize Expression or Abundance</b><i> accepts only v4 gene IDs (Zm00001), not classical or GRMZM IDs. To convert your GRMZM IDs to v4, go</i><b> <a href="https://www.maizegdb.org/gene_center/gene#translate">HERE</a>.</b></p>
<div id="subheaderContainer">
<p><h3><font color="white">Single-Gene Expression or Abundance</font></h3></p>
        </div>

<br>
<form action="Rna_Protein_bar_chart_B73v4.php" method="post">
<a href="qT_bc2.png"><img src="qT_bc2.png" class="image1" width=500 /></a><br>
<br>
<div id="tool2Container">
	<br>
<b><font color="green">Gene Name: <input id = "geneName" type="text" name='name' /> </font></b>ZM*, Maize_refgen4<br>
<p>Try entering the id for glossy1 <i id='gene4' onclick="copyToClipboard('gene4','geneName')" style="cursor: pointer;color:blue;">Zm00001d045462</i></p>
<p>To select only specific datasets, go to <a href="Protein_advanced_barchart_options_B73v4.php">Advanced options</a></p>
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit for mRNA Abundance! " name = "gene">
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit for Protein Abundance! " name = "protein">
<br>
</div>
<br>
<div id="subheaderContainer">
<p><h3><font color="white">Two-Gene Scatterplot</font></h3></p>
        </div>

<br>
</form>
<form action="Rna_Protein_scatter_plot_B73v4.php" method="post">
<a href="qT_sc2.png"><img src="qT_sc2.png" class="image1" width=430 /></a><br>
<br>
<div id="tool2Container">
	<br>
<b><font color="green">
Gene1 Name: <input id = 'geneName1' type="text" name='name1' /><br>
Gene2 Name: <input id = 'geneName2' type="text" name='name2' /><br></font></b>
<br>
<p>Try comparing the expression or abundance of these two genes <i id='gene5' onclick="copyToClipboard('gene5','geneName1')" style="cursor: pointer;color:blue;">Zm00001d045462</i> and <i id='gene6' onclick="copyToClipboard('gene6','geneName2')" style="cursor: pointer;color:blue;">Zm00001d045520</i><p>
<p>To select only specific datasets, go to <a href="Protein_advanced_scatterplot_options_B73v4.php">Advanced options</a></p>
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit for mRNA Abundance! " name = "gene">
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit for Protein Abundance! " name = "protein">
<br>
<br>
</div>
<div id="subheaderContainer">
<p><h3><font color="white">Single-Gene Expression vs Abundance</font></h3></p>
        </div>

<br>
</form>
<form action="ProteinvsGene_chart_B73v4.php" method="post">
<a href="ProteinvsGene.png"><img src="ProteinvsGene.png" class="image1" width=500 /></a><br>
<br>
<div id="tool2Container">
	<br>
<b><font color="green">Gene Name: <input id = 'geneName7' type="text" name='name' /> </font></b>ZM*, Maize_refgen4<br>
<p>Try entering the id for glossy1 <i id='gene7' onclick="copyToClipboard('gene7','geneName7')" style="cursor: pointer;color:blue;">Zm00001d000002</i></p>
<p>To select only specific datasets, go to <a href="advanced_GenevsProtein_options_B73v4.php">Advanced options</a></p>
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit! ">
<br>
</div>
<br>
<div id="subheaderContainer">
<p><h3><font color="white">Multi-Gene Expression vs Abundance in a single tissue</font></h3></p>
        </div>

<br>
</form>
<form action="Mutli_ProteinvsGene_chart_B73v4.php" method="post">
<a href="B73 internode_6_7 GeneExpression-B73 internode_6_7 ProteinAbundance.png"><img src="B73 internode_6_7 GeneExpression-B73 internode_6_7 ProteinAbundance.png" class="image1" width=500 /></a><br>
<br>
<div id="tool2Container">
	<br>
<b><font color="green">Select Tissue:</b> <select name="Tissues">
    <option value="Internode 6 and 7"><b>Internode 6 and 7</b></option>
    <option value="Internode 7 and 8"><b>Internode 7 and 8</b></option>
    <option value="Mature pollen"><b>Mature pollen</b></option>
    <option value="Ear Primordium 2-4 mm"><b>Ear Primordium 2-4 mm</b></option>
    <option value="Ear Primordium 6-8 mm"><b>Ear Primordium 6-8 mm</b></option>
    <option value="Embryos 20 DAP"><b>Embryos 20 DAP</b></option>
    <option value="Embryo 38 DAP"><b>Embryo 38 DAP</b></option>
    <option value="Endosperm 12 DAP"><b>Endosperm 12 DAP</b></option>
    <option value="Endosperm Crown 27 DAP"><b>Endosperm Crown 27 DAP</b></option>
    <option value="Female spikelet collected day of silking"><b>Female spikelet collected day of silking</b></option>
    <option value="Germi0 0tin Kernels 2 DAI"><b>Germi0 0tin Kernels 2 DAI</b></option>
    <option value="Leaf Zone 1 (Symmetrical)"><b>Leaf Zone 1 (Symmetrical)</b></option>
    <option value="Leaf Zone 2 (Stomatal)"><b>Leaf Zone 2 (Stomatal)</b></option>
    <option value="Leaf Zone 3 (Growth)"><b>Leaf Zone 3 (Growth)</b></option>
    <option value="Mature Leaf 8"><b>Mature Leaf 8</b></option>
    <option value="Pericarp/Aleurone 27 DAP"><b>Pericarp/Aleurone 27 DAP</b></option>
    <option value="Primary Root 5 Days"><b>Primary Root 5 Days</b></option>
    <option value="Root - Cortex 5 Days"><b>Root - Cortex 5 Days</b></option>
    <option value="Root Elongation Zone 5 Days"><b>Root Elongation Zone 5 Days</b></option>
    <option value="Root Meristem Zone 5 Days"><b>Root Meristem Zone 5 Days</b></option>
    <option value="Secondary Root 7-8 Days"><b>Secondary Root 7-8 Days</b></option> 
    <option value="Silks"><b>Silks</b></option>
    <option value="Vegetative Meristem 16-19 days"><b>Vegetative Meristem 16-19 days</b></option>	
		
  </select>
  <br><br>

<p><b><font color="green">Paste gene IDs for expression vs abundance analysis in the box below. One gene per row.</font></b> <br><i>Try entering the ID for</i> <i id='gene1' onclick="copyToClipboard('gene1','select-this')" style="cursor: pointer;color:blue;">Zm00001d045462</i> <i id='gene2' onclick="copyToClipboard('gene2','select-this')" style="cursor: pointer;color:blue;">Zm00001d045520</i> <i id='gene3' onclick="copyToClipboard('gene3','select-this')" style="cursor: pointer;color:blue;">Zm00001d000005</i></p>

<TEXTAREA id="select-this" NAME=gene_names rows=25 cols=50 wrap=physical>
</TEXTAREA>
<br>
<input type="submit" style="font-face: 'Comic Sans MS'; font-size: larger; color: white; background-color: #cc0000; border: 3pt ridge lightgrey" value=" Submit! ">
<br>
</div>


</body>
</html>
