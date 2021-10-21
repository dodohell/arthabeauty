<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$words = new Words();
	
	$files = $words->getLngFiles();
	$sm->assign("files", $files);
	
	if( isset($_REQUEST["Submit"]) ) {
		$words->backupFiles($files);
		$words->saveChanges($_REQUEST);
		
		header("Location: words.php");
		die();
	}
	
	
	$langs = $words->getContent();
	$sm->assign("langs", $langs);
	
	
	$sm->display("admin/words.html");
?>