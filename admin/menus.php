<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = (int)$_REQUEST["id"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$menu_pos = htmlspecialchars(trim($_REQUEST['menu_pos']), ENT_QUOTES);
	$page_heading = $configVars["menus"];
	
	$php_self = "menus.php";
	$php_edit = "menus_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
    $menu_pos_key = array_search($menu_pos, array_column($menu_posIDS, 'menu_pos'));
    $menu_name = $menu_posIDS[$menu_pos_key]["name"];
	$sm->assign("menu_name", $menu_name);
	$sm->assign("page_heading", $page_heading);
	
	$menus = new Menus();
	
    if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$menus->deleteRecord($value);
		}
	}
    
	if ( $act == "delete" ){
		$menus->deleteRecord($id);
		header("Location: ".$php_self."?menu_pos=$menu_pos");
		die();
	}
	
	$items = $menus->getMenuPosition($menu_pos);
	$sm->assign("items", $items);
	
	
	$sm->assign("open_menu", 1);
	
	$sm->display("admin/menus.html");
?>