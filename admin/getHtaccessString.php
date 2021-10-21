<?php
	include("globals.php");
	
	
	
	$string = $_REQUEST['string'];
	
	echo Helpers::generateURL($string);
?>