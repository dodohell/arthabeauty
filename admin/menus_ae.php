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
	$settings = new Settings();

	if( isset($_REQUEST["Submit"]) ){
		$menus->addEditRow($_REQUEST);
		header("Location: menus.php?menu_pos=$menu_pos");
		exit;
	}
	if ( $id ){
		$row = $menus->getRecord($id);
		$sm->assign("row", $row);
	}
	if ( $act == "delete" ){
		$field = $_REQUEST["field"];
		$menus->deleteField($id, $field);
		header("Location: menus_ae.php?id=$id&act=edit&menu_pos=$menu_pos&id_menu=".$row["id_menu"]);
		die();
	}

	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);

	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
	$sm->assign("editorExcerpt", $editorExcerpt);

	$sm->assign("open_menu", 1);

	$sm->display("admin/menus_ae.html");
?>
