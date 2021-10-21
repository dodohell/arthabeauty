<?php
	include("globals.php");
	
	$id = $params->getInt("id");
	$product_id = $params->getInt("product_id");
	$act = $params->getString("act");
	$file = $params->getString("file");
	
	$products = new Products();
	if ( $act == "add" ){
		$products->postImage($file, $product_id);
	}
	
	if ( $act == "delete" ){
		$products->deleteImage($id);
	}
    
    if ( $act == "getImageForm" ){
		$products->getImageForm($id);
	}
	
	if ( $act == "updateImage" ){
		$products->updateImage($params);
	}
	
	if ( $act == "show" ){
		$content = $products->getImages($product_id);
		echo json_encode($content);
	}
	
?>