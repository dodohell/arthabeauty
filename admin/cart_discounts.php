<?php
	include "./globals.php";
    
	/*************** Basic Data ***************/
	$act	= $params->getString("act");
	$id		= $params->getInt("id");
    $menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["cart_discounts"];
    
	$sm->assign("php_self","cart_discounts.php");
	$sm->assign("php_edit","cart_discounts_ae.php");
    $sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	/******************* Delete Element ****************/
	if($act == "delete") {
		CartDiscounts::deleteRecord($id);
	}
    
	/***************** Delete Checked Elements ************/
	if($act == "mDelete") {
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			CartDiscounts::deleteRecord($value);
		}
	}
    
    $name = $params->getString("name");
    $status = $params->getInt("status");
    
	$items = CartDiscounts::getCartDiscounts($name, $status);
    
	$sm->assign("name",$name);
	$sm->assign("items", $items);
	
	$sm->display("./admin/cart_discounts.html");
    