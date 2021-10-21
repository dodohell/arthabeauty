<?php
	include("globals.php");

	$productsImport = $db->getAll("select * from ".$products_table." where edate = 0 AND brand_id = 2");
	
	foreach($productsImport as $k => $v){
			
			$id = $v["id"];
      $enc = mb_detect_encoding($v["barcode"]);
			$v["barcode"] = mb_convert_encoding($v["barcode"], "ASCII", $enc);
      $v["barcode"] = str_replace('?', '',$v["barcode"]);
      echo "../import-images/".trim($v['barcode']).".png<br />";
			if(file_exists("../import-images/".trim($v['barcode']).".png")){
				echo "img1<br />";
				$pic = copyImageImport("../import-images/".trim($v['barcode']).".png", "../files/", "../files/tn/", "../files/tntn/", "250x");
				if(!empty($pic)) {
				
						$fields_pic = array(
											"pic" => $pic,
											"pos" => 0,
											"product_id" => $id,
											"postdate" => time(),
										);
						shiftPos($db, $products_images_table);
						$res = $db->autoExecute($products_images_table, $fields_pic, DB_AUTOQUERY_INSERT); safeCheck($res);
				}
				
			}
			else{
				//echo "not existing image";
				
			}

			if(file_exists("../import-images/".trim($v['barcode'])."-1.png")){
				//echo "img2";
				$pic = copyImageImport("../import-images/".trim($v['barcode'])."-1.png", "../files/", "../files/tn/", "../files/tntn/", "250x");
				if(!empty($pic)) {
						$fields_pic = array(
											"pic" => $pic,
											"pos"	=> 1,
											"product_id" => $id,
											"postdate" => time(),
										);
						shiftPos($db, $products_images_table);
						$res = $db->autoExecute($products_images_table, $fields_pic, DB_AUTOQUERY_INSERT); safeCheck($res);
				}
				
			}
			if(file_exists("../import-images/".trim($v['barcode'])."-2.png")){
				$pic = copyImageImport("../import-images/".trim($v['barcode'])."-2.png", "../files/", "../files/tn/", "../files/tntn/", "250x");
				if(!empty($pic)) {
				
						$fields_pic = array(
											"pic" => $pic,
											"pos" => 2,
											"product_id" => $id,
											"postdate" => time(),
										);
						shiftPos($db, $products_images_table);
						$res = $db->autoExecute($products_images_table, $fields_pic, DB_AUTOQUERY_INSERT); safeCheck($res);
				}
				
			}      
	}
	echo count($productsImport);
?>