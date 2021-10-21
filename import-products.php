<?php
	include("globals.php");

	$productsImport = $db->getAll("select * from tmp_table where col6 = 1");
	echo "<pre>";
	print_r($productsImport);
	echo "</pre>";
?>