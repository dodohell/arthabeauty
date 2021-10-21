<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
//	$id = (int)$_REQUEST["id"];
//	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$page_heading = $configVars["out_of_stock"];
	
	$php_self = "out_of_stock.php";
	$php_edit = "products_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	//$sm->assign("id", $id);
	//$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$products = new Products();
    
    if ( $params->has("changeQuantities") ){
		
		$products_quantity = $params->get("products_quantity");
		$variants_quantity = $params->get("variants_quantity");
		$products_ids = $params->get("products_ids");
		$variants_ids = $params->get("variants_ids");
        
		foreach($products_quantity as $k => $v){
            $quantity = (int)$v;
            $productId = $products_ids[$k];

            Products::updateField((int)$productId, "quantity", $quantity);
		}
		foreach($variants_quantity as $k => $v){
            $quantity = (int)$v;
            $variantId = $variants_ids[$k];

            Products::updateVariantField((int)$variantId, "quantity", $quantity);
		}
        header("Location: ".$php_self);
		die();
	}
    
	$items = $products->getOutOfStockProducts();
	$sm->assign("items", $items);
	$sm->display("admin/out_of_stock.html");
?>