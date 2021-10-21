<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$product_id = $params->getInt("product_id");
	$act = $params->getString("act");
	$page_heading = $configVars["products_comments"];
    
    $page = $params->getInt("page");
    $limit = 50;
	
	$php_self = "products_comments.php";
	$php_edit = "products_comments_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("product_id", $product_id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$products_comments = new Products();
	
	if ( $act == "delete" ){
		$products_comments->deleteProductCommentRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $products_comments->getProductsCommentsPage($page, $limit, $params);
	$sm->assign("items", $items);
	$sm->assign("pagination", $products_comments->pagination_comments);
    
	$sm->display("admin/products_comments.html");
?>