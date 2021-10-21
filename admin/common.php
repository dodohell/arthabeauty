<?php
	include("globals.php");
	
	$id = (int)$_REQUEST["id"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$menu_pos = htmlspecialchars(trim($_REQUEST['menu_pos']), ENT_QUOTES);
	$page_heading = $configVars["common"];
	
	$php_self = "common.php";
	$php_edit = "common_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
	
	
	$common = new Common();
	$items = $common->getCommon();
	$sm->assign("items", $items);
	
	
	
	
	$sm->display("admin/common.html");
?>