<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = (int)$_REQUEST["id"];
	$info_id = (int)$_REQUEST["info_id"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$file = htmlspecialchars(trim($_REQUEST['file']), ENT_QUOTES);
	
	$menus = new Menus();
	
	if ( $act == "add" ){
		$menus->postImage($file, $info_id);
	}
	
	if ( $act == "delete" ){
		$menus->deleteImage($id);
	}
	if ( $act == "getImageForm" ){
		$menus->getImageForm($id);
	}
	
	if ( $act == "updateImage" ){
		$menus->updateImage($_REQUEST);
	}
	
	if ( $act == "show" ){
		$content = $menus->getImages($info_id);
		echo json_encode($content);
	}
	
?>