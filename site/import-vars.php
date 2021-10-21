<?php
	include("globals.php");

	$productsImport = $db->getAll("select * from tmp_table");
	
	foreach($productsImport as $k => $v){
			$checkProd = $db->getRow("SELECT * from ".$products_table." WHERE barcode like '".trim($v["col1"])."'");
			
						
			//col23 - razfasovka
			$optionPack = 4;
			$getPackId = $db->getRow("SELECT id from arthabeauty_options where option_group_id = 4 AND option_text like '".trim($v["col23"])."'");
			//print_r($getPackId);
			if($getPackId["id"]){
				$checkHasVar = $db->getRow("SELECT * FROM {$variants_table} where product_id = ".$checkProd["id"]." AND option_group_id = 4 AND option_id = ".$getPackId["id"]);
				if($checkHasVar["id"]){
					//echo $checkProd["id"]." ~ ".$checkProd["barcode"]." ~ ".trim($v["col23"])." ~ ";
					//echo "has var";
					//echo "<br />";
				}
				else{
/*					
					      $fields_tmp = array(
                          "product_id" => $checkProd["id"],
                          "option_group_id" => 4,
                          "option_id" => $getPackId["id"],
                          "price_supply" => (float)$v["col2"],
                					"price"        => (float)$v["col4"],
                          "bonus_points" => 0,
                          "weight" => (float)$v["col24"],
                          "quantity" => 0,
                          "is_default" => 1,
                          "edate" => 0
                      );
      
      					$res = $db->autoExecute($variants_table, $fields_tmp, DB_AUTOQUERY_INSERT); safeCheck($res);
      					echo "inserted";
*/
				}
			}
			else{

				$checkHasVarNew = $db->getRow("SELECT * FROM {$variants_table} where product_id = ".$checkProd["id"]." AND option_group_id = 4 AND option_id = ".(int)$getPackIdNEW["id"]);
				if($checkHasVarNew["id"]){
					/*
					      $fields_tmp = array(
                          "product_id" => $checkProd["id"],
                          "option_group_id" => 4,
                          "option_id" => $getPackIdNEW["id"],
                          "price_supply" => (float)$v["col2"],
                					"price"        => (float)$v["col4"],
                          "bonus_points" => 0,
                          "weight" => (float)$v["col24"],
                          "quantity" => 0,
                          "is_default" => 1,
                          "edate" => 0
                      );
      
      					$res = $db->autoExecute($variants_table, $fields_tmp, DB_AUTOQUERY_INSERT); safeCheck($res);
      					echo "inserted";
      		*/
				}
				else{
					$checkHasVarNew1 = $db->getAll("SELECT * FROM {$variants_table} where product_id = ".$checkProd["id"]." AND option_group_id = 4 ");
					print_r($checkHasVarNew1);
					echo "<br />";
					echo $checkProd["id"]." ~ ".$checkProd["barcode"]." ~".trim($v["col23"])."~".str_replace("ml", " ml", trim($v["col23"]))."~ ".$getPackIdNEW["id"]." ~ ";
					echo "no var";
					echo "<br />";
					
					      $fields_tmp = array(
                          "product_id" => $checkProd["id"],
                          "option_group_id" => 4,
                          "option_id" => $getPackIdNEW["id"],
                          "price_supply" => (float)$v["col2"],
                					"price"        => (float)$v["col4"],
                          "bonus_points" => 0,
                          "weight" => (float)$v["col24"],
                          "quantity" => 0,
                          "is_default" => 1,
                          "edate" => 0
                      );
                      print_r($fields_tmp);
                      echo "<br />";
                      echo "<br />";
					
				}
			}
			//$variants_table
			
			
/*			
      $fields_tmp = array(
                          "product_id" => $id,
                          "option_group_id" => 4,
                          "option_id" => $getPackId["id"],
                          "price_supply" => (float)$v["col2"],
                					"price"        => (float)$v["col4"],
                          "bonus_points" => 0,
                          "weight" => (float)$v["col24"],
                          "quantity" => 0,
                          "is_default" => 1,
                          "edate" => 0
                      );
      
      $res = $db->autoExecute($variants_table, $fields_tmp, DB_AUTOQUERY_INSERT); safeCheck($res);
*/
			//echo "<br />";
	}
	echo count($productsImport);
?>