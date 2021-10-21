<?php
	include "./globals.php";

	/***************** Basic Data **************/
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["promo_codes"];
    
    $php_self = "promo_codes.php";
	$php_edit = "promo_codes_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
    
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	/****************** Save Data *****************/
	if($params->has("Submit") || $params->has("SaveAndStay")) {
		$id = PromoCodes::addEditRow($params);
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
		$row = PromoCodes::getRecord($id);
		$sm->assign("row", $row);
	}

	$brands = PromoCodes::getPromoCodeBrands($id);
	$sm->assign("brands", $brands);

	$categories = PromoCodes::getPromoCodeCategories($id);
	$sm->assign("items", $categories);

	$sm->display("./admin/promo_codes_ae.html");
?>