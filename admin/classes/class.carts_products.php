<?php
	class CartsProducts extends Settings{
		
        //duplicate with Carts->deleteCartProductRecord(). It must be changed with this one.
        public static function getRecord(int $id = 0){
            global $db;
			global $carts_products_table;
            
            $row = $db->getRow("SELECT * FROM ".$carts_products_table." WHERE id = '".$id."'"); safeCheck($row);
            
            return $row;
        }
        
        //duplicate with Carts->deleteCartProductRecord(). It must be changed with this one.
        public static function deleteRecord(int $id){
			global $db;
			global $carts_products_table;
			
			$fields = array(
								"edate" => time(),
								"edate_cms_user_id" => $_SESSION["uid"]
							);
			$res = $db->autoExecute($carts_products_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
        
        //duplicate with Carts->deleteCartProductByCartIdAndProductId(). It must be changed with this one.
        public static function deleteRecordByCartIdAndProductId(int $cart_id, int $product_id){
			global $db;
			global $carts_products_table;
			
			$fields = array(
								"edate" => time(),
								"edate_cms_user_id" => $_SESSION["uid"]
							);
			$res = $db->autoExecute($carts_products_table, $fields, DB_AUTOQUERY_UPDATE, " cart_id = $cart_id AND product_id = $product_id "); safeCheck($res);
			
			return $res;
		}
        
        public static function getCartProductsSimple(int $cart_id) {
            global $db;
            global $carts_products_table;
            
            $res = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE cart_id = '".$cart_id."' AND edate = 0"); safeCheck($res);
        
            return $res;
        }
        
        //duplicate with Carts->getCartProducts(). It must be changed with this one.
        public static function getCartProducts(int $cart_id) {
            global $db;
            global $lng;
            global $products_table;
            global $carts_products_table;
            global $options_table;
            global $option_groups_table;
            
            $cart = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE edate = 0 AND cart_id = '".$cart_id."'"); safeCheck($cart);
            $total_weight = 0;
            $total_postage_points = 0;
            foreach($cart as $k => $v){
                $product = $db->getRow("SELECT id, name_{$lng} AS name FROM ".$products_table." AS products WHERE edate = 0 AND id = '".$v["product_id"]."'"); safeCheck($product);
                $v["product"] = $product;
                $total_weight += $product["product_weight"]*$v["quantity"];
                $total_postage_points += $product["postage_points"]*$v["quantity"];
                $v["cart_price"] = number_format(($v["product_price"]+$v["choices_price"])*$v["quantity"],2);
                $vat_norounding = $v["cart_price"]*5/6;
                $v["cart_price_clear"] = number_format((($v["cart_price"]*5)/6), 2);
                $v["cart_price_w_vat"] = number_format($vat_norounding+($vat_norounding*$vat["vat_percent"])/100, 2);
                $v["cart_price_w_vat_total"] = number_format($v["cart_price_w_vat"]*$v["quantity"],2);
                
                $option = $db->getRow("SELECT *, option_text AS name, (SELECT og.name_{$lng} FROM ".$option_groups_table." AS og WHERE og.id = options.option_group_id) AS option_group_name FROM ".$options_table." AS options	WHERE options.id = '".$v["variant_id"]."'"); safeCheck($option);
                $v["option"] = $option;
                
                $tmp_total_amount = 0;
                if ( $v["product_price_discount"] > 0.0 ){
                    $subtotal_amount += $v["quantity"] * $v["product_price_discount"];
                    $tmp_total_amount = $v["quantity"] * $v["product_price_discount"];
                    $v["tmp_product_price"] = $v["product_price_discount"];
                }else{
                    $subtotal_amount += $v["quantity"] * $v["product_price"];
                    $tmp_total_amount = $v["quantity"] * $v["product_price"];
                    $v["tmp_product_price"] = $v["product_price"];
                }
                
                $v["tmp_total_amount"] = $tmp_total_amount;
                
                $cart[$k] = $v;
            }
            return $cart;
        }

	}
