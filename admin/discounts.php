<?php
	include "./globals.php";
    
	/*************** Basic Data ***************/
	$act	= $params->getString("act");
	$id		= $params->getInt("id");
    $menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["discounts"];
    
	$sm->assign("php_self","discounts.php");
	$sm->assign("php_edit","discounts_ae.php");
    $sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	/******************* Delete Element ****************/
	if($act == "delete") {
		Discounts::deleteRecord($id);
	}
    
	/***************** Delete Checked Elements ************/
	if($act == "mDelete") {
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			Discounts::deleteRecord($value);
		}
	}
    
    $name = $params->getString("name");
    $status = $params->getInt("status");
    $discount_date_from = $params->getString("discount_date_from");
    $discount_date_to = $params->getString("discount_date_to");
    
	$items = Discounts::getDiscounts($name, $status, $discount_date_from, $discount_date_to);
    
	$sm->assign("name",$name);
	$sm->assign("items", $items);
	
	$sm->display("./admin/discounts.html");
    