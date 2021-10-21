<?php
	include("globals.php");
	
	$id = $params->getInt("id");
	$news_id = $params->getInt("news_id");
	$act = $params->getString("act");
	$file = $params->getString("file");
	
	$news = new News();
	if ( $act == "add" ){
		$news->postImage($file, $news_id);
	}
	
	if ( $act == "delete" ){
		$news->deleteImage($id);
	}
    
	if ( $act == "getImageForm" ){
		$news->getImageForm($id);
	}
	
	if ( $act == "updateImage" ){
		$news->updateImage($params);
	}
    
	if ( $act == "show" ){
		$content = $news->getImages($news_id);
		echo json_encode($content);
	}
	
?>