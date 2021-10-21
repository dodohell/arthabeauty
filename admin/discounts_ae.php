<?php
	include "./globals.php";

	/***************** Basic Data **************/
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["discounts"];
    
    $php_self = "discounts.php";
	$php_edit = "discounts_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
    
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	/****************** Save Data *****************/
	if($params->has("Submit") || $params->has("SaveAndStay")) {
		$id = Discounts::addEditRow($params);
		if($params->has("Submit")){
			header("Location: $php_self");
		}else{
			header("Location: $php_edit?act=edit&id=$id");
		}
		die();
	}

    $discounts = new Discounts();
    if ( $act == "delete" ){
		$field = $params->getString("field");
		$discounts->deleteField($id, $field);
        header("Location: {$php_edit}?id={$id}&act=edit");
		die();
	}

	/******************* Get Data If Id Is Not Empty ***************/
    $row = NULL;
    $sm->assign("row", $row);
	if($id) {
		$row = Discounts::getRecord($id);
		$sm->assign("row", $row);
	}
		
//	$editor = generateEditor($languages,"description", $row);
//	$sm->assign("editor", $editor);
	
    $products = Discounts::getDiscountProducts($id);
	$sm->assign("products", $products);
	
    $collections = Discounts::getDiscountBrands($id);
	$sm->assign("collections", $collections);
    
    $brands = Discounts::getDiscountBrands($id);
	$sm->assign("brands", $brands);
	
	$categories = Discounts::getDiscountCategories($id);
	$sm->assign("items", $categories);
	
	$sm->display("./admin/discounts_ae.html");
?>