<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = (int)$_REQUEST["id"];
	$info_id = (int)$_REQUEST["info_id"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$file = htmlspecialchars(trim($_REQUEST['file']), ENT_QUOTES);
	
	$menus = new Menus();
	if ( $act == "add" ){
		$menus->postFile($file, $info_id);
	}
	
	if ( $act == "delete" ){
		$menus->deleteFile($id);
	}
    
    if ( $act == "getFileForm" ){
		$menus->getFileForm($id);
	}
    
    if ( $act == "updateFile" ){
		$menus->updateFile($_REQUEST);
	}
	
	if ( $act == "show" ){
		$content = $menus->getFiles($info_id);
		echo json_encode($content);
	}
	
?>