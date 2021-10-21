<?php
	include("globals.php");
    
    $fileName = $_REQUEST["file_name"];

	Helpers::showPdf($install_path."files/econt/bill_of_ladings/", $fileName);
?>