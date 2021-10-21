<?php
	include("globals.php");

	$productsImport = $db->getAll("select * from tmp_table where col6 = 7");
	
	foreach($productsImport as $k => $v){
			$checkProd = $db->getRow("SELECT * from ".$products_table." WHERE barcode like '".trim($v["col1"])."'");

			//col15 - product type - 
			$product_type_id = $db->getRow("SELECT id FROM {$product_types_table} WHERE name_bg LIKE '".trim($v["col15"])."' AND edate = 0");
			
      $v["col2"] = str_replace("лв.", "", $v["col2"]);
      $v["col2"] = trim(str_replace(",", ".", $v["col2"]));
      $v["col3"] = str_replace("лв.", "", $v["col3"]);
      $v["col3"] = trim(str_replace(",", ".", $v["col3"]));
      $v["col4"] = str_replace("лв.", "", $v["col4"]);
      $v["col4"] = trim(str_replace(",", ".", $v["col4"]));
      $params = $v;
			$fields = array(
                "barcode"       				=> htmlspecialchars(trim($v['col1'])),
                'name_bg'								=> htmlspecialchars(trim($v['col9'])),
                'name_en'								=> htmlspecialchars(trim($v['col8'])),
                'excerpt_bg'            => nl2br(trim($v['col17'])),
                'description_bg'				=> nl2br(trim($v['col18'])),
                'usage_bg'              => nl2br(trim($v['col19'])),
                'ingredients_bg'        => nl2br(trim($v['col20'])),
                'useful_bg'							=> htmlspecialchars(trim($v['col11'])),
                'video_bg'							=> trim($v['col25']),
                "price_supply"          => (float)$v["col2"],
                "price"                 => (float)$v["col4"],
                "price_profit"          => (float)$v["col3"],
                "brand_id"              => (int)$v["col6"],
                "collection_id"         => (int)$v["col7"],
                "product_type_id"       => ((int)$product_type_id["id"]?(int)$product_type_id["id"]:1),
                "weight"                => (float)$v["col24"],
                "last_update"           => $act == "edit" ? time() : NULL,
                'active'				=> 1
            );
/*
            if($act == "add"){
                $fields["postdate"] = time();
                
				shiftPos($db, $products_table);
				$res = $db->autoExecute($products_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
*/
		//print_r($fields);
		if($checkProd["id"]){
			$fields["last_update"] = time();
			$id = $checkProd["id"];
			$res = $db->autoExecute($products_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $checkProd["id"]);safeCheck($res);
		}
		else{
			$fields["postdate"] = time();
			$fields["last_update"] = NULL;
			
			shiftPos($db, $products_table);
			$res = $db->autoExecute($products_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
			$id = mysqli_insert_id($db->connection);
		}

			//col13 - kategoria - ,
			//col15 - podkategoria
			$cats = explode(",", $v["col13"]);
			$cats1 = explode(",", $v["col15"]);
			
			$categoriesRequest = Array();
			foreach($cats as $k1 => $v1){
				$cat = $db->getRow("SELECT id FROM arthabeauty_categories WHERE name_bg LIKE '".trim($v1)."' AND edate = 0");
				if($cat["id"]){
					$categoriesRequest[] = $cat["id"];
					foreach($cats1 as $k2 => $v2){
						$cat1 = $db->getRow("SELECT id FROM arthabeauty_categories WHERE name_bg LIKE '".trim($v2)."' AND category_id = ".$cat["id"]." AND edate = 0");
						if($cat1["id"]){
							$categoriesRequest[] = $cat1["id"];
						}
					}					
				}
			}
/*			
			foreach($cats1 as $k1 => $v1){
				$cat = $db->getRow("SELECT id FROM arthabeauty_categories WHERE name_bg LIKE '".trim($v1)."' AND edate = 0");
				if($cat["id"]){
					$categoriesRequest[] = $cat["id"];
				}
			}
*/			
      $res = $db->Query("DELETE FROM ".$product_to_category_table." WHERE product_id = '{$id}'"); safeCheck($res);
      if ($categoriesRequest){
          foreach($categoriesRequest as $k1 => $v1){
              $res = $db->Query("INSERT INTO ".$product_to_category_table." (product_id, category_id) VALUES ('".$id."','".$v1."')"); safeCheck($res);
          }
      }
			
			
			
			//arthabeauty_attributes_to_attribute_options
			//col10 - wid na produkta
			//col11 - osn. polzi
			//col12 - prednaznachenie
			//col14 - aktivni systavki
			//col16 - tip kosa
			//col21 - predimstva
			//col22 - pol
			//col26 - spf
			
			$attr1 = explode(",", $v["col10"]);
			//$attr2 = explode(",", $v["col11"]);
			$attr3 = explode(",", $v["col12"]);
			$attr4 = explode(",", $v["col14"]);
			$attr5 = explode(",", $v["col16"]);
			$attr6 = explode(",", $v["col21"]);
			$attr7 = explode(",", $v["col22"]);
			$attr8 = explode(",", $v["col26"]);
			
			$arrtibutes = array_merge($attr1, $attr3, $attr4, $attr5, $attr6, $attr7, $attr8);
			$attributesDB = Array();
			foreach($arrtibutes as $k1 => $v1){
				$attr = $db->getRow("SELECT id, attribute_id FROM arthabeauty_attributes_to_attribute_options WHERE option_text_bg LIKE \"".trim($v1)."\" AND edate = 0 AND attribute_id IN (SELECT id from arthabeauty_attributes where edate = 0)");
				print_r($attr);
				if($attr["id"]){
					$attributesDB[] = array($attr["id"], $attr["attribute_id"]);
				}
			}
			$del = $db->Query("DELETE FROM ".$product_to_attribute_option_table." WHERE product_id = '".$id."'"); safeCheck($del);
      foreach($attributesDB as $k1 => $v1){
            $res = $db->Query("INSERT INTO ".$product_to_attribute_option_table." (product_id, attribute_id, attribute_option_id) VALUES ('".$id."','".$v1[1]."','".$v1[0]."')"); safeCheck($res);
      }
						
			//col5 - swyrzani
						
			//col23 - razfasovka
			$optionPack = 4;
			$getPackId = $db->getRow("SELECT id from arthabeauty_options where option_group_id = 4 AND option_text like '".trim($v["col23"])."'");
			
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
      
      //$res = $db->autoExecute($variants_table, $fields_tmp, DB_AUTOQUERY_INSERT); safeCheck($res);
      echo $v['col1'];
      $enc = mb_detect_encoding($v["col1"]);
			$v["col1"] = mb_convert_encoding($v["col1"], "ASCII", $enc);
      $v["col1"] = str_replace('?', '',$v["col1"]);
      echo "../import-images/".trim($v['col1']).".png<br />";
			if(file_exists("../import-images/".trim($v['col1']).".png")){
				echo "img1<br />";
				$pic = copyImageImport("../import-images/".trim($v['col1']).".png", "../files/", "../files/tn/", "../files/tntn/", "250x");
				if(!empty($pic)) {
				
						$fields_pic = array(
											"pic" => $pic,
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

			if(file_exists("../import-images/".trim($v['col1'])."-1.png")){
				//echo "img2";
				$pic = copyImageImport("../import-images/".trim($v['col1'])."-1.png", "../files/", "../files/tn/", "../files/tntn/", "250x");
				if(!empty($pic)) {
						$fields_pic = array(
											"pic" => $pic,
											"product_id" => $id,
											"postdate" => time(),
										);
						shiftPos($db, $products_images_table);
						$res = $db->autoExecute($products_images_table, $fields_pic, DB_AUTOQUERY_INSERT); safeCheck($res);
				}
				
			}
			if(file_exists("../import-images/".trim($v['col1'])."-2.png")){
				$pic = copyImageImport("../import-images/".trim($v['col1'])."-2.png", "../files/", "../files/tn/", "../files/tntn/", "250x");
				if(!empty($pic)) {
				
						$fields_pic = array(
											"pic" => $pic,
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