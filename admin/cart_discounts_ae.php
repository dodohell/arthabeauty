<?php
	include "./globals.php";

	/***************** Basic Data **************/
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["cart_discounts"];
    
    $php_self = "cart_discounts.php";
	$php_edit = "cart_discounts_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
    
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	/****************** Save Data *****************/
	if($params->has("Submit") || $params->has("SaveAndStay")) {
		CartDiscounts::addEditRow($params);
		if($params->has("Submit")){
			header("Location: $php_self");
		}else{
			header("Location: $php_edit?act=edit&id=$id");
		}
		die();
	}

	/******************* Get Data If Id Is Not Empty ***************/
    $row = NULL;
    $sm->assign("row", $row);
	if($id) {
		$row = CartDiscounts::getRecord($id);
		$sm->assign("row", $row);
	}

	$sm->display("./admin/cart_discounts_ae.html");
?>