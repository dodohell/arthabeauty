<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["product_types"];
	
	$php_self = "product_types.php";
	$php_edit = "product_types_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);

	$product_types = new ProductTypes();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$product_types->addEditRow($params);
		header("Location: product_types.php");
		die();
	}
    if( $params->has("SaveAndStay") ){
		$id = $product_types->addEditRow($params);
		header("Location: product_types_ae.php?id=$id&act=edit");
		die();
	}
	if ( $id ){
        $attributesObj = new Attributes();
        $attributes = $attributesObj->getProductTypeAttributesSelectedOrNot($id);
        
        $sm->assign("attributes", $attributes);
        
		$row = $product_types->getRecord($id);
		$sm->assign("row", $row);
	}
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$product_types->deleteField($id, $field);
		header("Location: product_types_ae.php?id=$id&act=edit");
		die();
	}
	
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
	$sm->assign("editorExcerpt", $editorExcerpt);
	
	$sm->display("admin/product_types_ae.html");
?>