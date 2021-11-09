<?php
header("Content-type: text/csv charset=UTF-8");
header("Content-Disposition: attachment; filename=".(time()+331122).".csv");
header("Content-Transfer-Encoding: UTF-8");
header("Pragma: no-cache");
header("Expires: 0");
	include("globalsXml.php");

	$lng = "bg";
         
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			
//            $code = $params->getString("code");
//            $sm->assign("code", $code);
             
      $sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();

            $sqlItems = "SELECT DISTINCT
                            products.*,
                            products.name_{$lng} AS name,
                            products.htaccess_url_{$lng} AS htaccess_url,
                            products.excerpt_{$lng} AS excerpt,
                            (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE brands.edate = 0 AND products.brand_id = brands.id) AS brand_name,
                            (SELECT pi.pic FROM " . $products_images_table . " AS pi WHERE pi.product_id = products.id AND pi.edate = 0 ORDER BY pi.pos LIMIT 1) as mainPic,
                            products.htaccess_url_{$lng} AS htaccess_url
                        FROM 
                            ".$products_table." AS products
                        WHERE
                            products.edate = 0
                        AND products.active = 1
                        AND products.feed_exclude = 0
                        AND products.quantity > 0
                        {$sql_order_by}
                        
                        ";


            $items = $db->getAll($sqlItems); safeCheck($items);
			echo "id,title,description,availability,condition,price,link,image_link,brand,additional_image_link,google_product_category,product_type,sale_price\n";

      $helpers = new Helpers();
      $user_group_id = Helpers::getCurentUserGroupId();

			foreach($items as $k => $v){
				 	$price_specialoffer = getSpecialOfferPrice($v["id"], $v["brand_id"], 1);
				 
					if ( $price_specialoffer["price_specialoffer"] > 0.0 ){
		          $v["price"] = $helpers->getDiscountedPrice($v["price"], 1, $user_group_id);
		          $v["price_specialoffer"] = $helpers->getDiscountedPrice($price_specialoffer["price_specialoffer"], 1, $user_group_id);
		          $v["price_specialoffer_text"] = $price_specialoffer["price_specialoffer_text"];
		          $v["bonus_points"] = $price_specialoffer["bonus_points"];
		          $v["bonus_points_win"] = round($price_specialoffer["price_specialoffer"] * 1, 0) ;
		          $v["discount_date_to"] = $price_specialoffer["discount_date_to"];
		      }else{
		          $v["price"] = $helpers->getDiscountedPrice($v["price"], 0, $user_group_id);
		          $v["bonus_points_win"] = round($v["price"] * 1, 0);
		      }
		      
		      $images = $db->getAll("SELECT pi.pic FROM " . $products_images_table . " AS pi WHERE pi.product_id = ".$v["id"]." AND pi.edate = 0 ORDER BY pi.pos");
		      
		      $imgs = Array();
		      foreach($images as $kk => $vv){
		      	$imgs[] = $host."files/".$vv["pic"];
		      }
		      
		      $category = $db->getRow("SELECT *, (SELECT categories.name_{$lng} AS name FROM ".$categories_table." AS categories WHERE ptc.category_id = categories.id) AS name FROM ".$product_to_category_table." AS ptc WHERE ptc.product_id = ".$v["id"]." AND category_id IN (SELECT id FROM ".$categories_table." WHERE category_id = 0 AND edate = 0) ORDER BY ptc.category_id"); safeCheck($category);
            
           $catString = ""; 
            
           if ($category["category_id"]){
            	
                $categoryMain = $db->getRow("SELECT id, name_{$lng} AS name, htaccess_url_{$lng} AS htaccess_url FROM ".$categories_table." WHERE edate = 0 AND id = '".$category["category_id"]."'"); safeCheck($categoryMain);
                
                $catString = $categoryMain["name"];
                
		            $subCategory = $db->getRow("SELECT *, (SELECT categories.name_{$lng} AS name FROM ".$categories_table." AS categories WHERE ptc.category_id = categories.id) AS name,  (SELECT categories.htaccess_url_{$lng} AS htaccess_url FROM ".$categories_table." AS categories WHERE ptc.category_id = categories.id) AS htaccess_url FROM ".$product_to_category_table." AS ptc WHERE ptc.product_id = ".$v["id"]." AND category_id IN (SELECT id FROM {$categories_table} WHERE edate = 0 and active = 1 AND category_id = ".$category["category_id"]." ) ORDER BY ptc.category_id"); safeCheck($category);
		            $sm->assign("subCategory", $subCategory);
		            
		            if($subCategory["category_id"]){
				          $catString .= " > ".$subCategory["name"];
		            }
		            
                
            }
		    
					echo maybeEncodeCSVField($v["barcode"]).",".
								maybeEncodeCSVField($v["name_en"].", ".$v["name"]).",".
								maybeEncodeCSVField($v["excerpt"]).",".
								maybeEncodeCSVField("in stock").",".
								maybeEncodeCSVField("new").",".
								maybeEncodeCSVField($v["price"]." BGN").",".
								maybeEncodeCSVField(substr($host,0,-1).($v["htaccess_url"] ? $v["htaccess_url"]: $htaccessVars["htaccess_product"]."/".$v["id"])).",".
								maybeEncodeCSVField(substr($host,0,-1)."/files/".$v["mainPic"]).",".
								maybeEncodeCSVField($v["brand_name"]).",".
								maybeEncodeCSVField(implode(",",$imgs)).",".
								//maybeEncodeCSVField($catString).",".
								maybeEncodeCSVField("Health & Beauty > Personal Care").",".
								maybeEncodeCSVField($catString).",".
								($v["price_specialoffer"] > 0 ? maybeEncodeCSVField($v["price_specialoffer"]." BGN") : "")."\n"
					;
			}



	function maybeEncodeCSVField($string) {
		$string = str_replace("&nbsp;", " ", $string);
		$string = str_replace("&amp;", " ", $string);
        $string = str_replace("&#039;", "'", $string);
    if(strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
        $string = '"' . str_replace('"', '""', $string) . '"';
    }
    return $string;
	}
?>