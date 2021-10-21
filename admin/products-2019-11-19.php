<?php
	include("globals.php");
	
	//$settingsObj->checkLogin();
	
	if($params->getInt('brand_id')){// && $params->getString('Filter')
		$_SESSION["brand_id"] = $params->getInt('brand_id');
	}
	else{
		
	}
	if($params->getInt('collection_id')){
		$_SESSION["collection_id"] = $params->getInt('collection_id');
	}
	
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
  $page = $params->getInt("page");
    
	$page_heading = $configVars["products"];
    
    $limit = 50;
	
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

	if ( $act == "delete" ){
		$ids = $params->get("ids");
		foreach($ids as $k => $v){
			$products->deleteRecord($v);
		}
		header("Location: ".$php_self);
		die();
	}
    
    if ( $params->has("changePrices") ){
		$products_prices = $params->get("products_prices");
		$products_prices_supply = $params->get("products_prices_supply");
		$products_prices_profit = $params->get("products_prices_profit");
		$products_quantity = $params->get("products_quantity");
		$products_ids = $params->get("products_ids");
		
		foreach($products_prices as $k => $v){
			if ( (double)$v > 0.0 ){
				$price = (double)$v;
				$productId = $products_ids[$k];
                
                Products::updateField($productId, "price", $price);
			}
		}
		foreach($products_prices_supply as $k => $v){
			if ( (double)$v > 0.0 ){
				$price = (double)$v;
				$productId = $products_ids[$k];
                
                Products::updateField($productId, "price_supply", $price);
			}
		}
		foreach($products_prices_profit as $k => $v){
			if ( (double)$v > 0.0 ){
				$price = (double)$v;
				$productId = $products_ids[$k];
                
                Products::updateField($productId, "price_profit", $price);
			}
		}
		foreach($products_quantity as $k => $v){
			if ( (double)$v > 0.0 ){
				$price = (double)$v;
				$productId = $products_ids[$k];
                
                Products::updateField($productId, "quantity", $price);
			}
		}		
	}
	
	$items = $products->getProductsPage($page, $limit, $params);
    $pagination = $products->getProductsPagination();
    
	$sm->assign("items", $items);
    $sm->assign("pagination", $pagination);
	
	$brands = Brands::getBrandsAll();
    $sm->assign("brands", $brands);
    $sm->assign("brand_id", $params->getInt('brand_id'));
	
	
	$sm->display("admin/products.html");
?>