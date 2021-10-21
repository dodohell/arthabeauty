<?php
	include("globals.php");
	
	//$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["products"];
	
	$php_self = "products.php";
	$php_edit = "products_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	$products = new products();
	$settings = new Settings();
	
	if( $params->has("copy_variants") ){
        $copy_from_id = $params->getInt("copy_variants_from_product_id");
        $copy_to_id = $params->getInt("copy_variants_to_product_id");
        
		$products->copyVariants($copy_from_id, $copy_to_id);
        
		header("Location: products_ae.php?id=$id&act=edit#variantsCont");
		die();
	}
    
	if( $params->has("copy_related_products") ){
        $copy_from_id = $params->getInt("copy_related_products_from_product_id");
        $copy_to_id = $params->getInt("copy_related_products_to_product_id");
        
		$products->copyRelatedProducts($copy_from_id, $copy_to_id);
        
		header("Location: products_ae.php?id=$id&act=edit#related_productsCont");
		die();
	}
    
	if( $params->has("Submit") ){
		$products->addEditRow($params);
		header("Location: products.php");
		die();
	}
	if( $params->has("SaveAndStay") ){
		$id = $products->addEditRow($params);
        //$row = $products->getRecord($id);
		header("Location: products_ae.php?id=$id&act=edit");
		die();
	}
	if ( $id ){
		$row = $products->getRecord($id);
		$sm->assign("row", $row);
	}

	if ( $act == "delete_var_field" ){
		$var_id = $params->getInt("var_id");
		$field = $_REQUEST["field"];
		$products->deleteImageVariant($var_id, $field);
		header("Location: products_ae.php?id=$id&act=edit");
		die();
	}

	if ( $act == "delete" ){
		$field = $params->getString("field");
		$products->deleteField($id, $field);
		header("Location: products_ae.php?id=$id&act=edit");
		die();
	}
    
    $productTypesObj = new ProductTypes();
    $product_types = $productTypesObj->getProductTypesAllActive();
    $sm->assign("product_types", $product_types);
    
    $categoriesObj = new Categories();
    $items = $categoriesObj->getCategories($id, 1);
    
    $sm->assign("items", $items);
    
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
	$sm->assign("editorExcerpt", $editorExcerpt);
    
	$editorUsage = $settings->generateEditor($languages,"usage", $row);
	$sm->assign("editorUsage", $editorUsage);
    
	$editorIngredients = $settings->generateEditor($languages,"ingredients", $row);
	$sm->assign("editorIngredients", $editorIngredients);
    
	$editorVideo = $settings->generateEditor($languages,"video", $row);
	$sm->assign("editorVideo", $editorVideo);
	
//	$editorForOffer = $settings->generateEditor($languages,"for_offer", $row);
//	$sm->assign("editorForOffer", $editorForOffer);
	
    $optionGroupsObj = new OptionGroups();
    $option_groups = $optionGroupsObj->getProductOptionGroupsSelectedOrNot($id);
    $sm->assign("option_groups", $option_groups);
    
    $brands = Brands::getBrandsAll();
    $sm->assign("brands", $brands);
    
    $colorsObj = new Colors();
    $colors = $colorsObj->getColors(1);
    $sm->assign("colors", $colors);
    
    
	$sm->display("admin/products_ae.html");
?>