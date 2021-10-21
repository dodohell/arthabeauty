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
	$sm->assign("page_heading", $page_heading);
	
	$products = new Products();
	
	if ( $act == "clone" ){
		$products->cloneProduct($id);
		header("Location: ".$php_self);
		die();
	}
    
	if ( $act == "delete" ){
		$products->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
    
    if ( $params->has("changePrices") ){
		$products_prices = $params->get("products_prices");
		$products_ids = $params->get("products_ids");
		
		foreach($products_prices as $k => $v){
			if ( (double)$v > 0.0 ){
				$price = (double)$v;
				$productId = $products_ids[$k];
                
                Products::updateField($productId, "price", $price);
			}
		}
	}
	
	$items = $products->getProducts($params);
	$sm->assign("items", $items);
	
	$brands = Brands::getBrandsAll();
    $sm->assign("brands", $brands);
    $sm->assign("brand_id", $params->getInt('brand_id'));
	
	
	$sm->display("admin/products.html");
?>