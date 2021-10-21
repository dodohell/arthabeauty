<?php
	include("globals.php");

	$productsImport = $db->getAll("select * from tmp_table");
	
	foreach($productsImport as $k => $v){
		
		$checkProd = $db->getRow("SELECT * from ".$products_table." WHERE barcode like '".trim($v["col1"])."'");
		
		$clearRel = $db->Query("DELETE FROM arthabeauty_product_to_product_similar where main_product_id = '".$checkProd["id"]."'");
		
		$tmp = nl2br($v["col5"]);
		$relImport1 = explode("<br />", $tmp);
		
		foreach($relImport1 as $k1 => $v1){
			$relImport = explode(",", $v["col5"]);			
			foreach($relImport as $k2 => $v2){
				$checkProd1 = $db->getRow("SELECT id from ".$products_table." WHERE barcode like '".trim($v2)."'");
				if((int)$checkProd1["id"] > 0){
					$res = $db->query("INSERT INTO arthabeauty_product_to_product_similar (main_product_id, product_id) VALUES (".(int)$checkProd["id"].", ".(int)$checkProd1["id"].")" );
					print_r($res);
				}
			}
		}
		/*
		$relImport = explode(",", $v["col5"]);
		
		foreach($relImport as $k1 => $v1){
			$tmp = nl2br($v1);
			
			foreach($relImport1 as $k2 => $v2){
				echo trim($v2)."<br />";
			}
		}
		*/
		//$clearRel = $db->Query("DELETE FROM arthabeauty_product_to_product where main_product_id = '".$checkProd["id"]."'");
	}
	echo count($productsImport);
?>