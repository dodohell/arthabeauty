<?php
	include("globals.php");
	
	$id = $params->getInt("id");
	$product_id = $params->getInt("product_id");
	$act = $params->getString("act");
	$file = $params->getString("file");
	
	$products = new Products();
	if ( $act == "add" ){
		$products->postFile($file, $product_id);
	}
	
	if ( $act == "delete" ){
		$products->deleteFile($id);
	}
	
	if ( $act == "updateFile" ){
		$products->updateFile($params);
	}
    
	if ( $act == "getFileForm" ){
		$products->getFileForm($id);
	}
	
	if ( $act == "show" ){
		$content = $products->getFiles($product_id);
		echo json_encode($content);
	}
	
?>