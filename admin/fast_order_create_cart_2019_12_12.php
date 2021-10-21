<?php
	include "./globals.php";
	//include("./classes/class.econt.php");
    
	/***************** Basic Data **************/
	$fast_order_id = $params->getInt("fast_order_id");
	$act = $params->getString("act");

	if ( $act == "create_order" ){
		$row = FastOrders::getRecord($fast_order_id);
		
        $names = explode(" ", trim($row["name"]));
		for ( $i = 0 ; $i < sizeof($names)-1; $i++ ){
			$delivery_first_name .= $names[$i]." ";
		}
		$delivery_first_name = trim($delivery_first_name);
		$delivery_last_name = trim($names[sizeof($names)-1]);

        $product_id = $row["product_id"];
        $option_id = $row["variant_id"];
        $quantity = $row["quantity"];
        $user_id = $row["user_id"];
        //---------------------------------------------------
        $product = $db->getRow("SELECT products.*, 
									   products.name_{$lng} AS name, 
									   (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE products.brand_id = brands.id) AS brand_name, 
									   (SELECT images.id FROM ".$products_images_table." AS images WHERE images.product_id = products.id ORDER BY pos LIMIT 1) AS product_image_id 
								FROM ".$products_table." AS products 
								WHERE id = '".$product_id."'
								"); safeCHeck($product);
		
        $variant = $db->getRow("SELECT * FROM ".$variants_table." WHERE product_id = '".$product_id."' AND option_id = '".$option_id."' AND edate = 0 AND quantity > 0"); safeCheck($variant);
        //$variantId = isset($variant["id"]) ? $variant["id"] : 0;
        $option_id = $option_id > 0 ? $option_id : 0;
		$price_discounted = getSpecialOfferPrice($product_id, $product["brand_id"], 0, $option_id);
		
        $helpers = new Helpers();
        $users = new Users();
        $user = $user_id > 0 ? $users->getRecord($user_id) : array();
        $user_group_id = isset($user["user_group_id"]) && $user["user_group_id"] > 0 ? $user["user_group_id"] : 0;
        
		if ( $variant["price"] ){
            if($price_discounted > 0.0){
                $price_use = $helpers->getDiscountedPrice($price_discounted, 1, $user_group_id);
            }else{
                $price_use = $helpers->getDiscountedPrice($variant["price"], 0, $user_group_id);
            }
			//$price_bonus_points_use = $variant["bonus_points"];
			$product_weight = $variant["weight"];
		}else{
            if($price_discounted > 0.0){
                $price_use = $helpers->getDiscountedPrice($price_discounted, 1, $user_group_id);
            }else{
                $price_use = $helpers->getDiscountedPrice($product["price"], 0, $user_group_id);
            }
			$product_weight = $product["weight"];
		}
		
		$product_price_total = $price_use * $quantity;
		$product_weight_total = $product_weight * $quantity;
        //$price_specialoffer = getSpecialOfferPrice($product_id, $product["brand_id"], 1, $variant_id);
        
//        echo "<pre>";
//        var_dump($product_id);
//        var_dump($option_id);
//        var_dump($quantity);
//        var_dump($user_id);
//        var_dump($user_group_id);
//        var_dump($product["price"]);
//        var_dump($variant["price"]);
//        var_dump($price_discounted);
//        var_dump($price_use);
//        var_dump($product_price_total);
//        echo "</pre>";
//        exit();
        
        //-----------------------------------------------------------------
        $fields = array(
                            "user_id" => $user_id > 0 ? $user_id : 0,
                            "status" => 1,
							"fast_order_id" => $row["id"],
							"ip" => $row['ip'],
							"postdate" => time(),
							"fast_order_cms_user_id" => $_SESSION["uid"],
                            "total_amount" => $product_price_total
						);
		$res = $db->autoExecute($carts_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
		$cart_id = mysqli_insert_id($db->connection);
        
		//-----------------------------------------------------------------
        $fields_products = array(
                                    "cart_id" => $cart_id,
									"product_id" => $product_id,
                                    "option_id" => $option_id,
                                    "brand_id" => $product["brand_id"],
                                    "product_image_id" => $product["product_image_id"],
                                    "product_name" => $product["name"],
                                    "product_code" => $product["code"],
                                    "product_price" => $price_use,
                                    "product_price_total" => $product_price_total,
                                    "product_weight" => $product_weight,
                                    "product_weight_total" => $product_weight_total,
                                    "brand_name" => $product["brand_name"],
                                    "quantity" => $quantity,
                                    "postdate" => $row["postdate"],
                                    "ip_address" => $row["ip"]
								);
		$res = $db->autoExecute($carts_products_table, $fields_products, DB_AUTOQUERY_INSERT); safeCheck($res);
		
        $fieldsUser = array(
							"delivery_name" => $row["name"],
							"delivery_first_name" => $delivery_first_name,
							"delivery_last_name" => $delivery_last_name,
							"billing_first_name" => $delivery_first_name,
							"billing_last_name" => $delivery_last_name,
							"billing_phone" => $row["phone"],
							"email" => $row["email"],
							//"fast_order_id" => $row["id"],
							"ip" => $row['ip'],
							"postdate" => time(),
							//"fast_order_cms_user_id" => $_SESSION["uid"],
                            "cart_id" => $cart_id,
                            //"total_amount" => $product_price_total
						);

		$res = $db->autoExecute($carts_user_table, $fieldsUser, DB_AUTOQUERY_INSERT); safeCheck($res);
		
        $res = $db->autoExecute($fast_orders_table, array("cart_created" => 1), DB_AUTOQUERY_UPDATE, " id = $fast_order_id "); safeCheck($res);
	}
	
	header("Location: carts_ae.php?act=edit&id=".$cart_id);
?>