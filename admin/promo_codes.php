<?php
	include "./globals.php";
    
	/*************** Basic Data ***************/
	$act        = $params->getString("act");
	$id         = $params->getInt("id");
    $menu_pos   = $params->getString("menu_pos");
	$page_heading = $configVars["promo_codes"];
    
	$sm->assign("php_self","promo_codes.php");
	$sm->assign("php_edit","promo_codes_ae.php");
    $sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	/******************* Delete Element ****************/
	if($act == "delete") {
		PromoCodes::deleteRecord($id);
	}
    
	/***************** Delete Checked Elements ************/
	if($act == "mDelete") {
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			PromoCodes::deleteRecord($value);
		}
	}
    
    $name = $params->getString("name");
    $status = $params->getInt("status");
    
	$items = PromoCodes::getPromoCodes($name, $status);
    
	$sm->assign("name",$name);
	$sm->assign("items", $items);
	
	$sm->display("./admin/promo_codes.html");
    