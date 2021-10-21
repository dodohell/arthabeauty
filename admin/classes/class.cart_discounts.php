<?php
	class CartDiscounts extends Settings{
		
		public $pagination = "";
		
		public static function getRecord(int $id){
			global $db;
			global $lng;
			global $cart_discounts_table;
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$cart_discounts_table." WHERE id = $id AND edate = 0"); safeCheck($row);
			
			return $row;
		}
		
        /**
         * 
         * @global DB $db
         * @global type $cart_discounts_table
         * @param FilteredMap $params
         */
		public static function addEditRow(FilteredMap $params){
			global $db;
			global $cart_discounts_table;

            $act = $params->getString("act");
			$id = $params->getInt("id");
            $discount_type = $params->getInt("cart_discount_type");
			$fields = array(
                'name_bg'	=> $params->getString("name_bg"),
                'name_en'	=> $params->getString("name_en"),
                'name_de'	=> $params->getString("name_de"),
                'name_ru'	=> $params->getString("name_ru"),
                'name_ro'	=> $params->getString("name_ro"),
                'cart_discount_type'	=> $discount_type,
                'order_amount_from'	=> $params->getString("order_amount_from"),
                'order_amount_to'	=> $params->getString("order_amount_to"),
                'order_weight_from'	=> $params->getNumber("order_weight_from"),
                'order_weight_to'	=> $params->getNumber("order_weight_to"),
                'active'	=> $params->getInt("active")
            );
            
            //if cart_discount_type is not free delivery get the value of the delivery amount:
            $fields["value"] = $discount_type > 1 ? $params->getNumber("value") : NULL;
            
            if($act === "add") {
                shiftPos($db, $cart_discounts_table);
                $db->autoExecute($cart_discounts_table,$fields,DB_AUTOQUERY_INSERT);
                $id = mysqli_insert_id($db->connection);
            }

            if($act === "edit") {
                $db->autoExecute($cart_discounts_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);
            }
            
		}
		
		public static function getCartDiscounts($name = '', $status = NULL){
			global $db;
			global $lng;
			global $cart_discounts_table;

			$sql_where = '';
            if($status === 1){
                $sql_where .= ' AND active = "1"';
            }
            if($status === 0){
                $sql_where .= ' AND active = "0"';
            }
            if($name){
                $sql_where .= ' AND name_bg LIKE "%'.$name .'%"';
            }
            $sql = "SELECT
                        id,
                        name_{$lng} AS name,
                        cart_discount_type,
                        order_amount_from,
                        order_amount_to,
                        order_weight_from,
                        order_weight_to,
                        value,
                        pos,
                        active
                    FROM " . $cart_discounts_table . "
                    WHERE 
                        edate = 0 ". $sql_where ."
                    ORDER BY pos";
            $items = $db->getAll($sql); safeCheck($items);
            
			return $items;
		}
		
		public static function deleteRecord(int $id){
			global $db;
			global $cart_discounts_table;
			
			$fields = array(
								"edate" => time(),
								"edate_cms_user_id" => $_SESSION["uid"]
							);
			$res = $db->autoExecute($cart_discounts_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
        
        public static function isFreeDelivery(float $total_amount, float $weight): bool{
            
            $cartDiscounts = CartDiscounts::getCartDiscounts("", 1);
            
            foreach ($cartDiscounts as $k => $v) {
                if($v["cart_discount_type"] == 1){
                    $order_amount_from = (double)$v["order_amount_from"]; //>= 0.0 ? (double)$v["order_amount_from"] : 0;
                    $order_amount_to = (double)$v["order_amount_to"]; //>= 0.0 ? (double)$v["order_amount_to"] : 0;
                    $order_weight_from = (double)$v["order_weight_from"]; //>= 0.0 ? (double)$v["order_weight_from"] : 0;
                    $order_weight_to = (double)$v["order_weight_to"]; //>= 0.0 ? (double)$v["order_weight_to"] : 0;
                    if( $total_amount >= $order_amount_from && $order_amount_to = $order_amount_to > 0.0 ? $total_amount <= $order_amount_to : true ){
                        if ( $weight >= $order_weight_from && $order_weight_to = $order_weight_to > 0.0 ? $weight <= $order_weight_to : true ){
                            return TRUE;
                        }
                    }
                }
            }
            
            return FALSE;
        }
        
        /**
         * if delivery type is not some of the Econt types we use custom rules from cart_discounts
         * 
         * @param float $total_amount
         * @param float $weight
         * @return float delivery amount
         */
        public static function getCustomDeliveryAmount(float $total_amount, float $weight): float{
            
            $cartDiscounts = CartDiscounts::getCartDiscounts("", 1);
            $cartDiscounts = $cartDiscounts ? $cartDiscounts : [];
            
            foreach ($cartDiscounts as $k => $v) {
                if($v["cart_discount_type"] == 2){
                    $order_amount_from = (double)$v["order_amount_from"]; //>= 0.0 ? (double)$v["order_amount_from"] : 0;
                    $order_amount_to = (double)$v["order_amount_to"]; //>= 0.0 ? (double)$v["order_amount_to"] : 0;
                    $order_weight_from = (double)$v["order_weight_from"]; //>= 0.0 ? (double)$v["order_weight_from"] : 0;
                    $order_weight_to = (double)$v["order_weight_to"]; //>= 0.0 ? (double)$v["order_weight_to"] : 0;
                    $value = (double)$v["value"]; //>= 0.0 ? (double)$v["order_weight_to"] : 0;
                    if( $total_amount >= $order_amount_from && $order_amount_to = $order_amount_to > 0.0 ? $total_amount <= $order_amount_to : true ){
                        if ( $weight >= $order_weight_from && $order_weight_to = $order_weight_to > 0.0 ? $weight <= $order_weight_to : true ){
                            return $value;
                        }
                    }
                }
            }
            
            return 0.0;
        }
		
        
        
	}