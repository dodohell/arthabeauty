<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$tag = $_REQUEST["tag"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$page_heading = $configVars["common"];
	
	$php_self = "common.php";
	$php_edit = "common_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("tag", $tag);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);

	$common = new Common();
	$settings = new Settings();
	
	if( isset($_REQUEST["Submit"]) ){
		$common->addEditRow($_REQUEST);
		header("Location: common.php");
		die();
	}
	
	if ( $tag ){
		$row = $common->getRecord($tag);
		foreach($languages as $k => $v){
			$v["value"] = $row["description_".strtolower($v["short"])];
			$v["short_use"] = strtolower($v["short"]);
			$languages[$k] = $v;
		}
		$sm->assign("languages", $languages);
	}
	
	$sm->assign("row", $row);
	
	
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
	$sm->assign("editorExcerpt", $editorExcerpt);
	
	
	$sm->display("admin/common_ae.html");
?>